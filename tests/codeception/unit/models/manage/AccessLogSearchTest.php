<?php
namespace models\manage;

use app\models\manage\AccessLog;
use app\models\manage\AccessLogSearch;
use app\models\manage\JobMaster;
use app\modules\manage\models\Manager;
use tests\codeception\unit\fixtures\AccessLogFixture;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use tests\codeception\unit\fixtures\CorpMasterFixture;
use Yii;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Json;

/**
 * @property AccessLogFixture $application_master
 * @property JobMasterFixture $job_master
 * @property ClientMasterFixture $client_master
 * @property CorpMasterFixture $corp_master
 */
class AccessLogSearchTest extends JmTestCase
{
    /**
     * rules test
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new AccessLogSearch();
            $model->load([
                $model->formName() => [
                    'accessPageId' => '文字列',
                    'carrier_type' => '文字列',
                    'jobNo' => '文字列',
                    ],
            ]);
            $model->validate();
            verify($model->hasErrors('accessPageId'))->true();
            verify($model->hasErrors('carrier_type'))->true();
            verify($model->hasErrors('jobNo'))->true();
        });

        $this->specify('日付チェック', function () {
            $model = new AccessLogSearch();
            $model->load([
                $model->formName() => [
                    'searchStartDate' => '文字列',
                    'searchEndDate' => '文字列',
                    ],
            ]);
            $model->validate();
            verify($model->hasErrors('searchStartDate'))->true();
            verify($model->hasErrors('searchEndDate'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new AccessLogSearch();
            $model->load([
                $model->formName() => [
                    'accessPageId' => 1,
                    'carrier_type' => 1,
                    'jobNo' => 1,
                    'access_url' => 'http://jm2.yii/',
                    'access_user_agent' => 'あああ',
                    'access_referrer' => 'あああ',
                    'searchStartDate' => '1989/03/01',
                    'searchEndDate' => '2016/06/06',
                    ],
            ]);
            verify($model->validate())->true();
        });
    }

    /**
     * 検索テスト（運営元）
     * todo all検索テスト実装
     */
    public function testSearchByOwner()
    {
        $this->setIdentity('owner_admin');

        $this->specify('初期状態で検索', function () {
            $models = $this->getAccessLog([
                'accessPageId' => '',
                'carrier_type' => '',
                'jobNo' => '',
                'access_url' => '',
                'access_user_agent' => '',
                'access_referrer' => '',
                'searchStartDate' => '',
                'searchEndDate' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessed_at'))->notEmpty();
            }
        });

        $this->specify('クリアボタン押下時', function () {
            $models = $this->getAccessLog(1);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessed_at'))->notEmpty();
            }
        });

        // コンポーネントを変数へ
        $tenant = Yii::$app->tenant->tenant;
        $areaComp = Yii::$app->area;
        // 判定に使うURLを変数に格納
        $rootUrl = Url::to('/', true);
        $appliedBaseUrl = Url::to('/apply/complete', true);
        $jobDetailBaseUrl = $rootUrl . $tenant->kyujin_detail_dir;

        $this->specify('アクセスページで検索(全国TOP)', function () use ($rootUrl) {
            $models = $this->getAccessLog([
                'accessPageId' => AccessLogSearch::NATIONWIDE_TOP_PAGE,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'access_url'))->equals($rootUrl);
            }
        });

        $this->specify('アクセスページで検索(地域TOP)', function () use ($rootUrl, $areaComp) {
            $models = $this->getAccessLog([
                'accessPageId' => AccessLogSearch::AREA_TOP_PAGE,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                $accessUrl = (string)ArrayHelper::getValue($model, 'access_url');
                $directories = str_replace($rootUrl, '', $accessUrl);
                $areaDirs = ArrayHelper::getColumn($areaComp->models, 'area_dir');
                verify(in_array($directories, $areaDirs))->true();
            }
        });

        $this->specify('アクセスページで検索(求人詳細)', function () use ($jobDetailBaseUrl) {
            $models = $this->getAccessLog([
                'accessPageId' => AccessLogSearch::JOB_DETAIL_PAGE,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'access_url'))->contains($jobDetailBaseUrl);
            }
        });

