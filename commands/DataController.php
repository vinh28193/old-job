<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/05/16
 * Time: 16:09
 */
namespace app\commands;

use app\models\manage\AdminMaster;
use app\models\manage\ApplicationMaster;
use app\models\manage\ClientMaster;
use app\models\manage\JobMaster;
use yii;
use yii\console\Controller;

class DataController extends Controller
{
    const COMMON_FIXTURE_BASE = 'tests\codeception\fixtures\\';
    const FIXTURES = [
        // 基本
        'Tenant',
        'SiteMaster',
        'NameMaster',
        // 権限
        'AuthItem',
        'AuthItemChild',
        'AuthRule',
        // 項目設定
        'AdminColumnSet',
        'ApplicationColumnSet',
        'ClientColumnSet',
        'CorpColumnSet',
        'InquiryColumnSet',
        'JobColumnSet',
        'MainDisp',
        'ListDisp',
        // 管理画面設定
        'ManageMenuMain',
        'ManageMenuCategory',
        'Policy',
        'SendMailSet',
        'ToolMaster',
        // 仕事情報
        'DispType',
        'ClientChargePlan',
        // 検索キー（area, prefは別controllerでloadしている）
        'SearchkeyMaster',
        'Dist',
        'Station',
        'WageCategory',
        'WageItem',
        'SearchkeyCategory1',
        'SearchkeyCategory2',
        'SearchkeyCategory3',
        'SearchkeyCategory4',
        'SearchkeyCategory5',
        'SearchkeyItem1',
        'SearchkeyItem2',
        'SearchkeyItem3',
        'SearchkeyItem4',
        'SearchkeyItem5',
        'SearchkeyItem11',
        'SearchkeyItem12',
        'SearchkeyItem13',
        'SearchkeyItem14',
        'SearchkeyItem15',
        // widget
        'WidgetLayout',
        'Widget',
        'WidgetData',
        'WidgetDataArea',
        // corp
        'CorpMaster',
        // Application
        'ApplicationStatus',
        'Occupation',
    ];

    public function actionIndex()
    {
        foreach (self::FIXTURES as $fixtureName) {
            $this->loadFixture($fixtureName);
        }
        ClientMaster::deleteAll();
        $client = new ClientDataController('client-data', $this->module);
        $client->actionIndex(10000, 5, false);
        unset($client);
        echo "client_masterをinsertしました\n";

        AdminMaster::deleteAll();
        $admin = new AdminDataController('admin-data', $this->module);
        $admin->actionIndex(20000, 5, false);
        unset($admin);
        echo "admin_masterをinsertしました\n";

        $mediaUpload = new MediaUploadDataController('media-upload-data', $this->module);
        $mediaUpload->actionIndex();
        unset($mediaUpload);
        echo "media_uploadをinsertしました\n";

        $prefDist = new PrefDistDataController('pref-dist-data', $this->module);
        $prefDist->actionIndex();
        unset($prefDist);
        echo "pref_dist_masterをinsertしました\n";

        $jobType = new JobTypeDataController('job-type-data', $this->module);
        $jobType->actionIndex(5);
        unset($jobType);
        echo "job_type_dataをinsertしました\n";

        JobMaster::deleteAll();
        $job = new JobDataController('job-data', $this->module);
        $job->actionIndex(30000, 5, false);
        unset($job);
        echo "job_masterをinsertしました\n";

        ApplicationMaster::deleteAll();
        $application = new ApplicationDataController('application-data', $this->module);
        $application->actionIndex(30000, 5, false);
        unset($application);
        echo "application_masterをinsertしました\n";
    }

    private function loadFixture($fixtureName)
    {
        Yii::createObject(self::COMMON_FIXTURE_BASE . $fixtureName . 'Fixture')->load();
        echo "{$fixtureName}をloadしました\n";
    }
}
