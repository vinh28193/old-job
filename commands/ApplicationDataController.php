<?php
namespace app\commands;

use yii;
use app\models\manage\ApplicationMaster;
use app\models\manage\searchkey\Pref;
use app\models\manage\ApplicationStatus;
use app\models\manage\Occupation;
use app\models\JobMasterDisp;
use app\models\manage\ApplicationResponseLog;
use app\models\manage\AdminMaster;
use app\models\manage\ClientMaster;
use yii\helpers\ArrayHelper;

/**
 * Class DataController
 * @package app\commands
 */
class ApplicationDataController extends BaseDataController
{
    protected $tableName = 'application_master';

    private $_classNames;

    private $_jobId2AdminId;

    /**
     * 初期化処理
     */
    public function init()
    {
        $this->_classNames = [
            ApplicationMaster::className(),
            ApplicationResponseLog::className(),
        ];

        foreach ($this->_classNames as $className) {
            /** @var yii\db\ActiveRecord $className */
            $tableName = $className::tableName();
            // 初期idをセット
            $this->currentId[$tableName] = $className::find()->max('id') + 1;
        }
    }

    /**
     * 応募者情報（application_masterとapplication_response_log）を登録する
     * @param $count
     * @return mixed|void
     */
    public function insert($count)
    {
        // データ作成
        $this->makeValues($count);

        // データ挿入
        $this->save();
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
        }
    }

    /**
     * 有効な原稿データに紐づく、応募者データを作成する
     * @param $count
     * @return array
     */
    private function makeValues($count)
    {
        // rowsを初期化
        $this->rows = [];

        // 管理IDと仕事IDの連想配列をキャッシュ
        $this->cacheJobId2AdminId();

        // データ挿入で使うid取得
        $this->ids['pref'] = Pref::find()->select('id')->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['application_status'] = ApplicationStatus::find()->select('id')->where(['tenant_id' => $this->tenantId])->column();
        $this->ids['occupation'] = Occupation::find()->select('id')->where(['tenant_id' => $this->tenantId])->column();

        for ($i = 1; $i <= $count; $i++) {
            $this->currentId['job_master'] = $this->inc(array_keys($this->_jobId2AdminId), $i);
            $this->currentId['application_status'] = $this->inc($this->ids['application_status'], $i);
            $this->setApplicationMasterRow();
            // 関連テーブルのセット
            $this->setApplicationResponseLogRow();
            $this->currentId['application_master']++;
            $this->currentId['application_response_log']++;
        }
    }

    /**
     * application_masterのrowを作成してpropertyにセットする
     */
    private function setApplicationMasterRow()
    {
        // 作業テーブルセット
        $this->currentTable = 'application_master';

        $this->rows['application_master'][] = [
            $this->id(),    // id
            $this->tenantId,// tenant_id
            $this->id(), // application_no
            $this->currentId['job_master'],    // job_master_id
            $this->data('name_sei'),    // name_sei
            $this->data('name_mei'),    // name_mei
            $this->data('kana_sei'),    // kana_sei
            $this->data('kana_mei'),    // kana_mei
            rand(1, 2),    // sex
            '1980-01-01',    // birth_date
            $this->incId('pref'),    // pref_id
            $this->data('address'),    // address
            $this->telNo(),    // tel_no
            $this->mail(),    // mail_address
            $this->incId('occupation'),    // occupation_id
            $this->data('self_pr'),    // self_pr
            $this->timeStamp(),    // created_at
            $this->data('option100'),    // option100
            $this->data('option101'),    // option101
            $this->data('option102'),    // option102
            $this->data('option103'),    // option103
            $this->data('option104'),    // option104
            $this->data('option105'),    // option105
            $this->data('option106'),    // option106
            $this->data('option107'),    // option107
            $this->data('option108'),    // option108
            $this->data('option109'),    // option109
            $this->currentId['application_status'],    // application_status_id
            rand(1, 2),    // carrier_type
            $this->data('application_memo'),    // application_memo
        ];
    }

    /**
     * application_response_logのrowを作成してpropertyにセットする
     */
    private function setApplicationResponseLogRow()
    {
        // 作業テーブルセット
        $this->currentTable = 'application_response_log';

        $this->rows['application_response_log'][] = [
            $this->id(),    // id
            $this->tenantId,// tenant_id
            $this->currentId['application_master'],    //	application_id
            $this->_jobId2AdminId[$this->currentId['job_master']],    //	admin_id
            $this->currentId['application_status'],    //	application_status_id
            null,    //	mail_send_id
            $this->timeStamp(),    //	created_at
        ];
    }

    /**
     * 有効な原稿のIDと、原稿に紐付く掲載企業に属する管理者IDを1つ取得し、連想配列にしてpropertyにセットする
     */
    private function cacheJobId2AdminId()
    {
        $this->_jobId2AdminId = [];

        // 掲載企業に紐づく管理者IDを取得するためにサブクエリを使用
        $subQuery = AdminMaster::find()
            ->select([
                'client_master_id',
                'admin_master_id' => 'MIN(id)',
            ])
            ->distinct(true)
            ->alias('CA')
            ->where(['not', ['client_master_id' => null]])
            ->groupBy('client_master_id');

        $query = JobMasterDisp::find()
            ->select([
                'job_master_id' => JobMasterDisp::tableName() . '.id',
                'admin_master_id' => 'CA.admin_master_id',
            ])
            ->innerJoin(['CA' => $subQuery], 'CA.`client_master_id` = `' . ClientMaster::tableName() . '`.`id`')
            ->where([JobMasterDisp::tableName() . '.tenant_id' => $this->tenantId])
            ->active(); //有効な原稿のみ取得
        $queryArr = $query->createCommand()->queryAll();

        $this->_jobId2AdminId = ArrayHelper::map(
            $queryArr,
            'job_master_id',
            'admin_master_id'
        );
    }
}
