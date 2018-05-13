<?php
namespace app\commands;

use app\models\manage\ClientCharge;
use app\models\manage\JobMaster;
use app\models\manage\MediaUpload;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\JobDist;
use app\models\manage\searchkey\JobPref;
use app\models\manage\searchkey\JobSearchkeyItem1;
use app\models\manage\searchkey\JobSearchkeyItem2;
use app\models\manage\searchkey\JobSearchkeyItem3;
use app\models\manage\searchkey\JobSearchkeyItem4;
use app\models\manage\searchkey\JobSearchkeyItem5;
use app\models\manage\searchkey\JobSearchkeyItem11;
use app\models\manage\searchkey\JobSearchkeyItem12;
use app\models\manage\searchkey\JobSearchkeyItem13;
use app\models\manage\searchkey\JobSearchkeyItem14;
use app\models\manage\searchkey\JobSearchkeyItem15;
use app\models\manage\searchkey\JobStationInfo;
use app\models\manage\searchkey\JobType;
use app\models\manage\searchkey\JobTypeSmall;
use app\models\manage\searchkey\JobWage;
use app\models\manage\searchkey\PrefDistMaster;
use app\models\manage\searchkey\SearchkeyItem1;
use app\models\manage\searchkey\SearchkeyItem11;
use app\models\manage\searchkey\SearchkeyItem12;
use app\models\manage\searchkey\SearchkeyItem13;
use app\models\manage\searchkey\SearchkeyItem14;
use app\models\manage\searchkey\SearchkeyItem15;
use app\models\manage\searchkey\SearchkeyItem2;
use app\models\manage\searchkey\SearchkeyItem3;
use app\models\manage\searchkey\SearchkeyItem4;
use app\models\manage\searchkey\SearchkeyItem5;
use app\models\manage\searchkey\Station;
use app\models\manage\searchkey\WageItem;
use yii;
use yii\console\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class DataController
 * @package app\commands
 */
class JobDataController extends BaseDataController
{
    protected $tableName = 'job_master';

    protected $limit = 50000;

    private $_classNames;

    private $_clientArray;
    private $_distId2PrefId;
    private $_wageArray;

    /**
     * 初期化処理
     */
    public function init()
    {
        $this->_classNames = [
            JobMaster::className(),
            JobDist::className(),
            JobPref::className(),
            JobWage::className(),
            JobStationInfo::className(),
            JobType::className(),
            JobSearchkeyItem1::className(),
            JobSearchkeyItem2::className(),
            JobSearchkeyItem3::className(),
            JobSearchkeyItem4::className(),
            JobSearchkeyItem5::className(),
            JobSearchkeyItem11::className(),
            JobSearchkeyItem12::className(),
            JobSearchkeyItem13::className(),
            JobSearchkeyItem14::className(),
            JobSearchkeyItem15::className(),
        ];

        foreach ($this->_classNames as $className) {
            /** @var yii\db\ActiveRecord $className */
            $tableName = $className::tableName();
            // 初期idをセット
            $this->currentId[$tableName] = $className::find()->max('id') + 1;
            // これから登録するjobに紐づいている中間テーブルレコードを削除
            if ($tableName != 'job_master') {
                $className::deleteAll(['>=', 'job_master_id', $this->currentId['job_master']]);
            }
        }
    }

    /**
     * 仕事情報を登録する
     * 半分は有効状態で有効な掲載企業に紐づけられる
     * もう半分は無効状態で有効もしくは無効な掲載企業に紐づけられる
     * @param $count
     * @return mixed|void
     */
    public function insert($count)
    {
        ini_set('memory_limit', '1536M');
        // 有効無効関係ないid配列をキャッシュ
        $this->cacheWageArray();
        $this->ids['media_upload'] = MediaUpload::find()->select('id')->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['station'] = Station::find()->select(['station_no'])->column();
        $this->ids['job_type_small'] = JobTypeSmall::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['searchkey_item1'] = SearchkeyItem1::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['searchkey_item2'] = SearchkeyItem2::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['searchkey_item3'] = SearchkeyItem3::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['searchkey_item4'] = SearchkeyItem4::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['searchkey_item5'] = SearchkeyItem5::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['searchkey_item11'] = SearchkeyItem11::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['searchkey_item12'] = SearchkeyItem12::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['searchkey_item13'] = SearchkeyItem13::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['searchkey_item14'] = SearchkeyItem14::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['searchkey_item15'] = SearchkeyItem15::find()->select(['id'])->where(['tenant_id' => $this->tenantId])->column();

        // 有効と無効半分ずつに分ける
        $halfCount = $count / 2;
        $once = 3000;

        // 有効データ作成・挿入
        $this->valid = 1;
        $this->cacheClientArray();
        $this->cacheDistId2PrefId();
        $quotient = floor($halfCount / $once);
        $reminder = $halfCount % $once;
        for ($i = 1; $i <= $quotient; $i++) {
            $this->makeValues($once);
            $this->save();
        }
        if ($reminder != 0) {
            $this->makeValues($reminder);
            $this->save();
        }

        // 無効データ作成・挿入
        $this->valid = 0;
        $this->cacheClientArray();
        $this->cacheDistId2PrefId();
        $quotient = floor($halfCount / $once);
        $reminder = $halfCount % $once;
        for ($i = 1; $i <= $quotient; $i++) {
            $this->makeValues($once);
            $this->save();
        }
        if ($reminder != 0) {
            $this->makeValues($reminder);
            $this->save();
        }
    }

