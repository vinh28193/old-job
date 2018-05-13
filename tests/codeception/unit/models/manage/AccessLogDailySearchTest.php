<?php
namespace models\manage;

use app\models\manage\AccessLog;
use app\models\manage\AccessLogDailySearch;
use app\modules\manage\models\Manager;
use tests\codeception\unit\fixtures\AccessLogFixture;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use tests\codeception\unit\fixtures\CorpMasterFixture;
use Yii;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @property AccessLogFixture $application_master
 * @property JobMasterFixture $job_master
 * @property ClientMasterFixture $client_master
 * @property CorpMasterFixture $corp_master
 */
class AccessLogDailySearchTest extends JmTestCase
{
    /**
     * rules test
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new AccessLogDailySearch();
            $model->load([
                $model->formName() => [
                    'clientMasterId' => '文字列',
                    'corpMasterId' => '文字列',
                    'accessMonth' => '文字列',
                    'prefId' => '文字列',
                    'jobNo' => '文字列',
                    ],
            ]);
            $model->validate();
            verify($model->hasErrors('clientMasterId'))->true();
            verify($model->hasErrors('corpMasterId'))->true();
            verify($model->hasErrors('accessMonth'))->true();
            verify($model->hasErrors('prefId'))->true();
            verify($model->hasErrors('jobNo'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new AccessLogDailySearch();
            $model->load([
                $model->formName() => [
                    'clientMasterId' => 1,
                    'corpMasterId' => 1,
                    'accessMonth' => 1,
                    'prefId' => 1,
                    'jobNo' => 1,
                    ],
            ]);
            verify($model->validate())->true();
        });
    }

    /**
     * 検索テスト(運営元)
     * todo all検索テスト実装
     */
    public function testSearchByOwner()
    {
        $this->setIdentity('owner_admin');

        $this->specify('初期状態で検索', function () {
            $searchParam = [
                'clientMasterId' => '',
                'corpMasterId' => '',
                'accessMonth' => 1,
                'prefId' => '',
                'jobNo' => '',
            ];
            $models = $this->getAccessLog($searchParam);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessDate'))->notEmpty();
                // 日付ごとの集約データを確認する
                $this->checkData($model, $searchParam);
            }
        });
        $this->specify('クリアボタン押下時', function () {
            $models = $this->getAccessLog(1);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessDate'))->notEmpty();
                // 日付ごとの集約データを確認する
                $this->checkData($model, 1);
            }
        });

        $this->specify('都道府県で検索', function () {
            $searchParam = [
                'clientMasterId' => '',
                'corpMasterId' => '',
                'accessMonth' => 1,
                'prefId' => 1,
                'jobNo' => '',
            ];
            $models = $this->getAccessLog($searchParam);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessDate'))->notEmpty();
                // 日付ごとの集約データを確認する
                $this->checkData($model, $searchParam);
            }
        });

        $this->specify('仕事ID', function () {
            $searchParam = [
                'clientMasterId' => '',
                'corpMasterId' => '',
                'accessMonth' => 1,
                'prefId' => '',
                'jobNo' => 12,
            ];
            $models = $this->getAccessLog($searchParam);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessDate'))->notEmpty();
                // 日付ごとの集約データを確認する
                $this->checkData($model, $searchParam);
            }
        });

        $this->specify('代理店で検索', function () {
            $searchParam = [
                'clientMasterId' => '',
                'corpMasterId' => 1,
                'accessMonth' => 1,
                'prefId' => '',
                'jobNo' => '',
            ];
            $models = $this->getAccessLog($searchParam);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessDate'))->notEmpty();
                // 日付ごとの集約データを確認する
                $this->checkData($model, $searchParam);
            }
        });

        $this->specify('掲載企業で検索', function () {
            $searchParam = [
                'clientMasterId' => 7,
                'corpMasterId' => '',
                'accessMonth' => 1,
                'prefId' => '',
                'jobNo' => '',
            ];
            $models = $this->getAccessLog($searchParam);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessDate'))->notEmpty();
                // 日付ごとの集約データを確認する
                $this->checkData($model, $searchParam);
            }
        });

        $this->specify('アクセス月で検索', function () {
            $searchParam = [
                'clientMasterId' => '',
                'corpMasterId' => '',
                'accessMonth' => 2,
                'prefId' => '',
                'jobNo' => '',
            ];
            $models = $this->getAccessLog($searchParam);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessDate'))->notEmpty();
                // 日付ごとの集約データを確認する
                $this->checkData($model, $searchParam);
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
            $searchParam = [
                'clientMasterId' => '',
                'corpMasterId' => $corpMasterId,
                'accessMonth' => 1,
                'prefId' => '',
                'jobNo' => '',
            ];
            $models = $this->getAccessLog($searchParam);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessDate'))->notEmpty();
                // 日付ごとの集約データを確認する
                $this->checkData($model, $searchParam);
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
            $searchParam = [
                'clientMasterId' => $clientMasterId,
                'corpMasterId' => '',
                'accessMonth' => 1,
                'prefId' => '',
                'jobNo' => '',
            ];
            $models = $this->getAccessLog($searchParam);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'accessDate'))->notEmpty();
                // 日付ごとの集約データを確認する
                $this->checkData($model, $searchParam);
            }
        });
    }

    /**
     * getAccessMonthListのテスト
     */
    public function testGetAccessMonthList()
    {
        $accessMonthList = AccessLogDailySearch::getAccessMonthList();
        verify($accessMonthList[AccessLogDailySearch::CURRENT_MONTH])->equals('今月');
        verify($accessMonthList[AccessLogDailySearch::BEFORE_ONE_MONTH])->equals('先月');
        verify($accessMonthList[AccessLogDailySearch::BEFORE_TWO_MONTH])->equals('先々月');
    }

    /**
     * accessLogテーブルのデータ取得
     * @param $searchParam
     * @return array
     */
    private function getAccessLog($searchParam)
    {
        $model = new AccessLogDailySearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);

        return $dataProvider->query->all();
    }

    /**
     * 日付ごとの、ページ別の集計チェックを行う
     * @param $data AccessLog
     * @param $searchParam array
     */
    private function checkData($data, $searchParam)
    {
        $allRecords = self::getFixtureInstance('access_log')->data();
        $accessDate = date('Y/m/d', strtotime($data->accessDate));

        // 全国TOP,エリアTOPは、運営元管理者のみ取得する
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        if ($identity->myRole == Manager::OWNER_ADMIN) {
            // 全国TOP_PC
            $targetRecords = array_filter($allRecords, function ($record) use ($accessDate) {
                return $record['tenant_id'] == Yii::$app->tenant->id
                && $record['search_date'] == $accessDate
                && $record['carrier_type'] == 0
                && $record['access_url'] == Url::to('/', true);
            });
            verify($data->zenkokuPc)->equals(count($targetRecords));

            // 全国TOP_スマートフォン
            $targetRecords = array_filter($allRecords, function ($record) use ($accessDate) {
                return $record['tenant_id'] == Yii::$app->tenant->id
                && $record['search_date'] == $accessDate
                && $record['carrier_type'] == 1
                && $record['access_url'] == Url::to('/', true);
            });
            verify($data->zenkokuSp)->equals(count($targetRecords));

            // エリアTOP_PC
            $targetRecords = array_filter($allRecords, function ($record) use ($accessDate) {
                return $record['tenant_id'] == Yii::$app->tenant->id
                && $record['search_date'] == $accessDate
                && $record['carrier_type'] == 0
                && $record['access_url'] != Url::to('/', true)
                && empty($record['job_master_id']);
            });
            verify($data->areaPc)->equals(count($targetRecords));

            // エリアTOP_スマートフォン
            $targetRecords = array_filter($allRecords, function ($record) use ($accessDate) {
                return $record['tenant_id'] == Yii::$app->tenant->id
                && $record['search_date'] == $accessDate
                && $record['carrier_type'] == 1
                && $record['access_url'] != Url::to('/', true)
                && empty($record['job_master_id']);
            });
            verify($data->areaSp)->equals(count($targetRecords));
        } else {
            // 運営元管理者以外はnullとなる
            verify(empty($data->zenkokuPc))->true();
            verify(empty($data->zenkokuSp))->true();
            verify(empty($data->areaPc))->true();
            verify(empty($data->areaSp))->true();
        }

        // それぞれの検索条件で、job_master_idを取得する
        $jobMasterIds['pref_id'] = $this->getJobMasterIdsByPrefId($searchParam['prefId']);
        $jobMasterIds['corp_master_id'] = $this->getJobMasterIdsByCorpMasterId($searchParam['corpMasterId']);
        $jobMasterIds['client_master_id'] = $this->getJobMasterIdsByClientMasterId($searchParam['clientMasterId']);
        $jobMasterIds['job_no'] = $this->getJobMasterIdsByJobNo($searchParam['jobNo']);

        // 求人詳細_PC
        $targetRecords = array_filter($allRecords, function ($record) use ($accessDate, $jobMasterIds) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['search_date'] == $accessDate
            && $record['carrier_type'] == 0
            && !empty($record['job_master_id'])
            && empty($record['application_master_id'])
            && (empty($jobMasterIds['pref_id']) || in_array($record['job_master_id'], $jobMasterIds['pref_id']))
            && (empty($jobMasterIds['corp_master_id']) || in_array($record['job_master_id'], $jobMasterIds['corp_master_id']))
            && (empty($jobMasterIds['client_master_id']) || in_array($record['job_master_id'], $jobMasterIds['client_master_id']))
            && (empty($jobMasterIds['job_no']) || in_array($record['job_master_id'], $jobMasterIds['job_no']));
        });
        verify($data->jobPc)->equals(count($targetRecords));

        // 求人詳細_スマートフォン
        $targetRecords = array_filter($allRecords, function ($record) use ($accessDate, $jobMasterIds) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['search_date'] == $accessDate
            && $record['carrier_type'] == 1
            && !empty($record['job_master_id'])
            && empty($record['application_master_id'])
            && (empty($jobMasterIds['pref_id']) || in_array($record['job_master_id'], $jobMasterIds['pref_id']))
            && (empty($jobMasterIds['corp_master_id']) || in_array($record['job_master_id'], $jobMasterIds['corp_master_id']))
            && (empty($jobMasterIds['client_master_id']) || in_array($record['job_master_id'], $jobMasterIds['client_master_id']))
            && (empty($jobMasterIds['job_no']) || in_array($record['job_master_id'], $jobMasterIds['job_no']));
        });
        verify($data->jobSp)->equals(count($targetRecords));

        // 応募完了データはapplication_masterテーブルから取得する
        // 一旦応募完了リストの一覧を作成する
        $targetRecords = array_filter($allRecords, function ($record) use ($accessDate, $jobMasterIds) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['search_date'] == $accessDate
            && !empty($record['job_master_id'])
            && !empty($record['application_master_id'])
            && (empty($jobMasterIds['pref_id']) || in_array($record['job_master_id'], $jobMasterIds['pref_id']))
            && (empty($jobMasterIds['corp_master_id']) || in_array($record['job_master_id'], $jobMasterIds['corp_master_id']))
            && (empty($jobMasterIds['client_master_id']) || in_array($record['job_master_id'], $jobMasterIds['client_master_id']))
            && (empty($jobMasterIds['job_no']) || in_array($record['job_master_id'], $jobMasterIds['job_no']));
        });
        $applicationPcCount = 0;
        $applicationSpCount = 0;
        foreach ($targetRecords as $targetRecord) {
            $applicationMaster = $this->findRecordById(self::getFixtureInstance('application_master'), $targetRecord['application_master_id']);
            if (!empty($applicationMaster)) {
                if ($applicationMaster['carrier_type'] == 0) {
                    $applicationPcCount++;
                } else {
                    $applicationSpCount++;
                }
            }
        }

        // 応募完了_PC
        verify($data->applicationPc)->equals($applicationPcCount);

        // 応募完了_スマートフォン
        verify($data->applicationSp)->equals($applicationSpCount);
    }

    /**
     * 都道府県IDに紐づく仕事IDを取得する
     * @param $prefId integer
     * @return array
     */
    private function getJobMasterIdsByPrefId($prefId)
    {
        if (empty($prefId)) {
            return null;
        }
        $searchJobMasterIds = [];
        
        // 都道府県
        $jobPrefs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_pref'), 'pref_id', $prefId);
        foreach ($jobPrefs as $jobPref) {
            if ($jobPref['tenant_id'] == Yii::$app->tenant->id) {
                $searchJobMasterIds[] = $jobPref['job_master_id'];
            }
        }
        return $searchJobMasterIds;
    }

    /**
     * 代理店IDに紐づく仕事IDを取得する
     * @param $corpMasterId integer
     * @return array
     */
    private function getJobMasterIdsByCorpMasterId($corpMasterId)
    {
        if (empty($corpMasterId)) {
            return null;
        }
        $searchJobMasterIds = [];
        
        // 代理店ID
        $clientMasters = $this->findRecordsByForeignKey(self::getFixtureInstance('client_master'), 'corp_master_id', $corpMasterId);
        foreach ($clientMasters as $clientMaster) {
            $jobMasters = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $clientMaster['id']);
            foreach ($jobMasters as $jobMaster) {
                if ($jobMaster['tenant_id'] == Yii::$app->tenant->id) {
                    $searchJobMasterIds[] = $jobMaster['id'];
                }
            }
        }
        return $searchJobMasterIds;
    }

    /**
     * 掲載企業IDに紐づく仕事IDを取得する
     * @param $clientMasterId integer
     * @return array
     */
    private function getJobMasterIdsByClientMasterId($clientMasterId)
    {
        if (empty($clientMasterId)) {
            return null;
        }
        $searchJobMasterIds = [];
        
        // 掲載企業ID
        $clientMasters = $this->findRecordsByForeignKey(self::getFixtureInstance('client_master'), 'id', $clientMasterId);
        foreach ($clientMasters as $clientMaster) {
            $jobMasters = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $clientMaster['id']);
            foreach ($jobMasters as $jobMaster) {
                if ($jobMaster['tenant_id'] == Yii::$app->tenant->id) {
                    $searchJobMasterIds[] = $jobMaster['id'];
                }
            }
        }
        return $searchJobMasterIds;
    }

    /**
     * 仕事NOに紐づく仕事IDを取得する
     * @param $jobNo integer
     * @return array
     */
    private function getJobMasterIdsByJobNo($jobNo)
    {
        if (empty($jobNo)) {
            return null;
        }
        $searchJobMasterIds = [];
        
        // 仕事ID
        $jobMasters = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'job_no', $jobNo);
        foreach ($jobMasters as $jobMaster) {
            if ($jobMaster['tenant_id'] == Yii::$app->tenant->id) {
                $searchJobMasterIds[] = $jobMaster['id'];
            }
        }
        return $searchJobMasterIds;
    }
}
