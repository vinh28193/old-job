<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/09/09
 * Time: 11:22
 */

namespace app\modules\manage\components;

use app\models\manage\DispType;
use yii;
use yii\db\ActiveRecord;
use app\common\csv\CsvLoader;
use app\modules\manage\models\JobCsvRegister;
use app\models\manage\ClientChargePlan;
use yii\helpers\ArrayHelper;
use app\models\manage\ClientCharge;
use app\models\manage\ClientMaster;
use app\models\manage\JobMaster;
use app\models\manage\MediaUpload;
use app\models\manage\searchkey\Station;
use app\common\csv\CsvWorker;
use app\common\Helper\JmUtils;

class JobCsvLoader extends CsvLoader
{
    /**
     * @var array
     * 駅コードをバリューにした配列
     * JobCsvRegister::stationCodeCheckで負荷対策のため、一時保存している
     */
    public $stationNos = [];

    /**
     * 掲載企業のどのプランにどの求人原稿が割り当てられているか
     * $plans[client_master_id][client_charge_plan_id] == [求人原稿Nos]
     * @var array
     */
    public $plans;

    /**
     * 登録する掲載企業と料金プランのセット。登録しようとした掲載企業・料金プランのみ、登録上限数のバリデーションを行うため
     * @var array
     */
    public $changedPlans;
    /**
     * media_uploadテーブルのレコード情報
     * $fileNames2Ids[file_name]
     * @var array
     */
    public $fileNames2Ids;
    public $planNos2Ids;
    public $planNos2DispNos;
    public $planLimits;
    public $clientNos2Ids;
    public $jobNos2Ids;
    public $maxJobNo;
    public $newJobNo;
    public $jobIds2Plans;

    /**
     * CSVのフォーマットと、順列が等しいAttribute配列を返す
     * @return string[]
     */
    public function getCsvAttributes()
    {
        return JobCsvRegister::csvAttributes();
    }

    /**
     * 負荷対策のため、モデル側にDBを参照するバリデーションに必要な値をロード（バリデーション時）
     * @param CsvWorker $worker
     */
    public function beforeCsvLoad($worker = null)
    {
        parent::beforeCsvLoad();
        $this->jobCsvRegisterCache();
    }