    /**
     * saveする
     */
    private function save()
    {
        foreach ($this->_classNames as $className) {
            /** @var yii\db\ActiveRecord $className */
            $tableName = $className::tableName();
            Yii::$app->db->createCommand()->batchInsert($className::tableName(), $className::getTableSchema()->columnNames, $this->rows[$tableName])->execute();
            unset($this->rows[$tableName]);
        }
    }

    /**
     * 有効もしくは無効な掲載企業データを作成する
     * @param $count
     * @return array
     */
    private function makeValues($count)
    {
        // rowsを初期化
        $this->rows = [];

        for ($i = 1; $i <= $count; $i++) {
            $this->setJobMasterRow();
            $this->setJobDistAndPrefRows();
            $this->setJobWageRows();
            $this->setJobStationInfo();
            $this->setJobSearchKeyItems(JobType::tableName(), 5);
            for ($ii = 1; $ii <= 5; $ii++) {
                $this->setJobSearchKeyItems('job_searchkey_item' . $ii, 3);
                $this->setJobSearchKeyItems('job_searchkey_item1' . $ii, 3);
            }
            $this->currentId['job_master']++;
        }
    }

    /**
     * 有効もしくは無効なjob_masterのrowを作成してpropertyにセットする
     */
    private function setJobMasterRow()
    {
        // 作業テーブルセット
        $this->currentTable = 'job_master';
        // 開始日終了日を取得
        $date = $this->startAndEndDate();
        // 掲載企業とプランの組み合わせを取得
        $clientAndPlanId = $this->incClientAndPlanId();
        // rowをセット
        $this->rows['job_master'][] = [
            $this->id(), // id
            $this->tenantId,// tenant_id
            $this->id(), // job_no
            $clientAndPlanId['client'], //client_master_id
            $this->data('corp_name_disp'),//corp_name_disp
            $this->data('job_pr'), // job_pr
            $this->data('main_copy'), // main_copy
            $this->data('job_comment'), // job_comment
            $this->data('job_type_text'), // job_type_text
            $this->data('work_place'), // work_place
            $this->data('station'), // station
            $this->data('transport'), // transport
            $this->data('wage_text'), // wage_text
            $this->data('requirement'), // requirement
            $this->data('conditions'), // conditions
            $this->data('holidays'), // holidays
            $this->data('work_period'), // work_period
            $this->data('work_time_text'), // work_time_text
            $this->data('application'), // application
            $this->telNo(), // application_tel_1
            $this->telNo(), // application_tel_2
            $this->mail(), // application_mail
            $this->data('application_place'), // application_place
            $this->data('application_staff_name'), // application_staff_name
            '', // agent_name
            $date['start'], // disp_start_date
            $date['end'], // disp_end_date
            $this->timeStamp(), // created_at
            $this->valid, // valid_chk
            $this->data('job_search_number'), // job_search_number
            '', // job_pict_text_3  あまり使われてないので空文字を入れる
            '', // job_pict_text_4  あまり使われてないので空文字を入れる
            $this->url(), // map_url
            $this->data('mail_body'), // mail_body
            $this->timeStamp(), // updated_at
            '', // job_pict_text_5  あまり使われてないので空文字を入れる
            $this->data('main_copy2'), // main_copy2
            $this->data('job_pr2'), // job_pr2
            $this->data('option100'), // option100
            $this->data('option101'), // option101
            $this->data('option102'), // option102
            $this->data('option103'), // option103
            $this->data('option104'), // option104
            $this->data('option105'), // option105
            $this->data('option106'), // option106
            $this->data('option107'), // option107
            $this->data('option108'), // option108
            $this->data('option109'), // option109
            rand(1, 2147483647), // import_site_job_id
            $clientAndPlanId['plan'], // client_charge_plan_id
            6, // job_review_status_id
            1, // disp_type_sort
            $this->incId('media_upload'), // media_upload_id_1
            $this->incId('media_upload'), // media_upload_id_2
            $this->incId('media_upload'), // media_upload_id_3
            $this->incId('media_upload'), // media_upload_id_4
            $this->incId('media_upload'), // media_upload_id_5
        ];
    }

