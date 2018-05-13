<?php

namespace models\manage;

use app\models\manage\ClientMaster;
use app\models\manage\JobMasterSearch;
use app\models\manage\JobReviewStatus;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\JobDist;
use app\models\manage\searchkey\JobSearchkeyItem1;
use app\models\manage\searchkey\JobType;
use app\models\manage\searchkey\JobWage;
use app\models\manage\searchkey\SearchkeyCategory1;
use app\models\manage\searchkey\SearchkeyItem1;
use app\models\manage\searchkey\WageCategory;
use app\models\manage\searchkey\WageItem;
use app\models\manage\SearchkeyMaster;
use proseeds\base\Tenant;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\JmTestCase;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @group job_master
 */
class JobMasterSearchTest extends JmTestCase
{
    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->setIdentity('owner_admin');
        $this->specify('数字チェック', function () {
            $model = new JobMasterSearch();
            $model->load([
                $model->formName() => [
                    'corpMasterId' => '文字列',
                    'client_master_id' => '文字列',
                    'client_charge_plan_id' => '文字列',
                    'job_review_status_id' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('corpMasterId'))->true();
            verify($model->hasErrors('client_master_id'))->true();
            verify($model->hasErrors('client_charge_plan_id'))->true();
            verify($model->hasErrors('job_review_status_id'))->true();
        });

        $this->specify('日付チェック', function () {
            $model = new JobMasterSearch();
            $model->load([
                $model->formName() => [
                    'startFrom' => '文字列',
                    'startTo' => [4],
                    'endFrom' => [1, 2, 3],
                    'endTo' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('startFrom'))->true();
            verify($model->hasErrors('startTo'))->true();
            verify($model->hasErrors('endFrom'))->true();
            verify($model->hasErrors('endTo'))->true();
        });

        $this->specify('booleanチェック', function () {
            $model = new JobMasterSearch();
            $model->load([
                $model->formName() => [
                    'isDisplay' => '文字列',
                    'valid_chk' => 1812,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('isDisplay'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('文字列チェック', function () {
            $model = new JobMasterSearch();
            $model->load([
                $model->formName() => [
                    'searchItem' => 0222,
                    'searchText' => [1, 2, 3],
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('searchItem'))->true();
            verify($model->hasErrors('searchText'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new JobMasterSearch();
            $model->load([
                $model->formName() => [
                    'searchItem' => 'all',
                    'searchText' => 'you',
                    'corpMasterId' => 2,
                    'client_master_id' => 3,
                    'client_charge_plan_id' => 4,
                    'isDisplay' => 1,
                    'startFrom' => '1999/02/03',
                    'startTo' => '2011/09/23',
                    'endFrom' => '2012/01/17',
                    'endTo' => '2017/11/11',
                    'valid_chk' => '1',
                    'job_review_status_id' => 6,
                ],
            ]);
            verify($model->validate())->true();
        });
    }

    /**
     * ラベルテスト
     */
    public function testAttributeLabels()
    {
        $jobMaster = new JobMasterSearch();
        verify(count($jobMaster->attributeLabels()))->notEmpty();
    }

    /**
     * 検索テスト
     * 手間がかかりすぎるので一旦はキーワード検索のallは検証していません。
     * todo all検索テスト実装
     */
    public function testSearchByOwner()
    {
        $this->setIdentity('owner_admin');
        $this->specify('初期状態で検索', function () {
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'searchText' => '',
                'corpMasterId' => '',
                'client_master_id' => '',
                'client_charge_plan_id' => '',
                'isDisplay' => '',
                'startFrom' => '',
                'startTo' => '',
                'endFrom' => '',
                'endTo' => '',
                'valid_chk' => '',
                'job_review_status_id' => '',
            ]);
            verify($models)->notEmpty();
            verify($models)->count(JobMasterFixture::RECORDS_PER_TENANT + 1);
        });

        $this->specify('クリアボタン押下時', function () {
            $models = $this->getJobMaster(1);
            verify($models)->notEmpty();
            verify($models)->count(JobMasterFixture::RECORDS_PER_TENANT + 1);
        });

        $this->specify('キーワードで検索(job_no)', function () {
            $models = $this->getJobMaster([
                'searchItem' => 'job_no',
                'searchText' => '2',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'job_no'))->contains('2');
            }
        });

        $this->specify('代理店で検索', function () {
            $id = $this->id(1, 'corp_master');
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'corpMasterId' => $id,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'clientMaster.corp_master_id'))->equals($id);
            }
        });

        $this->specify('掲載企業で検索', function () {
            $id = ClientMaster::find()->select('id')->scalar();
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'client_master_id' => $id,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'client_master_id'))->equals($id);
            }
        });

        $this->specify('プランで検索', function () {
            $id = $this->id(4, 'client_charge_plan');
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'client_charge_plan_id' => $id,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'client_charge_plan_id'))->equals($id);
            }
        });

        $this->specify('掲載状況で検索', function () {
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'isDisplay' => JobMasterSearch::DISP_VALID,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify($model->valid_chk && $model->job_review_status_id == JobReviewStatus::STEP_REVIEW_OK && $model->clientMaster->valid_chk && $model->clientMaster->corpMaster->valid_chk && $model->clientChargePlan->valid_chk && $model->clientChargePlan->dispType->valid_chk && $model->disp_start_date <= time() && ($model->disp_end_date >= strtotime('today') || $model->disp_end_date == null))->true();
            }

            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'isDisplay' => JobMasterSearch::DISP_INVALID,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify($model->valid_chk && $model->job_review_status_id == JobReviewStatus::STEP_REVIEW_OK && $model->clientMaster->valid_chk && $model->clientMaster->corpMaster && $model->clientMaster->corpMaster->valid_chk && $model->clientChargePlan->valid_chk && $model->clientChargePlan->dispType->valid_chk && $model->disp_start_date <= time() && ($model->disp_end_date >= strtotime('today') || $model->disp_end_date == null))->false();
            }
        });