    /**
     * 負荷対策のため、モデル側にDBを参照するバリデーションに必要な値をロード（バリデーション時）
     * @param CsvWorker $worker
     * @return bool エラーがある場合true
     */
    public function afterCsvLoad($worker = null)
    {
        parent::afterCsvLoad();

        $changedPlans = $this->changedPlans;
        $planIds2Nos = array_flip($this->planNos2Ids);
        $clientIds2Nos = array_flip($this->clientNos2Ids);

        $errors = [];
        foreach ($changedPlans as $client => $plans) {
            $plans = array_unique($plans);  //登録しようとしたプランが同じものが含まれうるため
            foreach ($plans as $plan) {
                $limit = ArrayHelper::getValue($this->planLimits, $client . '.' . $plan);
                $planCount = ArrayHelper::getValue($this->plans, $client . '.' . $plan);
                if ($limit !== null && $planCount !== null && $planCount > $limit) {
                    $errors[] = ['planNo' => $planIds2Nos[$plan], 'clientNo' => $clientIds2Nos[$client]];
                }
            }
        }

        if (!JmUtils::isEmpty($errors)) {
            $clientChargePlan = new ClientChargePlan();//やむを得ず
            $clientMaster = new ClientMaster();//やむを得ず
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = Yii::t(
                    'app',
                    '下記の掲載企業に割り当てられた料金プランの上限数を超過しております。<br>{PLAN_LABEL}：{PLAN_NO}<br>{CLIENT_LABEL}：{CLIENT_NO}',
                    [
                        'PLAN_LABEL' => $clientChargePlan->getAttributeLabel('client_charge_plan_no'),
                        'PLAN_NO' => $error['planNo'],
                        'CLIENT_LABEL' => $clientMaster->getAttributeLabel('client_no'),
                        'CLIENT_NO' => $error['clientNo'],
                    ]
                );
            }
            $worker->sendError($worker->sendId, json_encode(['error' => $errorMessages]));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 負荷対策のため、モデル側にDBを参照するバリデーションに必要な値をロード（セーブ時）
     * @param CsvWorker $worker
     * @return string[]
     */
    public function beforeCsvSave($worker = null)
    {
        parent::beforeCsvSave();
        $this->jobCsvRegisterCache();
    }

    /**
     * 行のセルを前処理に掛ける
     * @param $row
     * @return \string[]
     */
    public static function convertRow($row)
    {
        return $row;
    }

    /**
     * @param string[] $line CSVの行
     * @param string $encodeFrom エンコード元の文字コード
     * @param string $encodeTo エンコード先の文字コード
     * @return ActiveRecord モデルのインスタンス
     */
    public function getInstance($line, $encodeFrom = 'ASCII,JIS,UTF-8,EUC-JP,SJIS-win', $encodeTo = 'UTF-8')
    {
        /** @var JobCsvRegister $model */
        $model = new JobCsvRegister();
        $model->loader = $this;
        $jobNoNum = ArrayHelper::getValue(array_flip($this->getCsvAttributes()), 'job_no');
        $jobNo =  ArrayHelper::getValue($line, $jobNoNum);
        // 既に求人情報が存在している場合は、主キーの値を追加し、既存原稿扱いにしておく。
        if (isset($jobNo) && isset($this->jobNos2Ids[$jobNo])) {
            //複合キーなので、idとtenant_idを入れておく（入力しておかないとupdateされない）
            $model->setAttribute('id', $this->jobNos2Ids[$jobNo]);
            $model->setAttribute('tenant_id', Yii::$app->tenant->id);
            $model->setIsNewRecord(false);
        }
        self::loadFromCsv($model, $line, $encodeFrom, $encodeTo);
        return $model;
    }

    /**
     * JobCsvRegisterへのDBデータをロード
     */
    private function jobCsvRegisterCache()
    {
        // 仕事情報を一時保存
        $jobs = JobMaster::find()->select(['job_no', 'id', 'client_charge_plan_id', 'client_master_id'])->asArray()->all();
        foreach ($jobs as $job) {
            $this->jobNos2Ids[$job['job_no']] = $job['id'];
            if (isset($this->plans[$job['client_master_id']][$job['client_charge_plan_id']])) {
                $this->plans[$job['client_master_id']][$job['client_charge_plan_id']] += 1;
            } else {
                $this->plans[$job['client_master_id']][$job['client_charge_plan_id']] = 1;
            }
            $this->jobIds2Plans[$job['id']] = ['client_master_id' => $job['client_master_id'], 'client_charge_plan_id' => $job['client_charge_plan_id']];
        }

        $this->maxJobNo = end($jobs)['job_no'];
        unset($jobs);
        $this->newJobNo = $this->maxJobNo + 1;
        // 掲載企業情報を一時保存
        $this->clientNos2Ids = ArrayHelper::map(ClientMaster::find()->select(['client_no', 'id'])->asArray()->all(), 'client_no', 'id');
        // 企業ごとのプラン割り当て情報を一時保存
        $charges = ClientCharge::find()->select(['client_charge_plan_id', 'client_master_id', 'limit_num'])->asArray()->all();
        foreach ($charges as $charge) {
            $this->planLimits[$charge['client_master_id']][$charge['client_charge_plan_id']] = $charge['limit_num'];
        }
        unset($charges);
        // プラン情報一時保存
        $planInfo = ClientChargePlan::find()
            ->joinWith('dispType')
            ->select([
                ClientChargePlan::tableName() . '.id',
                ClientChargePlan::tableName() . '.client_charge_plan_no',
                ClientChargePlan::tableName() . '.disp_type_id',
                DispType::tableName() . '.disp_type_no',
            ])->where([
                ClientChargePlan::tableName() . '.valid_chk' => ClientChargePlan::VALID,
                DispType::tableName() . '.valid_chk' => ClientChargePlan::VALID,
            ])->asArray()->all();
        $this->planNos2Ids = ArrayHelper::map($planInfo, 'client_charge_plan_no', 'id');
        $this->planNos2DispNos = ArrayHelper::map($planInfo, 'client_charge_plan_no', 'disp_type_no');
        // 画像情報一時保存
        $this->fileNames2Ids = ArrayHelper::map(MediaUpload::find()->select(['disp_file_name', 'id'])->asArray()->all(), 'disp_file_name', 'id');
        // 駅情報
        $this->stationNos = array_flip(Station::find()->select(['station_no'])->groupBy(['station_no'])->column());
    }
}