    /**
     * キャッシュからjob_distのrowを生成してセットする
     */
    private function setJobDistAndPrefRows()
    {
        // 異なるdistのid3つと、重複を削除した対応するprefのidを取得
        if (count($this->_distId2PrefId) < 3) {
            throw new Exception("number of valid district in tenant{$this->tenantId} is not enough.");
        }
        for ($distIds = []; count($distIds) < 3; $distIds = array_unique($distIds)) {
            $distIds[] = array_rand($this->_distId2PrefId);
        }
        foreach ($distIds as $distId) {
            $prefIds[] = $this->_distId2PrefId[$distId];
        }
        /** @noinspection PhpUndefinedVariableInspection */
        $prefIds = array_unique($prefIds);

        // distのrowを生成してセット
        $this->currentTable = 'job_dist';
        foreach ($distIds as $distId) {
            $this->setJunctionRow('job_dist', $distId);
        }

        // prefのrowを生成してセット
        $this->currentTable = 'job_pref';
        foreach ($prefIds as $prefId) {
            $this->setJunctionRow('job_pref', $prefId);
        }
    }

    /**
     * キャッシュからjob_wageのrowを生成してセットする
     */
    private function setJobWageRows()
    {
        $this->currentTable = 'job_wage';
        if (!$this->_wageArray) {
            throw new Exception("wage search key item in tenant{$this->tenantId} is not exists.");
        }
        // 順番にcategoryを選択
        $itemIds = $this->inc($this->_wageArray, $this->currentId['job_master']);
        // ランダムにitemを選択
        $itemKey = array_rand($itemIds);
        reset($itemIds);

        // 選択したitemを入れる
        $this->setJunctionRow('job_wage', $itemIds[$itemKey]);
    }

    /**
     * キャッシュからjob_wageのrowを生成してセットする
     */
    private function setJobStationInfo()
    {
        $this->currentTable = 'job_station_info';
        for ($i = 1; $i <= 3; $i++) {
            $this->rows['job_station_info'][] = [
                $this->id(),                        // id
                $this->tenantId,                    // tenant_id
                $this->currentId['job_master'],     // job_master_id
                $this->rand($this->ids['station']), // station_id
                rand(0, 1),                         // transport_type
                rand(1, 30),                        // transport_time
            ];
            $this->currentId['job_station_info']++;
        }
    }

    /**
     * キャッシュからjob_wageのrowを生成してセットする
     * @param $tableName
     * @param $num
     * @throws Exception
     */
    private function setJobSearchKeyItems($tableName, $num)
    {
        if ($tableName == 'job_type') {
            $itemTableName = 'job_type_small';
        } else {
            $itemTableName = str_replace('job_', '', $tableName);
        }
        $this->currentTable = $tableName;
        if ($num > count($this->ids[$itemTableName])) {
            throw new Exception("number of $itemTableName records in tenant$this->tenantId must be over $num");
        }
        for ($itemIds = []; count($itemIds) < $num; $itemIds = array_unique($itemIds)) {
            $itemIds[] = $this->rand($this->ids[$itemTableName]);
        }
        foreach ($itemIds as $itemId) {
            $this->setJunctionRow($tableName, $itemId);
        }
    }

    /**
     * 検索キ中間テーブルのrowをセットする
     * @param $tableName
     * @param $itemId
     */
    private function setJunctionRow($tableName, $itemId)
    {
        $this->rows[$tableName][] = [
            $this->id(), // id
            $this->tenantId, // tenant_id
            $this->currentId['job_master'], // job_master_id
            $itemId, // item_id
        ];
        $this->currentId[$tableName]++;
    }