        $this->specify('掲載開始・終了日で検索', function () {
            // 掲載開始日起点
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'startFrom' => '2016/07/07',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'disp_start_date'))->greaterOrEquals(strtotime('2016/07/07'));
            }
            // 掲載開始日終点
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'startTo' => '2016/07/07',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'disp_start_date'))->lessOrEquals(strtotime('2016/07/07'));
            }
            // 掲載終了日起点
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'endFrom' => '2018/07/07',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'disp_end_date'))->greaterOrEquals(strtotime('2018/07/07'));
            }
            // 掲載終了日終点
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'endTo' => '2018/07/07',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'disp_end_date'))->lessOrEquals(strtotime('2018/07/07'));
            }
        });

        $this->specify('有効無効で検索', function () {
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'valid_chk' => JobMasterSearch::FLAG_VALID,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'valid_chk'))->equals(JobMasterSearch::FLAG_VALID);
            }
        });

        $this->specify('審査ステータスで検索', function () {
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'job_review_status_id' => JobReviewStatus::STEP_REVIEW_OK,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'job_review_status_id'))->equals(JobReviewStatus::STEP_REVIEW_OK);
            }
        });
    }

    public function testSearchByCorp()
    {
        $this->setIdentity('corp_admin');
        $this->specify('初期状態で検索', function () {
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'searchText' => '',
                'corpMasterId' => '',
                'client_master_id' => '',
                'client_charge_plan_id' => '',
                'isDisplay' => '',
                'startFrom' => '',
                'startTo' => '',
                'endFrom' => '',
                'endTo' => '',
                'valid_chk' => '',
                'job_review_status_id' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify($model->clientMaster->corp_master_id)->equals($this->getIdentity()->corp_master_id);
            }
        });
    }

    public function testSearchByClient()
    {
        $this->setIdentity('client_admin');
        $this->specify('初期状態で検索', function () {
            $models = $this->getJobMaster([
                'searchItem' => 'all',
                'searchText' => '',
                'corpMasterId' => '',
                'client_master_id' => '',
                'client_charge_plan_id' => '',
                'isDisplay' => '',
                'startFrom' => '',
                'startTo' => '',
                'endFrom' => '',
                'endTo' => '',
                'valid_chk' => '',
                'job_review_status_id' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify($model->client_master_id)->equals($this->getIdentity()->client_master_id);
            }
        });
    }

    /**
     * @param $searchParam
     * @return JobMasterSearch[]
     */
    private function getJobMaster($searchParam)
    {
        $model = new JobMasterSearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);

        return $dataProvider->query->all();
    }

    /**
     * CSV用検索テスト
     * 検索のテストはsearchのテストで担保されている
     */
    public function testCsvSearch()
    {
        $this->setIdentity('owner_admin');
        $searchModel = new JobMasterSearch();
        $params = [
            'gridData' => Json::encode([
                'searchParams' => [$searchModel->formName() => 1],
                'totalCount' => JobMasterFixture::RECORDS_PER_TENANT,
                'selected' => [
                    Json::encode(['id' => $this->id(1, 'job_master'), 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $this->id(2, 'job_master'), 'tenant_id' => Yii::$app->tenant->id]),
                ],
                'allCheck' => true,
            ]),
            $searchModel->formName() => 1,
        ];
        $dataProvider = $searchModel->csvSearch($params);
        // ページング処理の検証
        verify($dataProvider->query->count())->equals(JobMasterFixture::RECORDS_PER_TENANT - 2);
        // withの検証
        $with = $dataProvider->query->with;
        // 固定のもの
        verify(array_pop($with))->contains('mediaUpload5');
        verify(array_pop($with))->contains('mediaUpload4');
        verify(array_pop($with))->contains('mediaUpload3');
        verify(array_pop($with))->contains('mediaUpload2');
        verify(array_pop($with))->contains('mediaUpload1');
        verify(array_pop($with))->contains('clientChargePlan');
        verify(array_pop($with))->contains('clientMaster.corpMaster');
        // 検索キーのもの
        foreach ($with as $withString) {
            switch ($withString) {
                case 'jobDist.dist':
                    $this->verifyValidChkOfSearchKey('job_dist');
                    break;
                case 'jobStation.station':
                    $this->verifyValidChkOfSearchKey('job_station_info');
                    break;
                case 'jobWage.wageItem':
                    $this->verifyValidChkOfSearchKey('job_wage');
                    break;
                case 'jobType.jobTypeSmall':
                    $this->verifyValidChkOfSearchKey('job_type');
                    break;
                default:
                    $searchKeyNo = str_replace(['jobSearchkeyItem', '.searchKeyItem'], ['', ''], $withString);
                    $this->verifyValidChkOfSearchKey('job_searchkey_item' . $searchKeyNo);
                    break;
            }
        }
    }

    /**
     * 検索キーが有効かどうかを検証する
     * @param $tableName
     */
    private function verifyValidChkOfSearchKey($tableName)
    {
        return verify(SearchkeyMaster::find()->select(['valid_chk'])->where([
            'job_relation_table' => $tableName,
            'tenant_id' => Yii::$app->tenant->id,
        ])->scalar())->equals(1);
    }

    /**
     * deleteSearchメソッドとbackupAndDeleteメソッドのテスト
     * 検索のテストはsearchのテストで担保されている
     */
    public function testDelete()
    {
        /** @var Tenant $tenant */
        $tenant = Yii::$app->tenant;
        $this->setIdentity('owner_admin');
        $searchModel = new JobMasterSearch();
        // allCheck無し、id=1のみ選択
        $post = [
            'gridData' => Json::encode([
                'searchParams' => [$searchModel->formName() => 1],
                'totalCount' => JobMasterFixture::RECORDS_PER_TENANT,
                'selected' => [
                    Json::encode(['id' => $this->id(1, 'job_master'), 'tenant_id' => $tenant->id]),
                ],
                'allCheck' => false,
            ]),
            $searchModel->formName() => 1,
        ];
        $expectedModel = JobMasterSearch::findOne($this->id(1, 'job_master'));
        // deleteSearchで取得したmodelの内容検証
        $deleteModels = $searchModel->deleteSearch($post);
        $deleteModel = $deleteModels[0];
        verify($deleteModel->attributes)->equals($expectedModel->attributes);
        // 削除
        $deleteCount = $searchModel->backupAndDelete($deleteModels);
        // 削除件数の検証
        verify($deleteCount)->equals(1);
        // backupAndDelete後にbackupテーブルに入っているレコードの内容の検証
        $this->tester->canSeeInDatabase('job_master_backup', $expectedModel->attributes);

        // allCheckあり、1,2のみ未選択（ページングチェックも兼ねて削除件数のみ検証）
        $post = [
            'gridData' => Json::encode([
                'searchParams' => [$searchModel->formName() => 1],
                'totalCount' => JobMasterFixture::RECORDS_PER_TENANT,
                'selected' => [
                    Json::encode(['id' => $this->id(1, 'job_master'), 'tenant_id' => $tenant->id]),
                    Json::encode(['id' => $this->id(2, 'job_master'), 'tenant_id' => $tenant->id]),
                ],
                'allCheck' => true,
            ]),
            $searchModel->formName() => 1,
        ];
        $deleteModels = $searchModel->deleteSearch($post);
        $deleteCount = $searchModel->backupAndDelete($deleteModels);
        verify($deleteCount)->equals(JobMasterFixture::RECORDS_PER_TENANT - 2);

        // testで削除した原稿を元の状態に戻す
        static::getFixtureInstance('job_master')->initTable();
        static::getFixtureInstance('job_master_backup')->initTable();
    }

    /**
     * getColumnNameのtest
     */
    public function testGetColumnName()
    {
        verify(JobMasterSearch::getColumnName('client_master_id'))->equals('clientMaster.client_name');
        verify(JobMasterSearch::getColumnName('corpLabel'))->equals('clientMaster.corpMaster.corp_name');
        verify(JobMasterSearch::getColumnName('client_charge_plan_id'))->equals('clientChargePlan.plan_name');
        verify(JobMasterSearch::getColumnName('job_review_status_id'))->notEmpty();
        verify(JobMasterSearch::getColumnName('test'))->equals('test');
    }

