<?php
namespace models\manage;

use app\models\manage\ApplicationMaster;
use app\models\manage\JobMaster;
use app\models\manage\JobMasterBackup;
use app\models\manage\JobMasterSearch;
use app\modules\manage\models\Manager;
use tests\codeception\unit\fixtures\ApplicationMasterBackupFixture;
use tests\codeception\unit\fixtures\JobMasterBackupFixture;
use tests\codeception\unit\JmTestCase;
use Yii;
use tests\codeception\unit\fixtures\ApplicationMasterFixture;
use app\models\manage\ApplicationMasterSearch;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use tests\codeception\unit\fixtures\CorpMasterFixture;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @property ApplicationMasterFixture $application_master
 * @property JobMasterFixture $job_master
 * @property ClientMasterFixture $client_master
 * @property CorpMasterFixture $corp_master
 * @property ApplicationMasterBackupFixture $application_master_backup
 * @property JobMasterBackupFixture $job_master_backup
 */
class ApplicationMasterSearchTest extends JmTestCase
{
    /**
     * rules test
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new ApplicationMasterSearch();
            $model->load([$model->formName() => [
                'pref_id' => '文字列',
                'corpMasterId' => '文字列',
                'clientMasterId' => '文字列',
                'clientChargePlanId' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_id'))->true();
            verify($model->hasErrors('corpMasterId'))->true();
            verify($model->hasErrors('clientMasterId'))->true();
            verify($model->hasErrors('clientChargePlanId'))->true();
        });

        $this->specify('日付チェック', function () {
            $model = new ApplicationMasterSearch();
            $model->load([$model->formName() => [
                'searchStartDate' => '文字列',
                'searchEndDate' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('searchStartDate'))->true();
            verify($model->hasErrors('searchEndDate'))->true();
        });

        $this->specify('booleanチェック', function () {
            $model = new ApplicationMasterSearch();
            $model->load([$model->formName() => [
                'sex' => 3,
                'isJobDeleted' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('sex'))->true();
            verify($model->hasErrors('isJobDeleted'))->true();
        });

        $this->specify('必須チェック', function () {
            $model = new ApplicationMasterSearch();
            $model->load([$model->formName() => [
                'isJobDeleted' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('isJobDeleted'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new ApplicationMasterSearch();
            $model->load([$model->formName() => [
                'jobNo' => '1',
                'searchItem' => 'all',
                'searchText' => 'あああ',
                'corpMasterId' => 1,
                'clientMasterId' => 1,
                'clientChargePlanId' => 1,
                'application_status_id' => 1,
                'searchStartDate' => '1989/03/01',
                'searchEndDate' => '2016/06/06',
                'pref_id' => 1,
                'sex' => 1,
                'birthDateYear' => 'all',
                'birthDateMonth' => '12',
                'birthDateDay' => '24',
                'isJobDeleted' => 0,
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * 検索テスト
     * 手間がかかりすぎるので一旦はキーワード検索のallは検証していません。
     * todo all検索テスト実装
     */
    public function testSearchByOwner()
    {
        $this->setIdentity('owner_admin');
        // 削除された原稿関連の検証のために3つjobを削除
        $jobMasterSearch = new JobMasterSearch();
        $jobMasterSearch->backupAndDelete(JobMasterSearch::findAll(['id' => [$this->id(1, 'job_master'), $this->id(25, 'job_master'), $this->id(50, 'job_master')]]));

        $this->specify('初期状態で検索', function () {
            $models = $this->getApplicationMaster([
                'jobNo' => '',
                'searchItem' => 'all',
                'searchText' => '',
                'corpMasterId' => '',
                'clientMasterId' => '',
                'clientChargePlanId' => '',
                'application_status_id' => '',
                'searchStartDate' => '',
                'searchEndDate' => '',
                'pref_id' => '',
                'sex' => '',
                'birthDateYear' => '',
                'birthDateMonth' => '',
                'birthDateDay' => '',
                'isJobDeleted' => 0,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'jobMaster'))->isInstanceOf(JobMaster::className());
            }
        });