        $this->specify('アクセスページで検索(応募完了)', function () use ($appliedBaseUrl) {
            $models = $this->getAccessLog([
                'accessPageId' => AccessLogSearch::APPLIED_PAGE,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'access_url'))->contains($appliedBaseUrl);
            }
        });

        $this->specify('jobIdで検索', function () {
            $models = $this->getAccessLog([
                'jobNo' => strval(12),
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'jobMaster.job_no'))->contains('12');
            }
        });

        $this->specify('アクセスURLで検索', function () {
            // 実在するものを取得
            $sample = AccessLog::findOne($this->id(1, 'access_log'));
            $models = $this->getAccessLog([
                'access_url' => $sample->access_url,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'access_url'))->equals($sample->access_url);
            }
        });

        $this->specify('ユーザーエージェントで検索', function () {
            // 実在するものを取得
            $sample = AccessLog::findOne($this->id(1, 'access_log'));
            $models = $this->getAccessLog([
                'access_user_agent' => $sample->access_user_agent,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'access_user_agent'))->equals($sample->access_user_agent);
            }
        });

        $this->specify('リファラーで検索', function () {
            // 実在するものを取得
            $sample = AccessLog::findOne($this->id(1, 'access_log'));
            $models = $this->getAccessLog([
                'access_referrer' => $sample->access_referrer,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'access_referrer'))->equals($sample->access_referrer);
            }
        });

        $this->specify('アクセス日時で検索', function () {
            $models = $this->getAccessLog([
                'searchStartDate' => '2017/07/10',
                'searchEndDate' => '2020/01/31',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessed_at'))->greaterOrEquals(strtotime('2017-07-10'));
                verify(ArrayHelper::getValue($model, 'accessed_at'))->lessOrEquals(strtotime('2020-01-31'));
            }
        });

        $this->specify('応募機器で検索', function () {
            $models = $this->getAccessLog([
                'carrier_type' => 0,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'carrier_type'))->equals(0);
            }
        });
    }

    /**
     * 検索テスト(代理店)
     * todo all検索テスト実装
     */
    public function testSearchByCorp()
    {
        $this->setIdentity('corp_admin');
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        $corpMasterId = $identity->corp_master_id;

        $this->specify('初期状態で検索', function () use ($corpMasterId) {
            $models = $this->getAccessLog([
                'accessPageId' => '',
                'carrier_type' => '',
                'jobNo' => '',
                'access_url' => '',
                'access_user_agent' => '',
                'access_referrer' => '',
                'searchStartDate' => '',
                'searchEndDate' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'jobMaster'))->isInstanceOf(JobMaster::className());
                verify(ArrayHelper::getValue($model, 'jobMaster.clientMaster.corp_master_id'))->equals($corpMasterId);
            }
        });
    }

    /**
     * 検索テスト(掲載企業)
     * todo all検索テスト実装
     */
    public function testSearchByClient()
    {
        $this->setIdentity('client_admin');
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        $clientMasterId = $identity->client_master_id;

        $this->specify('初期状態で検索', function () use ($clientMasterId) {
            $models = $this->getAccessLog([
                'accessPageId' => '',
                'carrier_type' => '',
                'jobNo' => '',
                'access_url' => '',
                'access_user_agent' => '',
                'access_referrer' => '',
                'searchStartDate' => '',
                'searchEndDate' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'jobMaster'))->isInstanceOf(JobMaster::className());
                verify(ArrayHelper::getValue($model, 'jobMaster.client_master_id'))->equals($clientMasterId);
            }
        });
    }

    private function getAccessLog($searchParam)
    {
        $model = new AccessLogSearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);

        return $dataProvider->query->all();
    }

    /**
     * CSVダウンロードテスト
     */
    public function testCsvSearch()
    {
        $this->setIdentity('owner_admin');
        $searchModel = new AccessLogSearch();
        // allCheck無し、id=1～5選択
        $id1 = $this->id(1, 'access_log');
        $id2 = $this->id(2, 'access_log');
        $id3 = $this->id(3, 'access_log');
        $id4 = $this->id(4, 'access_log');
        $id5 = $this->id(5, 'access_log');

        $post = [
            'gridData' => Json::encode([
                'searchParams' => [$searchModel->formName() => 1],
                'totalCount' => AccessLogFixture::RECORDS_PER_TENANT,
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
        /** @var AccessLogSearch[] $models */
        $models = ArrayHelper::index($dataProvider->models, 'id');
        /** @var AccessLogSearch[] $expectedModels */
        $expectedModels = ArrayHelper::index(AccessLogSearch::find()->where(['in', 'id', [$id1, $id2, $id3, $id4, $id5]])->all(), 'id');
        // 数と内容を検証
        verify($dataProvider->totalCount)->equals(5);
        verify($models[$id1]->attributes)->equals($expectedModels[$id1]->attributes);
        verify($models[$id2]->attributes)->equals($expectedModels[$id2]->attributes);
        verify($models[$id3]->attributes)->equals($expectedModels[$id3]->attributes);
        verify($models[$id4]->attributes)->equals($expectedModels[$id4]->attributes);
        verify($models[$id5]->attributes)->equals($expectedModels[$id5]->attributes);
    }

    /**
     * getAccessPageNameのテスト
     */
    public function testGetAccessPageName()
    {
        // コンポーネントを変数へ
        $tenant = Yii::$app->tenant->tenant;
        $areaComp = Yii::$app->area;
        // 判定に使うURLを変数に格納
        $rootUrl = Url::to('/', true);
        $appliedBaseUrl = Url::to('/apply/complete', true);
        $jobDetailBaseUrl = $rootUrl . $tenant->kyujin_detail_dir;
        
        $accessPageNames = AccessLogSearch::accessPageNames();

        // 全国TOP検証
        $model = new AccessLogSearch();
        $model->access_url = $rootUrl;
        verify($model->accessPageName)->equals($accessPageNames[AccessLogSearch::NATIONWIDE_TOP_PAGE]);

        // 地域TOP検証
        $areaDirs = ArrayHelper::getColumn($areaComp->models, 'area_dir');
        $model = new AccessLogSearch();
        $model->access_url = $rootUrl . $areaDirs[0];
        verify($model->accessPageName)->equals($accessPageNames[AccessLogSearch::AREA_TOP_PAGE]);

        // 求人詳細検証
        $model = new AccessLogSearch();
        $model->access_url = $jobDetailBaseUrl;
        verify($model->accessPageName)->equals($accessPageNames[AccessLogSearch::JOB_DETAIL_PAGE]);

        // 応募完了検証
        $model = new AccessLogSearch();
        $model->access_url = $appliedBaseUrl;
        verify($model->accessPageName)->equals($accessPageNames[AccessLogSearch::APPLIED_PAGE]);
    }

    /**
     * isRootPageのテスト
     */
    public function testIsRootPage()
    {
        // 判定に使うURLを変数に格納
        $rootUrl = Url::to('/', true);
        $model = new AccessLogSearch();
        $model->access_url = $rootUrl;
        verify($model->isRootPage())->true();

        $model->access_url = $rootUrl . '/test';
        verify($model->isRootPage())->false();
    }

    /**
     * isAreaTopPageのテスト
     */
    public function testIsAreaTopPage()
    {
        // 判定に使うURLを変数に格納
        $rootUrl = Url::to('/', true);
        $areaComp = Yii::$app->area;
        $areaDirs = ArrayHelper::getColumn($areaComp->models, 'area_dir');
        $model = new AccessLogSearch();
        $model->access_url = $rootUrl . $areaDirs[0];
        verify($model->isAreaTopPage())->true();

        $model->access_url = $rootUrl . 'test';
        verify($model->isAreaTopPage())->false();
    }

    /**
     * isJobDetailPageのテスト
     */
    public function testIsJobDetailPage()
    {
        // コンポーネントを変数へ
        $tenant = Yii::$app->tenant->tenant;
        // 判定に使うURLを変数に格納
        $rootUrl = Url::to('/', true);
        $jobDetailBaseUrl = $rootUrl . $tenant->kyujin_detail_dir;

        $model = new AccessLogSearch();
        $model->access_url = $jobDetailBaseUrl;
        verify($model->isJobDetailPage())->true();

        $model->access_url = $rootUrl . 'test';
        verify($model->isJobDetailPage())->false();
    }

    /**
     * isAppliedPageのテスト
     */
    public function testIsAppliedPage()
    {
        // コンポーネントを変数へ
        $rootUrl = Url::to('/', true);
        $appliedBaseUrl = Url::to('/apply/complete', true);

        $model = new AccessLogSearch();
        $model->access_url = $appliedBaseUrl;
        verify($model->isAppliedPage())->true();

        $model->access_url = $rootUrl . 'test';
        verify($model->isAppliedPage())->false();
    }

    /**
     * accessPageArrayのテスト
     */
    public function testAccessPageArrayByOwner()
    {
        $this->setIdentity('owner_admin');
        $accessPageArray = AccessLogSearch::accessPageArray();
        $accessPageNames = AccessLogSearch::accessPageNames();
        verify(in_array($accessPageNames[AccessLogSearch::NATIONWIDE_TOP_PAGE], $accessPageArray))->true();
        verify(in_array($accessPageNames[AccessLogSearch::ONE_AREA_TOP_PAGE], $accessPageArray))->false();
        verify(in_array($accessPageNames[AccessLogSearch::AREA_TOP_PAGE], $accessPageArray))->true();
        verify(in_array($accessPageNames[AccessLogSearch::JOB_DETAIL_PAGE], $accessPageArray))->true();
        verify(in_array($accessPageNames[AccessLogSearch::APPLIED_PAGE], $accessPageArray))->true();
    }

    /**
     * accessPageArrayのテスト
     */
    public function testAccessPageArrayByCorp()
    {
        $this->setIdentity('corp_admin');
        $accessPageArray = AccessLogSearch::accessPageArray();
        $accessPageNames = AccessLogSearch::accessPageNames();
        verify(in_array($accessPageNames[AccessLogSearch::NATIONWIDE_TOP_PAGE], $accessPageArray))->false();
        verify(in_array($accessPageNames[AccessLogSearch::ONE_AREA_TOP_PAGE], $accessPageArray))->false();
        verify(in_array($accessPageNames[AccessLogSearch::AREA_TOP_PAGE], $accessPageArray))->false();
        verify(in_array($accessPageNames[AccessLogSearch::JOB_DETAIL_PAGE], $accessPageArray))->true();
        verify(in_array($accessPageNames[AccessLogSearch::APPLIED_PAGE], $accessPageArray))->true();
    }
}