//    /** 実際の出力の内容は、画面確認した方が早くかつ確実なので、省略 **/
//    public function testCsvAttribute()    {}

    /**
     * 掲載状況リスト取得テスト
     */
    public function testGetDispStatusList()
    {
        verify(JobMasterSearch::getDispStatusList())->count(3);
    }

    /**
     * getDeleted_atのテスト
     */
    public function testGetDeleted_at()
    {
        $model = new JobMasterSearch();
        verify($model->deleted_at)->equals(time());
    }

    /**
     * getDistCsvCellのテスト
     * todo テストしづらいので目的別にメソッド分割
     * todo 市区町村の所属する都道府県がエリアに紐づいていないとバグるので修正
     */
    public function testGetDistCsvCell()
    {
        /** @var JobMasterSearch[] $models */
        $models = JobMasterSearch::find()->limit(5)->all();

        foreach ($models as $model) {
            $distCds = JobDist::find()->select([Dist::tableName() . '.dist_cd'])->innerJoinWith(['dist.pref.area'])->where([
                JobDist::tableName() . '.job_master_id' => $model->id,
                Area::tableName() . '.valid_chk' => Area::FLAG_VALID,
            ])->orderBy([Dist::tableName() . '.dist_cd' => SORT_ASC])->column();

            verify($model->distCsvCell)->equals(implode('|', $distCds));
        }
    }

    /**
     * getMaxWageのテスト
     * todo テストしづらいので目的別にメソッド分割
     */
    public function testCateMaxWage()
    {
        // 一度でも有効な検証が行われたかのフラグ（テストレコードが全部消えたりしたとき用の対策）
        $isVerified = false;
        /** @var JobMasterSearch[] $models */
        $models = JobMasterSearch::find()->limit(5)->all();
        foreach ($models as $model) {
            // その原稿に紐づいている有効な給与カテゴリを抽出
            $cateIds = JobWage::find()->select([
                WageItem::tableName() . '.wage_category_id',
            ])->distinct()->innerJoinWith(['wageItem.wageCategory'])->where([
                JobWage::tableName() . '.job_master_id' => $model->id,
                WageItem::tableName() . '.valid_chk' => SearchkeyMaster::FLAG_VALID,
                WageCategory::tableName() . '.valid_chk' => SearchkeyMaster::FLAG_VALID,
            ])->column();
            foreach ($cateIds as $cateId) {
                // カテゴリ毎にアイテムの最大値を抽出
                $maxWage = WageItem::find()->select('max(wage_item_name)')->joinWith(['jobWage', 'wageCategory'])->where([
                    WageItem::tableName() . '.wage_category_id' => $cateId,
                    JobWage::tableName() . '.job_master_id' => $model->id,
                    WageItem::tableName() . '.valid_chk' => SearchkeyMaster::FLAG_VALID,
                    WageCategory::tableName() . '.valid_chk' => SearchkeyMaster::FLAG_VALID,
                ])->scalar();
                // 検証
                verify($model->cateMaxWage($cateId))->equals($maxWage);
                $isVerified = true;
            }
        }
        verify($isVerified)->true();
    }

    /**
     * getJobTypeSmallCsvCellのテスト
     */
    public function testGetJobTypeSmallCsvCell()
    {
        $jobId = $this->id(5, 'job_master');
        $model = JobMasterSearch::findOne($jobId);
        $types = array_filter($model->jobType, function (JobType $jt) {
            if (!isset($jt->jobTypeSmall) || $jt->jobTypeSmall->valid_chk === 0) {
                return false;
            }
            if (!isset($jt->jobTypeSmall->jobTypeBig) || $jt->jobTypeSmall->jobTypeBig->valid_chk === 0) {
                return false;
            }
            if (!isset($jt->jobTypeSmall->jobTypeBig->jobTypeCategory) || $jt->jobTypeSmall->jobTypeBig->jobTypeCategory->valid_chk === 0) {
                return false;
            }
            return true;
        });
        $typeNos = ArrayHelper::getColumn($types, 'jobTypeSmall.job_type_small_no');
        if ($typeNos) {
            sort($typeNos);
            verify($typeNos)->notEmpty();
            verify(explode('|', $model->jobTypeSmallCsvCell))->equals($typeNos);
        }
    }

    /**
     * getJobSearchKeyItemCsvCellのテスト
     * todo テストしづらいので目的別にメソッド分割
     */
    public function testGetJobSearchKeyItemCsvCell()
    {
        // 一度でも有効な検証が行われたかのフラグ（テストレコードが全部消えたりしたとき用の対策）
        $isVerified = false;
        /** @var JobMasterSearch[] $models */
        $models = JobMasterSearch::find()->all();
        foreach ($models as $model) {
            // その原稿に紐づいている有効な検索キーを抽出
            $itemNumbers = JobSearchkeyItem1::find(
                //)->distinct( todo データ不整合があると同じ数値が２つ以上出てしまうので修正した方がいいかも
            )->select([
                SearchkeyItem1::tableName() . '.searchkey_item_no',
            ])->innerJoinWith(['searchKeyItem.category'])->where([
                SearchkeyItem1::tableName() . '.valid_chk' => SearchkeyItem1::FLAG_VALID,
                SearchkeyCategory1::tableName() . '.valid_chk' => SearchkeyCategory1::FLAG_VALID,
                JobSearchkeyItem1::tableName() . '.job_master_id' => $model->id,
            ])->column();
            // 存在しなければ空
            if (!$itemNumbers) {
                verify($model->getJobSearchKeyItemCsvCell('jobSearchkeyItem1'))->isEmpty();
                continue;
            }
            asort($itemNumbers);
            verify($model->getJobSearchKeyItemCsvCell('jobSearchkeyItem1'))->equals(implode('|', $itemNumbers));
            $isVerified = true;
        }
        verify($isVerified)->true();
    }
}
