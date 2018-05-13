<?php
namespace app\commands;

use app\models\JobMasterDisp;
use app\models\manage\ApplicationMaster;
use app\models\manage\AccessLog;
use app\models\manage\JobMaster;
use app\models\manage\searchkey\Area;
use Faker\Factory;
use Yii;

/**
 * Class DataController
 * @package app\commands
 */
class LogDataController extends BaseDataController
{
    /** 負荷testで使うのはhttpなため、httpにしている */
    const SCHEME = 'http://';

    protected $tableName = 'access_log';
    private $_siteUrl;
    private $_jobIdsAndNos;
    private $_applicationIdsAndNos;

    public function setUp()
    {
        // idの最大値を取得
        $this->currentId['access_log'] = AccessLog::find()->max('id') + 1;
        // URL候補をキャッシュ
        $this->setUpUrls();

        // 仕事情報をキャッシュ
        $this->_jobIdsAndNos = JobMasterDisp::find()->select([
            JobMaster::tableName() . '.id',
            JobMaster::tableName() . '.job_no',
            JobMaster::tableName() . '.client_master_id',
            JobMaster::tableName() . '.client_charge_plan_id',
        ])->where([JobMaster::tableName() . '.tenant_id' => $this->tenantId])->active()->asArray()->all();

        // 応募情報をキャッシュ
        $this->_applicationIdsAndNos = ApplicationMaster::find()->select([
            ApplicationMaster::tableName() . '.id',
            ApplicationMaster::tableName() . '.application_no',
            ApplicationMaster::tableName() . '.job_master_id',
        ])->where([ApplicationMaster::tableName() . '.tenant_id' => $this->tenantId])->asArray()->all();

        // faker用意
        $faker = Factory::create();

        // data候補をキャッシュ
        foreach (range(1, 100) as $i) {
            $ago = 60 * 60 * 24 * 90; // 3ヶ月
            $this->data['access_log']['accessed_at'][] = $faker->numberBetween(time() - $ago, time());
            $this->data['access_log']['access_user_agent'][] = $faker->userAgent;
            $this->data['access_log']['access_referrer'][] = $faker->url;
        }
        $this->data['access_log']['access_browser'] = [
            'IE11',
            'chrome',
            'edge',
            'fireFox',
            'safari',
            'other',
        ];
    }

    /**
     * URL候補を生成してキャッシュ
     */
    public function setUpUrls()
    {
        $this->_siteUrl = self::SCHEME . Yii::$app->tenant->tenant->tenant_code . '/';

        // 全国top
        $this->data['access_log']['access_url'][] = $this->_siteUrl;
        // エリアtop
        $areas = Area::findAll(['valid_chk' => Area::FLAG_VALID, 'tenant_id' => $this->tenantId]);
        foreach ($areas as $area) {
            $this->data['access_log']['access_url'][] = $this->_siteUrl . $area->area_dir;
        }
        // 求人詳細URL
        $this->data['access_log']['access_url'][] = $this->_siteUrl . Yii::$app->tenant->tenant->kyujin_detail_dir;

        // 応募完了URL
        $this->data['access_log']['access_url'][] = $this->_siteUrl . 'apply/complete';
    }

    /**
     * @param $count
     * @return mixed|void
     */
    protected function insert($count)
    {
        $this->setUp();
        //カラム情報取得
        $accessLogColumnNames = AccessLog::getTableSchema()->columnNames;

        // 作業id取得
        $this->currentId['access_log'] = AccessLog::find()->max('id') + 1;

        // 有効データ作成
        $this->makeValues($count);

        // 有効データ挿入
        Yii::$app->db->createCommand()->batchInsert(
            AccessLog::tableName(),
            $accessLogColumnNames,
            $this->rows['access_log']
        )->execute();
    }

    /**
     * insertするvalueを生成する
     * @param $count
     */
    private function makeValues($count)
    {
        // rowsを初期化
        $this->rows = [];
        // データを生成してセット
        for ($i = 1; $i <= $count; $i++) {
            $this->makeAccessLogRow();
            $this->currentId['access_log']++;
        }
    }

    private function makeAccessLogRow()
    {
        $this->currentTable = 'access_log';
        $array = $this->makeUrlArray();

        // search_dateとaccessed_atの内容がずれるので、一旦変数に格納
        $accessed_at = $this->data('accessed_at');

        $this->rows['access_log'][] = [
            $this->id(),                      // id
            $this->tenantId,                  // tenant_id
            $accessed_at,                     // accessed_at
            $array['job_master_id'],          // job_master_id
            $array['application_master_id'],  // application_master_id
            rand(0, 1),                       // carrier_type
            $array['access_url'],             // access_url
            $this->data('access_browser'),    // access_browser
            $this->data('access_user_agent'), // access_user_agent
            $this->data('access_referrer'),   // access_referrer
            date('Y-m-d', $accessed_at),      // search_date
        ];
    }

    /**
     * URLとそれに依存する値を生成する
     * @return array
     */
    private function makeUrlArray()
    {
        $url = $this->data('access_url');

        if ($url == $this->_siteUrl) {
            // 全国top
            return [
                'access_url' => $url,
                'job_master_id' => null,
                'application_master_id' => null,
                'access_referrer' => $this->data('access_referrer'),
            ];
        } elseif (strpos($url, Yii::$app->tenant->tenant->kyujin_detail_dir)) {
            // 求人詳細
            $jobIdAndNo = $this->rand($this->_jobIdsAndNos);
            return [
                'access_url' => $url . '/' . $jobIdAndNo['job_no'],
                'job_master_id' => $jobIdAndNo['id'],
                'application_master_id' => null,
                'access_referrer' => $this->data('access_referrer'),
            ];
        } elseif (strpos($url, 'apply/complete')) {
            // 応募完了
            $applicationIdAndNo = $this->rand($this->_applicationIdsAndNos);
            return [
                'access_url' => $url . '/' . $applicationIdAndNo['id'],
                'job_master_id' => $applicationIdAndNo['job_master_id'],
                'application_master_id' => $applicationIdAndNo['id'],
                'access_referrer' => $this->data('access_referrer'),
            ];
        } else {
            // エリアtop
            return [
                'access_url' => $url,
                'job_master_id' => null,
                'application_master_id' => null,
                'access_referrer' => $this->data('access_referrer'),
            ];
        }
    }

    /**
     * デフォルトでテナントを表記しないように変更
     * @param string $columnName
     * @param bool $addTenantSign
     * @return mixed
     */
    protected function data($columnName, $addTenantSign = false)
    {
        return parent::data($columnName, $addTenantSign);
    }


    /**
     * tenant毎にレコードとdataをクリアする
     * @param int $numOfRecords
     * @param int $tenantId
     */
    protected function tenantInsert($numOfRecords, $tenantId)
    {
        $this->data = [];
        AccessLog::deleteAll(['tenant_id' => $tenantId]);
        parent::tenantInsert($numOfRecords, $tenantId);
    }
}
