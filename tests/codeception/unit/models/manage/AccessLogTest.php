<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2015/12/24
 * Time: 12:38
 */

namespace models\manage;

use app\models\manage\ApplicationMaster;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\JobMaster;
use app\models\manage\AccessLog;
use app\models\queries\AccessLogQuery;
use tests\codeception\unit\JmTestCase;
use app\models\manage\AccessLogSearch;

// tenant2用のデータが用意されていないのでtenant2でのテストは保留
class AccessLogTest extends JmTestCase
{
    /**
     * rules test
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new AccessLog();
            $model->load([
                $model->formName() => [
                    'accessed_at' => null,
                    'carrier_type' => null,
                    ],
            ]);
            $model->validate();
            verify($model->hasErrors('accessed_at'))->true();
            verify($model->hasErrors('carrier_type'))->true();
        });

        $this->specify('数字チェック', function () {
            $model = new AccessLog();
            $model->load([
                $model->formName() => [
                    'tenant_id' => '文字列',
                    'accessed_at' => '文字列',
                    'job_master_id' => '文字列',
                    'carrier_type' => '文字列',
                    ],
            ]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('accessed_at'))->true();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('carrier_type'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new AccessLog();
            $model->load([
                $model->formName() => [
                    'tenant_id' => 1,
                    'accessed_at' => 1,
                    'job_master_id' => 1,
                    'carrier_type' => 1,
                    'access_url' => 'あああ',
                    'access_browser' => 'あああ',
                    'access_user_agent' => 'あああ',
                    'access_referrer' => 'あああ',
                    ],
            ]);
            verify($model->validate())->true();
        });
    }

    /**
     * アクセス機器リストのgetterのテスト
     */
    public function testGetCarrierTypeList()
    {
        $model = new AccessLog();
        verify($model->carrierTypeList[AccessLog::PC_CARRIER])->equals('PC');
        verify($model->carrierTypeList[AccessLog::SMART_PHONE_CARRIER])->equals('スマホ');
    }
    /**
     * 各種モデルを返すgetter
     */
    public function testGetApplicationModel()
    {
        $model = AccessLog::findOne($this->id(1, 'access_log'));
        verify($model->applicationModel)->isInstanceOf(ApplicationMaster::className());
    }
    public function testGetJobModel()
    {
        $model = AccessLog::findOne($this->id(1, 'access_log'));
        verify($model->jobModel)->isInstanceOf(JobMaster::className());
    }
    public function testGetClientModel()
    {
        $model = AccessLog::findOne($this->id(1, 'access_log'));
        verify($model->clientModel)->isInstanceOf(ClientMaster::className());
    }
    public function testGetCorpModel()
    {
        $model = AccessLog::findOne($this->id(1, 'access_log'));
        verify($model->corpModel)->isInstanceOf(CorpMaster::className());
    }

    /**
     * getJobNoのテスト
     */
    public function testGetJobNo()
    {
        $i = $this->id(1, 'access_log');
        $accessLog = AccessLog::findOne(['id' => $i]);
        $job = $accessLog->jobMaster;

        $model = new AccessLogSearch();
        $model->job_master_id = $accessLog->job_master_id;
        verify($model->jobNo)->equals($job->job_no);
    }

    /**
     * setJobNoのテスト
     */
    public function testSetJobNo()
    {
        $model = new AccessLogSearch();
        $jobNo = $this->id(25, 'job_master');

        $model->jobNo = $jobNo;
        verify($model->jobNo)->equals($jobNo);
    }

    /**
     * Findのtest
     * 権限用の各管理者のレコードが最低一つあることが前提
     */
    public function testFind()
    {
        verify(AccessLog::find())->isInstanceOf(AccessLogQuery::className());
    }

    /**
     * AuthFindのtest
     * 権限用の各管理者のレコードが最低一つあることが前提
     */
    public function testAuthFind()
    {
        $model = new AccessLog();
        /** @var AccessLog[] $models */
        $this->setIdentity('owner_admin');
        $models = $model->authFind()->all();
        verify($models)->notEmpty();
        $count = AccessLog::find()->count();
        verify($models)->count((int)$count);

        $this->setIdentity('corp_admin');
        $models = $model->authFind()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify($model->clientMaster->corp_master_id)->equals($this->getIdentity()->corp_master_id);
        }

        $this->setIdentity('client_admin');
        $models = $model->authFind()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify($model->clientMaster->id)->equals($this->getIdentity()->client_master_id);
        }
    }
}
