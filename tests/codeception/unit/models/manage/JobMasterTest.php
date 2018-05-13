<?php

namespace models\manage;

use app\models\JobAccessRecommend;
use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\DispType;
use app\models\manage\JobMaster;
use app\models\manage\JobMasterSearch;
use app\models\manage\JobReviewStatus;
use app\models\manage\MediaUpload;
use app\models\manage\searchkey\JobDist;
use app\models\manage\searchkey\JobSearchkeyItem1;
use app\models\manage\searchkey\JobSearchkeyItem10;
use app\models\manage\searchkey\JobSearchkeyItem11;
use app\models\manage\searchkey\JobSearchkeyItem12;
use app\models\manage\searchkey\JobSearchkeyItem13;
use app\models\manage\searchkey\JobSearchkeyItem14;
use app\models\manage\searchkey\JobSearchkeyItem15;
use app\models\manage\searchkey\JobSearchkeyItem16;
use app\models\manage\searchkey\JobSearchkeyItem17;
use app\models\manage\searchkey\JobSearchkeyItem18;
use app\models\manage\searchkey\JobSearchkeyItem19;
use app\models\manage\searchkey\JobSearchkeyItem2;
use app\models\manage\searchkey\JobSearchkeyItem20;
use app\models\manage\searchkey\JobSearchkeyItem3;
use app\models\manage\searchkey\JobSearchkeyItem4;
use app\models\manage\searchkey\JobSearchkeyItem5;
use app\models\manage\searchkey\JobSearchkeyItem6;
use app\models\manage\searchkey\JobSearchkeyItem7;
use app\models\manage\searchkey\JobSearchkeyItem8;
use app\models\manage\searchkey\JobSearchkeyItem9;
use app\models\manage\searchkey\JobStationInfo;
use app\models\manage\searchkey\JobType;
use app\models\manage\searchkey\JobWage;
use app\modules\manage\models\Manager;
use Yii;
use tests\codeception\unit\fixtures\ClientChargePlanFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use tests\codeception\fixtures\DispTypeFixture;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;

/**
 * @group job_master
 * @property JobMasterFixture $job_master
 * @property ClientMasterFixture $client_master
 * @property ClientChargePlanFixture $client_charge_plan
 * @property DispTypeFixture $disp_type
 */
class JobMasterTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(JobMaster::tableName())->equals('job_master');
    }

    /**
     * 登録前処理テスト
     */
    public function testBeforeSave()
    {
        $this->setIdentity(Manager::CLIENT_ADMIN);
        $this->specify('掲載企業権限での有効日数付きプランの新規登録の際に終了日が自動で入る', function () {
            $model = new JobMaster();
            $model->disp_start_date = time();
            $model->client_charge_plan_id = $this->id(1, 'client_charge_plan');
            $model->beforeSave(true);
            verify($model->disp_end_date)->equals($model->disp_start_date + ($model->clientChargePlan->period - 1) * 24 * 60 * 60);
        });

        $this->specify('disp_type_noに料金プランに応じた値が入っているか', function () {
            $model = new JobMaster();
            $model->load([
                $model->formName() => [
                    'valid_chk' => 1,
                    'corpMasterId' => $this->id(1, 'corp_master'),
                    'client_charge_plan_id' => $this->id(1, 'client_charge_plan'),
                    'client_master_id' => $this->id(2, 'client_master'),
                    'disp_start_date' => 1455202800/*'2016-02-12'*/,
                    'disp_end_date' => 1486825200/*'2017-02-12'*/,
                ],
            ]);
            $model->beforeSave(true);
            verify($model->disp_type_sort)->equals($model->clientChargePlan->dispType->disp_type_no);
        });

        $this->specify('job_noのincrement', function () {
            // バックアップが空の時はjob_masterのnoの最大値+1が入る
            $model = new JobMaster();
            $model->client_charge_plan_id = $this->id(1, 'client_charge_plan');
            $model->beforeSave(true);
            verify($model->job_no)->equals(JobMasterFixture::RECORDS_PER_TENANT + 1);
            // job_noが最大値のレコードを削除する
            $deleteModel = JobMasterSearch::find()->where(['job_no' => JobMasterFixture::RECORDS_PER_TENANT])->one();
            $searchModel = new JobMasterSearch();
            $searchModel->backupAndDelete([$deleteModel]);
            // バックアップのnoの最大値の方が大きい時はバックアップのnoの最大値+1が入る
            $model = new JobMaster();
            $model->client_charge_plan_id = $this->id(1, 'client_charge_plan');
            $model->beforeSave(true);
            verify($model->job_no)->equals(JobMasterFixture::RECORDS_PER_TENANT + 1);
        });

        // 削除したjob_masterを元に戻す
        self::getFixtureInstance('job_master')->initTable();
        self::getFixtureInstance('job_master_backup')->initTable();
    }

    /**
     * beforeValidateのtest
     * 一緒にloadAuthParamのtestも
     */
    public function testBeforeValidateByOwner()
    {
        $this->setIdentity(Manager::OWNER_ADMIN);
        $model = new JobMaster();
        $model->beforeValidate();
        verify($model->corpMasterId)->equals(null);
        verify($model->client_master_id)->equals(null);
    }

    public function testBeforeValidateByCorp()
    {
        $this->setIdentity(Manager::CORP_ADMIN);
        $corpId = $this->getIdentity()->corp_master_id;
        $model = new JobMaster();
        $model->beforeValidate();
        verify($model->corpMasterId)->equals($corpId);
        verify($model->client_master_id)->equals(null);
    }

    public function testBeforeValidateByClient()
    {
        $this->setIdentity(Manager::CLIENT_ADMIN);
        $clientId = $this->getIdentity()->client_master_id;
        $corpId = $this->getIdentity()->corp_master_id;

        $model = new JobMaster();
        $model->beforeValidate();
        verify($model->corpMasterId)->equals($corpId);
        verify($model->client_master_id)->equals($clientId);
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $jobMaster = new JobMaster();
        verify($jobMaster->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->setIdentity(Manager::OWNER_ADMIN);
        $this->specify('必須チェック', function () {
            $model = new JobMaster();
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('disp_start_date'))->true();
            verify($model->hasErrors('corpMasterId'))->true();
            verify($model->hasErrors('client_charge_plan_id'))->true();
        });

        $this->specify('数字チェック', function () {
            $model = new JobMaster();
            $model->load([
                $model->formName() => [
                    'created_at' => '文字列',
                    'updated_at' => '文字列',
                    'corpMasterId' => '文字列',
                    'client_master_id' => '文字列',
                    'job_review_status_id' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('created_at'))->true();
            verify($model->hasErrors('updated_at'))->true();
            verify($model->hasErrors('corpMasterId'))->true();
            verify($model->hasErrors('client_master_id'))->true();
        });

        $this->specify('boolチェック', function () {
            $model = new JobMaster();
            $model->load([
                $model->formName() => [
                    'valid_chk' => 50,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $corpId = $this->id(1, 'corp_master');
        $planId = $this->id(1, 'client_charge_plan');
        // 掲載企業を新規登録
        $client = new ClientMaster();
        $client->load([
            $client->formName() => [
                'corp_master_id' => $corpId,
                'client_name' => 'プラン上限テスト',
                'valid_chk' => 1,
            ],
        ]);
        $client->save(false);
        // その掲載企業にplanId=1を1枠設定
        $clientCharge = new ClientCharge();
        $clientCharge->load([
            $clientCharge->formName() => [
                'client_charge_plan_id' => $planId,
                'client_master_id' => $client->id,
                'limit_num' => 1,
            ],
        ]);
        $clientCharge->save(false);

        $this->specify('正しい値', function () use ($planId, $client) {
            $model = new JobMaster();
            $model->load([
                $model->formName() => [
                    'valid_chk' => 1,
                    'corpMasterId' => 1,
                    'client_charge_plan_id' => $planId,
                    'client_master_id' => $client->id,
                    'disp_start_date' => '2016/02/12',
                    'disp_end_date' => '2017/02/12',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->false();
            verify($model->hasErrors('corpMasterId'))->false();
            verify($model->hasErrors('client_charge_plan_id'))->false();
            verify($model->hasErrors('created_at'))->false();
            verify($model->hasErrors('updated_at'))->false();
            verify($model->hasErrors('client_master_id'))->false();
            verify($model->hasErrors('disp_start_date'))->false();
            verify($model->hasErrors('disp_end_date'))->false();
            // 次の検証のためにsave(columnSetから来る必須項目を避けるためにvalidationを切っています。以下同じです。)
            $model->save(false);
        });

        $this->specify('プラン上限チェック', function () use ($planId, $client) {
            $model = new JobMaster();
            $model->load([
                $model->formName() => [
                    'valid_chk' => 1,
                    'corpMasterId' => 1,
                    'client_charge_plan_id' => $planId,
                    'client_master_id' => $client->id,
                    'disp_start_date' => '2016/02/12',
                    'disp_end_date' => '2017/02/12',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('client_charge_plan_id'))->true();
        });

        // fixtureを元に戻す
        static::getFixtureInstance('job_master')->initTable();
        static::getFixtureInstance('client_master')->initTable();
        static::getFixtureInstance('client_charge')->initTable();
    }

    /**
     * 運営元権限での日付ルール検証
     */
    public function testDateRuleByOwner()
    {
        $this->setIdentity(Manager::OWNER_ADMIN);
        $plans = $this->settingPlan();

        $model = new JobMaster();
        $this->validateDate($model);

        $model = new JobMaster();
        $model->client_charge_plan_id = $plans['oneWeek']->id;
        $this->validateDate($model);

        $model = new JobMaster();
        $model->client_charge_plan_id = $plans['noPeriod']->id;
        $this->validateDate($model);
    }

    /**
     * 代理店権限での日付ルール
     * 掲載企業も同じロジックを通るため掲載企業は省略する
     */
    public function testDateRuleByCorp()
    {
        $this->setIdentity(Manager::CORP_ADMIN);
        $plans = $this->settingPlan();

        $model = new JobMaster();
        $this->validateOnlyStartDate($model);

        $model = new JobMaster();
        $model->client_charge_plan_id = $plans['oneWeek']->id;
        $this->validateOnlyStartDate($model);

        $model = new JobMaster();
        $model->client_charge_plan_id = $plans['noPeriod']->id;
        $this->validateDate($model);
    }

    /**
     * @return ClientChargePlan[]
     */
    private function settingPlan()
    {
        /** @var ClientChargePlan[] $plans */
        $plans = ClientChargePlan::find()->all();
        $oneWeekPlan = $plans[0];
        $oneWeekPlan->period = 7;
        $oneWeekPlan->save();
        $noPeriodPlan = $plans[1];
        $noPeriodPlan->period = null;
        $noPeriodPlan->save();

        return ['oneWeek' => $oneWeekPlan, 'noPeriod' => $noPeriodPlan];
    }

    /**
     * 両方検証する
     * @param JobMaster $model
     */
    private function validateDate($model)
    {
        $invalidStartValues = [
            '空白' => '',
            '不適切な文字列' => '文字列',
            '先過ぎる日付' => '2038/01/20',
            '過去過ぎる日付' => '1901/12/13',
        ];
        $invalidEndValues = [
            '不適切な文字列' => '文字列',
            '先過ぎる日付' => '2038/01/20',
            '過去過ぎる日付' => '1901/12/13',
        ];
        $validValue = '2016/08/31';
        // 両方invalid
        foreach ($invalidStartValues as $startValue) {
            foreach ($invalidEndValues as $endValue) {
                $model->load([
                    $model->formName() => [
                        'disp_start_date' => $startValue,
                        'disp_end_date' => $endValue,
                    ],
                ]);
                $model->validate();
                verify($model->hasErrors('disp_start_date'))->true();
                verify($model->hasErrors('disp_end_date'))->true();
            }
        }
        // 開始日時のみinvalid
        foreach ($invalidStartValues as $startValue) {
            $model->load([
                $model->formName() => [
                    'disp_start_date' => $startValue,
                    'disp_end_date' => $validValue,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('disp_start_date'))->true();
            if ($startValue == '2038/01/20') {
                // compareValidationが走るのでこれだけエラーが出る
                verify($model->hasErrors('disp_end_date'))->true();
            } else {
                verify($model->hasErrors('disp_end_date'))->false();
            }
        }
        // 終了日時のみinvalid
        foreach ($invalidEndValues as $endValue) {
            $model->load([
                $model->formName() => [
                    'disp_start_date' => $validValue,
                    'disp_end_date' => $endValue,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('disp_start_date'))->false();
            verify($model->hasErrors('disp_end_date'))->true();
        }
        // compare
        $model->load([
            $model->formName() => [
                'disp_start_date' => '2016/06/28',
                'disp_end_date' => '2016/06/27',
            ],
        ]);
        $model->validate();
        verify($model->hasErrors('disp_end_date'))->true();
        // 正しい値
        $model->load([
            $model->formName() => [
                'disp_start_date' => $validValue,
                'disp_end_date' => $validValue,
            ],
        ]);
        $model->validate();
        verify($model->hasErrors('disp_start_date'))->false();
        verify($model->hasErrors('disp_end_date'))->false();
    }

    /**
     * 開始日のみ検証する
     * @param JobMaster $model
     */
    private function validateOnlyStartDate($model)
    {
        $invalidStartValues = [
            '空白' => '',
            '不適切な文字列' => '文字列',
            '先過ぎる日付' => '2038/01/20',
            '過去過ぎる日付' => '1901/12/13',
        ];
        $invalidEndValues = [
            '不適切な文字列' => '文字列',
            '先過ぎる日付' => '2038/01/20',
            '過去過ぎる日付' => '1901/12/13',
        ];
        $validValue = '2016/08/31';

        // 両方invalid
        foreach ($invalidStartValues as $startValue) {
            foreach ($invalidEndValues as $endValue) {
                $model->load([
                    $model->formName() => [
                        'disp_start_date' => $startValue,
                        'disp_end_date' => $endValue,
                    ],
                ]);
                $model->validate();
                verify($model->hasErrors('disp_start_date'))->true();
                verify($model->hasErrors('disp_end_date'))->false();
            }
        }
        // 終了日時のみinvalid
        foreach ($invalidEndValues as $endValue) {
            $model->load([
                $model->formName() => [
                    'disp_start_date' => $validValue,
                    'disp_end_date' => $endValue,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('disp_start_date'))->false();
            verify($model->hasErrors('disp_end_date'))->false();
        }
        // compare
        $model = new JobMaster();
        $model->load([
            $model->formName() => [
                'disp_start_date' => '2016/06/28',
                'disp_end_date' => '2016/06/27',
            ],
        ]);
        $model->validate();
        verify($model->hasErrors('disp_end_date'))->false();
    }

    /**
     * 各種モデルを返すgetter
     * __getのオーバーライドもここで検証している
     * (本当はrelationレコードの有無で場合分けして値のチェックもした方がより正確だが今回はこうする)
     */
    public function testModelGetters()
    {
        $model = JobMaster::findOne($this->id(12, 'job_master'));
        verify($model->clientModel)->isInstanceOf(ClientMaster::className());
        verify($model->jobDistModel)->isInstanceOf(JobDist::className());

        $jobStationModels = $model->jobStationModel;
        verify($jobStationModels)->count(3);
        foreach ($jobStationModels as $jobStationModel) {
            verify($jobStationModel)->isInstanceOf(JobStationInfo::className());
        }

        verify($model->jobWageModel)->isInstanceOf(JobWage::className());
        verify($model->jobTypeModel)->isInstanceOf(JobType::className());
        verify($model->jobAccessRecommendModel)->isInstanceOf(JobAccessRecommend::className());
        verify($model->jobSearchkeyItem1Model)->isInstanceOf(JobSearchkeyItem1::className());
        verify($model->jobSearchkeyItem2Model)->isInstanceOf(JobSearchkeyItem2::className());
        verify($model->jobSearchkeyItem3Model)->isInstanceOf(JobSearchkeyItem3::className());
        verify($model->jobSearchkeyItem4Model)->isInstanceOf(JobSearchkeyItem4::className());
        verify($model->jobSearchkeyItem5Model)->isInstanceOf(JobSearchkeyItem5::className());
        verify($model->jobSearchkeyItem6Model)->isInstanceOf(JobSearchkeyItem6::className());
        verify($model->jobSearchkeyItem7Model)->isInstanceOf(JobSearchkeyItem7::className());
        verify($model->jobSearchkeyItem8Model)->isInstanceOf(JobSearchkeyItem8::className());
        verify($model->jobSearchkeyItem9Model)->isInstanceOf(JobSearchkeyItem9::className());
        verify($model->jobSearchkeyItem10Model)->isInstanceOf(JobSearchkeyItem10::className());
        verify($model->jobSearchkeyItem11Model)->isInstanceOf(JobSearchkeyItem11::className());
        verify($model->jobSearchkeyItem12Model)->isInstanceOf(JobSearchkeyItem12::className());
        verify($model->jobSearchkeyItem13Model)->isInstanceOf(JobSearchkeyItem13::className());
        verify($model->jobSearchkeyItem14Model)->isInstanceOf(JobSearchkeyItem14::className());
        verify($model->jobSearchkeyItem15Model)->isInstanceOf(JobSearchkeyItem15::className());
        verify($model->jobSearchkeyItem16Model)->isInstanceOf(JobSearchkeyItem16::className());
        verify($model->jobSearchkeyItem17Model)->isInstanceOf(JobSearchkeyItem17::className());
        verify($model->jobSearchkeyItem18Model)->isInstanceOf(JobSearchkeyItem18::className());
        verify($model->jobSearchkeyItem19Model)->isInstanceOf(JobSearchkeyItem19::className());
        verify($model->jobSearchkeyItem20Model)->isInstanceOf(JobSearchkeyItem20::className());
    }

    /**
     * getJobImagePathのtest
     */
    public function testGetJobImagePath()
    {
        /** @var ClientMaster $client */
        $client = ClientMaster::find()->one();
        $clientMasterId = $client->id;
        $anotherClientId = ClientMaster::findOne(['id' => $clientMasterId + 1])->id;

        $ownerMedia = MediaUpload::findOne(['client_master_id' => null]);
        $clientMedia = MediaUpload::findOne(['client_master_id' => $clientMasterId]);
        $anotherClientMedia = MediaUpload::findOne(['client_master_id' => $anotherClientId]);

        $model = new JobMaster();
        $model->client_master_id = $clientMasterId;

        $model->media_upload_id_2 = $ownerMedia->id;
        $model->media_upload_id_3 = $clientMedia->id;
        $model->media_upload_id_4 = $anotherClientMedia->id;
        verify($model->getJobImagePath(2))->equals($ownerMedia->srcUrl());
        verify($model->getJobImagePath(3))->equals($clientMedia->srcUrl());
        verify($model->getJobImagePath(4))->equals('');
    }

    /**
     * corpMasterIdのgetterとsetterのtest
     */
    public function testGetAndSetCorpMasterId()
    {
        /** @var CorpMaster[] $corps */
        $corps = CorpMaster::find()->all();
        foreach ($corps as $corp) {
            $clients = $corp->clientMaster;
            $jobs = JobMaster::findAll(['client_master_id' => ArrayHelper::getColumn($clients, 'id')]);
            verify($jobs)->notEmpty();
            foreach ($jobs as $job) {
                verify($job->corpMasterId)->equals($corp->id);
            }
        }

        $job = JobMaster::findOne(['id' => $this->id(24, 'job_master')]);
        $job->corpMasterId = 212;
        verify($job->corpMasterId)->equals(212);
    }

    /**
     * saveRelationalModelsのtest
     */
    public function testSaveRelationalModels()
    {
        $this->setIdentity(Manager::OWNER_ADMIN);
        $jobId = $this->id(21, 'job_master');

        $model = JobMaster::findOne($jobId);
        $jobTypeParam = [
            'itemIds' => [1, 2, 3],
        ];
        $jobDistParam = [
            'itemIds' => [4],
        ];
        $jobWageParam = [
            'itemIds' => [5, 6],
        ];
        $jobStationParam = [
            [
                'station_id' => 22222,
                'transport_type' => 0,
                'transport_time' => 15,
            ],
            [
                'station_id' => '',
                'transport_type' => 1,
                'transport_time' => 30,
            ],
            [],
        ];
        $jobSearchkeyItem1Param = [
            'itemIds' => [7, 8, 9, 10, 11, 12],
        ];

        $model->saveRelationalModels([
            'JobType' => $jobTypeParam,
            'JobDist' => $jobDistParam,
            'JobWage' => $jobWageParam,
            'JobStationInfo' => $jobStationParam,
            'JobSearchkeyItem1' => $jobSearchkeyItem1Param,
        ]);
        foreach ($jobTypeParam['itemIds'] as $id) {
            $this->tester->canSeeInDatabase('job_type', ['job_master_id' => $jobId, 'job_type_small_id' => $id]);
        }
        foreach ($jobDistParam['itemIds'] as $id) {
            $this->tester->canSeeInDatabase('job_dist', ['job_master_id' => $jobId, 'dist_id' => $id]);
        }
        foreach ($jobWageParam['itemIds'] as $id) {
            $this->tester->canSeeInDatabase('job_wage', ['job_master_id' => $jobId, 'wage_item_id' => $id]);
        }
        $this->tester->canSeeInDatabase('job_station_info', ['job_master_id' => $jobId] + $jobStationParam[0]);
        $this->tester->cantSeeInDatabase('job_station_info', ['job_master_id' => $jobId] + $jobStationParam[1]);
        foreach ($jobSearchkeyItem1Param['itemIds'] as $id) {
            $this->tester->canSeeInDatabase('job_searchkey_item1', ['job_master_id' => $jobId, 'searchkey_item_id' => $id]);
        }
    }

    /**
     * getTypeScenarioのtest
     */
    public function testGetTypeScenario()
    {
        for ($i = 1; $i <= 3; $i++) {
            $dispType = DispType::findOne(['disp_type_no' => $i]);
            $plans = ClientChargePlan::findAll(['disp_type_id' => $dispType->id]);
            $jobs = JobMaster::findAll(['client_charge_plan_id' => ArrayHelper::getColumn($plans, 'id')]);
            verify($jobs)->notEmpty();
            foreach ($jobs as $job) {
                verify($job->getTypeScenario())->equals('type' . $i);
            }
        }
    }

    /**
     * loadテスト
     */
    public function testLoad()
    {
        $this->setIdentity(Manager::OWNER_ADMIN);
        $model = new JobMaster();
        verify($model->job_review_status_id)->null();
        // job_review_status_id以外の適当な値をロード
        $model->load([
            $model->formName() => [
                'disp_start_date' => '',
            ],
        ]);
        verify($model->job_review_status_id)->notNull();
    }

    /**
     * 審査するかどうかのチェックテスト
     */
    public function testUseReview()
    {
        // 新規レコードの場合（審査機能ON/OFF）
        $model = new JobMaster();
        Yii::$app->tenant->tenant->review_use = 0;
        verify($model->useReview())->false();
        Yii::$app->tenant->tenant->review_use = 1;
        verify($model->useReview())->false();

        // 既存レコードの場合（審査機能ON/OFF）
        $model = JobMaster::findOne(101);
        Yii::$app->tenant->tenant->review_use = 0;
        verify($model->useReview())->false();
        Yii::$app->tenant->tenant->review_use =1;
        verify($model->useReview())->true();
    }

    /**
     * 審査対象かどうかのチェックテスト
     */
    public function testIsReview()
    {
        $jobId = 101;
        $corpId = 7;

        $this->specify('運営元管理者', function () use ($jobId) {
            $model = JobMaster::findOne($jobId);
            // 運営元でログイン
            $this->setIdentity(Manager::OWNER_ADMIN);

            // STEP_OWNER_REVIEWの場合
            $model->job_review_status_id = JobReviewStatus::STEP_OWNER_REVIEW;
            verify($model->isReview())->true();

            // STEP_OWNER_REVIEW以外の場合
            $model->job_review_status_id = JobReviewStatus::STEP_CORP_REVIEW;
            verify($model->isReview())->false();
        });
        $this->specify('代理店管理者', function () use ($jobId, $corpId) {
            // 代理店でログイン
            $this->setIdentity(Manager::CORP_ADMIN);

            // STEP_CORP_REVIEWに設定
            $model = JobMaster::findOne($jobId);
            $model->job_review_status_id = JobReviewStatus::STEP_CORP_REVIEW;

            // 代理店審査ありの場合
            $corp = CorpMaster::findOne($corpId);
            $corp->corp_review_flg = 1;
            $corp->save(false);
            verify($model->isReview())->true();

            // 代理店審査なしの場合
            $corp->corp_review_flg = 0;
            $corp->save(false);
            $model = JobMaster::findOne($jobId);
            $model->job_review_status_id = JobReviewStatus::STEP_CORP_REVIEW;
            verify($model->isReview())->false();
        });
        $this->specify('掲載企業管理者', function () use ($jobId) {
            $model = JobMaster::findOne($jobId);
            // 掲載企業でログイン
            $this->setIdentity(Manager::CLIENT_ADMIN);

            // 必ずfalseに
            verify($model->isReview())->false();
        });
    }

    /**
     * 審査対象外かどうかのチェックテスト
     */
    public function testIsNotReviewer()
    {
        $jobId = 101;

        $this->specify('代理店審査中ステータス', function () use ($jobId) {
            $model = JobMaster::findOne($jobId);
            $model->job_review_status_id = JobReviewStatus::STEP_CORP_REVIEW;

            // 運営元の場合
            $this->setIdentity(Manager::OWNER_ADMIN);
            verify($model->isNotReviewer())->true();

            // 代理店の場合
            $this->setIdentity(Manager::CORP_ADMIN);
            verify($model->isNotReviewer())->false();

            // 掲載企業の場合
            $this->setIdentity(Manager::CLIENT_ADMIN);
            verify($model->isNotReviewer())->true();
        });
        $this->specify('運営元審査中ステータス', function () use ($jobId) {
            $model = JobMaster::findOne($jobId);
            $model->job_review_status_id = JobReviewStatus::STEP_OWNER_REVIEW;

            // 運営元の場合
            $this->setIdentity(Manager::OWNER_ADMIN);
            verify($model->isNotReviewer())->false();

            // 代理店の場合
            $this->setIdentity(Manager::CORP_ADMIN);
            verify($model->isNotReviewer())->true();

            // 掲載企業の場合
            $this->setIdentity(Manager::CLIENT_ADMIN);
            verify($model->isNotReviewer())->true();
        });
    }
}