        $this->specify('クリアボタン押下時', function () {
            $models = $this->getApplicationMaster(1);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'jobMaster'))->isInstanceOf(JobMaster::className());
            }
        });

        $this->specify('jobIdで検索', function () {
            $models = $this->getApplicationMaster([
                'jobNo' => strval(5),
                'searchItem' => 'all',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'jobMaster.job_no'))->contains('5');
            }
        });

        $this->specify('キーワードで検索(応募No.)', function () {
            $models = $this->getApplicationMaster([
                'searchItem' => 'application_no',
                'searchText' => '2',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'application_no'))->contains('2');
            }
        });

        $this->specify('キーワードで検索(フルネーム)', function () {
            $models = $this->getApplicationMaster([
                'searchItem' => 'fullName',
                'searchText' => 's',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'fullName'))->contains('s');
            }
        });

        $this->specify('キーワードで検索(ふりがな)', function () {
            $models = $this->getApplicationMaster([
                'searchItem' => 'fullNameKana',
                'searchText' => 'a',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'fullNameKana'))->contains('a');
            }
        });

        $this->specify('代理店、掲載企業、料金プランで検索', function () {
            // 実在するものを取得
            $sample = JobMaster::findOne($this->id(12, 'job_master'));
            // 代理店検索
            $models = $this->getApplicationMaster([
                'searchItem' => 'all',
                'corpMasterId' => $sample->clientMaster->corp_master_id,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'jobMaster.clientMaster.corp_master_id'))->equals($sample->clientMaster->corp_master_id);
            }
            // 掲載企業検索
            $models = $this->getApplicationMaster([
                'searchItem' => 'all',
                'clientMasterId' => $sample->client_master_id,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'jobMaster.client_master_id'))->equals($sample->client_master_id);
            }
            // プラン検索
            $models = $this->getApplicationMaster([
                'searchItem' => 'all',
                'clientChargePlanId' => $sample->client_charge_plan_id,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'jobMaster.client_charge_plan_id'))->equals($sample->client_charge_plan_id);
            }
        });

        $this->specify('statusの検索', function () {
            $models = $this->getApplicationMaster([
                'searchItem' => 'all',
                'application_status_id' => 2,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'application_status_id'))->equals(2);
            }
        });

        $this->specify('応募期間の検索', function () {
            $models = $this->getApplicationMaster([
                'searchItem' => 'all',
                'searchStartDate' => '1988/02/09',
                'searchEndDate' => '2020/08/08',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'created_at'))->greaterOrEquals(strtotime('1988-02-09'));
                verify(ArrayHelper::getValue($model, 'created_at'))->lessOrEquals(strtotime('2020-08-08'));
            }
        });

        $this->specify('都道府県の検索', function () {
            $models = $this->getApplicationMaster([
                'searchItem' => 'all',
                'pref_id' => $this->id(24, 'pref'),
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'pref_id'))->equals($this->id(24, 'pref'));
            }
        });

        $this->specify('性別の検索', function () {
            $models = $this->getApplicationMaster([
                'searchItem' => 'all',
                'sex' => 0,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'sex'))->equals(0);
            }
        });

        $this->specify('誕生日の検索', function () {
            $models = $this->getApplicationMaster([
                'searchItem' => 'all',
                'birthDateYear' => 'all',
                'birthDateMonth' => '12',
                'birthDateDay' => '25',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'birth_date'))->contains('-12-25');
            }
        });

        $this->specify('削除済み求人原稿の検索', function () {
            $models = $this->getApplicationMaster([
                'searchItem' => 'all',
                'isJobDeleted' => 1,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'jobMaster'))->null();
                verify(ArrayHelper::getValue($model, 'jobMasterBackup'))->isInstanceOf(JobMasterBackup::className());
            }
        });

        self::getFixtureInstance('job_master_backup')->load();
        self::getFixtureInstance('job_master')->load();
    }

    public function testSearchByCorp()
    {
        $this->setIdentity('corp_admin');
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        $corpMasterId = $identity->corp_master_id;

        $this->specify('初期状態で検索', function () use ($corpMasterId) {
            $models = $this->getApplicationMaster([
                'jobNo' => '',
                'searchItem' => 'all',
                'searchText' => '',
                'corpMasterId' => '',
                'clientMasterId' => '',
                'clientChargePlanId' => '',
                'application_status_id' => '',
                'searchStartDate' => '',
                'searchEndDate' => '',
                'pref_id' => '',
                'sex' => '',
                'birthDateYear' => '',
                'birthDateMonth' => '',
                'birthDateDay' => '',
                'isJobDeleted' => 0,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'jobMaster'))->isInstanceOf(JobMaster::className());
                verify(ArrayHelper::getValue($model, 'jobMaster.clientMaster.corp_master_id'))->equals($corpMasterId);
            }
        });
    }

    public function testSearchByClient()
    {
        $this->setIdentity('client_admin');
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        $clientMasterId = $identity->client_master_id;

        $this->specify('初期状態で検索', function () use ($clientMasterId) {
            $models = $this->getApplicationMaster([
                'jobNo' => '',
                'searchItem' => 'all',
                'searchText' => '',
                'corpMasterId' => '',
                'clientMasterId' => '',
                'clientChargePlanId' => '',
                'application_status_id' => '',
                'searchStartDate' => '',
                'searchEndDate' => '',
                'pref_id' => '',
                'sex' => '',
                'birthDateYear' => '',
                'birthDateMonth' => '',
                'birthDateDay' => '',
                'isJobDeleted' => 0,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'jobMaster'))->isInstanceOf(JobMaster::className());
                verify(ArrayHelper::getValue($model, 'jobMaster.client_master_id'))->equals($clientMasterId);
            }
        });
    }

    private function getApplicationMaster($searchParam)
    {
        $model = new ApplicationMasterSearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);

        return $dataProvider->query->all();
    }

    /**
     * CSVダウンロードテスト
     */
    public function testCsvSearch()
    {
        $this->setIdentity('owner_admin');
        $searchModel = new ApplicationMasterSearch();
        // allCheck無し、id=1～5選択
        $id1 = $this->id(1, 'application_master');
        $id2 = $this->id(2, 'application_master');
        $id3 = $this->id(3, 'application_master');
        $id4 = $this->id(4, 'application_master');
        $id5 = $this->id(5, 'application_master');

        $post = [
            'gridData' => Json::encode([
                'searchParams' => [$searchModel->formName() => 1],
                'totalCount' => ApplicationMasterFixture::RECORDS_PER_TENANT,
                'selected' => [
                    Json::encode(['id' => $id1, 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $id2, 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $id3, 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $id4, 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $id5, 'tenant_id' => Yii::$app->tenant->id]),
                ],
                'allCheck' => false,
            ], 0),
            $searchModel->formName() => 1,
        ];
        // 被検証モデルと検証モデルの生成

        $dataProvider = $searchModel->csvSearch($post);
        /** @var ApplicationMasterSearch[] $models */
        $models = ArrayHelper::index($dataProvider->models, 'id');
        /** @var ApplicationMasterSearch[] $expectedModels */
        $expectedModels = ArrayHelper::index(ApplicationMasterSearch::find()->where(['in', 'id', [$id1, $id2, $id3, $id4, $id5]])->all(), 'id');
        // 数と内容を検証
        verify($dataProvider->totalCount)->equals(5);
        verify($models[$id1]->attributes)->equals($expectedModels[$id1]->attributes);
        verify($models[$id2]->attributes)->equals($expectedModels[$id2]->attributes);
        verify($models[$id3]->attributes)->equals($expectedModels[$id3]->attributes);
        verify($models[$id4]->attributes)->equals($expectedModels[$id4]->attributes);
        verify($models[$id5]->attributes)->equals($expectedModels[$id5]->attributes);
    }

    /**
     * deleteSearchメソッドとbackupAndDeleteメソッドのテスト
     */
    public function testDelete()
    {
        $this->setIdentity('owner_admin');
        $searchModel = new ApplicationMasterSearch();
        $id1 = $this->id(1, 'application_master');
        $id2 = $this->id(2, 'application_master');
        // allCheck無し、id=1のみ選択
        $post = [
            'gridData' => Json::encode([
                'searchParams' => [$searchModel->formName() => 1],
                'totalCount' => ApplicationMasterFixture::RECORDS_PER_TENANT,
                'selected' => [
                    Json::encode(['id' => $id1, 'tenant_id' => Yii::$app->tenant->id]),
                ],
                'allCheck' => false,
            ]),
            $searchModel->formName() => 1,
        ];
        $expectedModel = ApplicationMasterSearch::findOne($id1);
        // deleteSearchで取得したmodelの内容検証
        $deleteModels = $searchModel->deleteSearch($post);
        $deleteModel = $deleteModels[0];
        verify($deleteModel->attributes)->equals($expectedModel->attributes);
        // 削除
        $deleteCount = $searchModel->backupAndDelete($deleteModels);
        // 削除件数の検証
        verify($deleteCount)->equals(1);
        // backupAndDelete後にbackupテーブルに入っているレコードの内容の検証
        $this->tester->canSeeInDatabase('application_master_backup', $expectedModel->attributes);

        // allCheckあり、1,2のみ未選択（削除件数のみ検証）
        $post = [
            'gridData' => Json::encode([
                'searchParams' => ['ApplicationMasterSearch' => 1],
                'totalCount' => ApplicationMasterFixture::RECORDS_PER_TENANT,
                'selected' => [
                    Json::encode(['id' => $id1, 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $id2, 'tenant_id' => Yii::$app->tenant->id]),
                ],
                'allCheck' => true,
            ]),
            'ApplicationMasterSearch' => 1,
        ];
        $deleteModels = $searchModel->deleteSearch($post);
        $deleteCount = $searchModel->backupAndDelete($deleteModels);
        verify($deleteCount)->equals(ApplicationMasterFixture::RECORDS_PER_TENANT - 2);

        // 削除したのを元に戻す
        self::getFixtureInstance('application_master')->load();
        self::getFixtureInstance('application_master_backup')->load();
    }

    /**
     * getCorpMasterIdのテスト
     */
    public function testGetCorpMasterId()
    {
        $i = $this->id(100, 'application_master');
        $application = ApplicationMaster::findOne(['id' => $i]);
        $job = $application->jobMaster;
        $client = $job->clientMaster;
        // 求人原稿idからの取得
        $model = new ApplicationMasterSearch();
        $model->job_master_id = $application->job_master_id;
        verify($model->corpMasterId)->equals($client->corp_master_id);
        // 掲載企業idからの取得
        $model = new ApplicationMasterSearch();
        $model->clientMasterId = $job->client_master_id;
        verify($model->corpMasterId)->equals($client->corp_master_id);
    }

    /**
     * setCorpMasterIdのテスト
     */
    public function testSetCorpMasterId()
    {
        $model = new ApplicationMasterSearch();
        $corpMasterId = $this->id(2, 'corp_master');

        $model->corpMasterId = $corpMasterId;
        verify($model->corpMasterId)->equals($corpMasterId);
    }

    /**
     * getClientMasterIdのテスト
     */
    public function testGetClientMasterId()
    {
        $i = $this->id(100, 'application_master');
        $application = ApplicationMaster::findOne(['id' => $i]);
        $job = $application->jobMaster;

        $model = new ApplicationMasterSearch();
        $model->job_master_id = $application->job_master_id;
        verify($model->clientMasterId)->equals($job->client_master_id);
    }

    /**
     * setClientMasterIdのテスト
     */
    public function testSetClientMasterId()
    {
        $model = new ApplicationMasterSearch();
        $clientMasterId = $this->id(25, 'client_master');

        $model->clientMasterId = $clientMasterId;
        verify($model->clientMasterId)->equals($clientMasterId);
    }
    
    /**
     * getDeleted_atのテスト
     */
    public function testGetDeleted_at()
    {
        $model = new ApplicationMasterSearch();
        verify($model->deleted_at)->equals(time());
    }
}