    /**
     * 掲載企業とプランの組み合わせを順番に取得する
     * @return array
     * @throws Exception
     */
    private function incClientAndPlanId()
    {
        if ($this->_clientArray == []) {
            throw new Exception("in tenant{$this->tenantId}, number of assignable clients is not enough.");
        }
        // job_master.idを元に順番に掲載企業とプランの組み合わせを取得する
        $i = ($this->currentId['job_master'] - 1) % count($this->_clientArray);
        $clientId = key(array_slice($this->_clientArray, $i, 1, true));
        $planId = key($this->_clientArray[$clientId]);
        $limitNum = $this->_clientArray[$clientId][$planId];

        if ($limitNum == 1) {
            unset($this->_clientArray[$clientId][$planId]);
            if (!$this->_clientArray[$clientId]) {
                unset($this->_clientArray[$clientId]);
            }
        }
        return ['client' => $clientId, 'plan' => $planId];
    }

    /**
     * [
     *     掲載企業id => [
     *         プランid => 残り枠数,
     *         プランid => 残り枠数,
     *         ],
     *     掲載企業id => [
     *         プランid => 残り枠数,
     *         プランid => 残り枠数,
     *         ],
     * ]
     * という形式の配列をsetする
     */
    private function cacheClientArray()
    {
        $this->_clientArray = [];
        // 現在登録されている求人原稿から掲載企業、プラン毎に使われている枠数を取得
        $jobMasterCondition = ['tenant_id' => $this->tenantId];
        $clientChargeCondition = ['client_charge.tenant_id' => $this->tenantId];
        if ($this->valid) {
            $jobMasterCondition = array_merge($jobMasterCondition, ['valid_chk' => $this->valid]);
            $clientChargeCondition = array_merge($jobMasterCondition, ['client_master.valid_chk' => $this->valid]);
        }

        $jobCount = ArrayHelper::map(
            JobMaster::find()
                ->select(['client_master_id', 'client_charge_plan_id', 'count(*) AS limit_num'])
                ->where($jobMasterCondition)
                ->groupBy(['client_master_id', 'client_charge_plan_id'])
                ->asArray()->all(),
            'client_charge_plan_id',
            'limit_num',
            'client_master_id'
        );

        // 掲載企業、プラン毎の枠上限数を取得
        $limit = ArrayHelper::map(
            ClientCharge::find()->joinWith('clientMaster')
                ->select(['client_charge.client_master_id', 'client_charge.client_charge_plan_id', 'client_charge.limit_num'])
                ->where($clientChargeCondition)
                ->asArray()->all(),
            'client_charge_plan_id',
            'limit_num',
            'client_master_id'
        );

        // 掲載企業とプラン毎の割り当て可能な枠数を持った配列を生成
        foreach ($limit as $clientId => $plan) {
            foreach ($plan as $planId => $limitNum) {
                $assignmentNum = ArrayHelper::getValue($jobCount, $clientId . '.' . $planId);
                if ($limitNum > $assignmentNum) {
                    $this->_clientArray[$clientId][$planId] = $limitNum - $assignmentNum;
                } elseif (!$limitNum) {
                    $this->_clientArray[$clientId][$planId] = 0;
                }
            }
        }
    }

    /**
     * 有効、もしくは全てのdistのidをkeyに持ち、valueにpref_noを持つ配列を生成してpropertyにセットする
     */
    private function cacheDistId2PrefId()
    {
        $this->_distId2PrefId = [];
        if ($this->valid) {
            $query = PrefDistMaster::find()->joinWith(['prefDist.distCd.pref.area'])->select(['dist.id', 'pref.id as pref_id'])->where([
                'and',
                ['area.valid_chk' => $this->valid],
                ['area.tenant_id' => $this->tenantId],
                ['pref.valid_chk' => $this->valid],
                ['pref.tenant_id' => $this->tenantId],
                ['pref_dist.tenant_id' => $this->tenantId],
                ['pref_dist_master.valid_chk' => $this->valid],
                ['pref_dist_master.tenant_id' => $this->tenantId],
            ]);
        } else {
            $query = Dist::find()->joinWith('pref')->select(['dist.id', 'pref.id as pref_id', 'pref.pref_no']);
        }

        $this->_distId2PrefId = ArrayHelper::map(
            $query->asArray()->all(),
            'id',
            'pref_id'
        );
    }

    /**
     * wageItemのidをwageCategoryのidでindexした配列をpropertyにキャッシュする
     */
    private function cacheWageArray()
    {
        $this->_wageArray = [];
        foreach (WageItem::find()->select(['id', 'wage_category_id'])->where(['tenant_id' => $this->tenantId])->orderBy('wage_item_name')->asArray()->all() as $item) {
            $this->_wageArray[$item['wage_category_id']][] = $item['id'];
        }
    }
}
