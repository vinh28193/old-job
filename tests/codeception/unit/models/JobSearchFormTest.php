<?php

namespace tests\codeception\unit\models;

use app\models\forms\JobSearchForm;
use app\models\JobMasterDisp;
use app\models\manage\JobColumnSet;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\JobDist;
use app\models\manage\searchkey\JobPref;
use app\models\manage\searchkey\JobSearchkeyItem;
use app\models\manage\searchkey\JobStationInfo;
use app\models\manage\searchkey\JobType;
use app\models\manage\searchkey\JobTypeBig;
use app\models\manage\searchkey\JobTypeCategory;
use app\models\manage\searchkey\JobTypeSmall;
use app\models\manage\searchkey\JobWage;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\PrefDist;
use app\models\manage\searchkey\Station;
use app\models\manage\searchkey\WageItem;
use app\models\manage\SearchKeyItem;
use app\models\SearchCategory;
use app\models\SearchItem;
use Codeception\Specify;
use tests\codeception\unit\JmTestCase;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * @group job_master
 */
class JobSearchFormTest extends JmTestCase
{
    const FLAG_ON = 1;

    /**
 * @var JobSearchForm
 */
    private $jobSearchForm;

     /**
     * エリア検索テスト
     */
    public function testAreaSearch()
    {
        /** @var Area $area */
        $area = ArrayHelper::getValue($this->jobSearchForm->areas, 0);
        // 検索実行
        $this->jobSearchForm->load([
            'area' => $area->id,
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        // 都道府県ID取得
        $prefIds = array_map(function (Pref $pref) {
            return $pref->id;
        }, Pref::find()->where([
            'area_id' => $area->id,
        ])->all());

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobPrefIds = array_map(function (JobPref $pref) {
                return $pref->pref_id;
            }, $job->jobPref);
            $diffIds    = array_diff($prefIds, $jobPrefIds);
//            codecept_debug($prefIds);
//            codecept_debug($diffIds);
            $this->assertTrue(count($prefIds) > count($diffIds));
        }
    }

    /**
     * 都道府県検索
     */
    public function testPrefSearch()
    {
        /** @var Pref $pref */
        $pref = ArrayHelper::getValue($this->jobSearchForm->prefs, 0);
        // 検索実行
        $this->jobSearchForm->load([
            'pref' => [$pref->pref_no],
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        // 都道府県ID取得
        $prefIds = array_map(function (Pref $pref) {
            return $pref->id;
        }, Pref::find()->where([
            'pref_no' => $pref->pref_no,
        ])->all());

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobPrefIds = array_map(function (JobPref $pref) {
                return $pref->pref_id;
            }, $job->jobPref);
            $diffIds    = array_diff($prefIds, $jobPrefIds);
//            codecept_debug($prefIds);
//            codecept_debug($diffIds);
            $this->assertTrue(count($prefIds) > count($diffIds));
        }
    }

    /**
     * 地域検索
     */
    public function testDistParentSearch()
    {
        $distParents = [];
        foreach ($this->jobSearchForm->prefs as $pref) {
            foreach ($pref->prefDistMasters as $parent) {
                $distParents[] = $parent->id;
            }
        }
        // 検索実行
        $this->jobSearchForm->load([
            'pref_dist_master_parent' => $distParents[0],
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        // 市区町村ID取得
        $distIds = array_map(function (Dist $dist) {
            return $dist->id;
        }, Dist::find()->joinWith([
            'prefDist',
        ])->where([
            PrefDist::tableName() . '.pref_dist_master_id' => $distParents[0],
        ])->all());

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobDistIds = array_map(function (JobDist $dist) {
                return $dist->dist_id;
            }, $job->jobDist);
            $diffIds    = array_diff($distIds, $jobDistIds);
//            codecept_debug(count($jobDistIds));
//            codecept_debug(count($diffIds));
            $this->assertTrue(count($distIds) > count($diffIds));
        }
    }

    /**
     * 市区町村検索
     */
    public function testDistSearch()
    {
        $districts = [];
        foreach ($this->jobSearchForm->prefs as $pref) {
            foreach ($pref->prefDistMasters as $parent) {
                foreach ($parent->districts as $dist) {
                    $districts[] = $dist->id;
                }
            }
        }
        // 検索実行
        $this->jobSearchForm->load([
            'pref_dist_master' => $districts[0],
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobDistIds = array_map(function (JobDist $dist) {
                return $dist->dist_id;
            }, $job->jobDist);
            $diffIds    = array_diff([$districts[0]], $jobDistIds);
//            codecept_debug(count($districts));
//            codecept_debug(count($diffIds));
            $this->assertTrue(1 > count($diffIds));
        }
    }

    /**
     * 路線検索
     */
    public function testStationParentSearch()
    {
        $routeCodes = [];
        foreach ($this->jobSearchForm->getStationParts() as $prefId => $stationPart) {
            foreach ($stationPart as $companyCode => $routes) {
                foreach ($routes as $routeCode => $stations) {
                    $routeCodes[] = $routeCode;
                }
            }
        }
        // 検索実行
        $this->jobSearchForm->load([
            'station_parent' => $routeCodes[0],
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        // 駅ID取得
        $stationIds = array_map(function (Station $station) {
            return $station->station_no;
        }, Station::find()->where([
            'route_cd' => $routeCodes[0],
        ])->all());

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobStationIds = array_map(function (JobStationInfo $info) {
                return $info->station_id;
            }, $job->jobStation);
            $diffIds       = array_diff($stationIds, $jobStationIds);
//            codecept_debug(count($stationIds));
//            codecept_debug(count($diffIds));
            $this->assertTrue(count($stationIds) > count($diffIds));
        }
    }

    /**
     * 駅検索
     */
    public function testStationSearch()
    {
        $stationIds = [];
        foreach ($this->jobSearchForm->getStationParts() as $prefId => $stationPart) {
            foreach ($stationPart as $companyCode => $routes) {
                foreach ($routes as $routeCode => $stations) {
                    foreach ($stations as $station) {
                        $stationIds[] = $station;
                    }
                }
            }
        }
        // 検索実行
        $this->jobSearchForm->load([
            'station' => $stationIds[0],
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobStationIds = array_map(function (JobStationInfo $info) {
                return $info->station_id;
            }, $job->jobStation);
            $diffIds       = array_diff([$stationIds[0]], $jobStationIds);
//            codecept_debug(count($stationIds));
//            codecept_debug(count($diffIds));
            $this->assertTrue(1 > count($diffIds));
        }
    }

    /**
     * 給与カテゴリ検索
     */
    public function testWageCategorySearch()
    {
        $wageCategoryIds = [];
        foreach ($this->jobSearchForm->wages as $k => $wageCategory) {
            $wageCategoryIds[] = $wageCategory->id;
        }
        // 検索実行
        $this->jobSearchForm->load([
            'wage_category_parent' => $wageCategoryIds[0],
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        // 給与ID取得
        $wageIds = array_map(function (WageItem $item) {
            return $item->id;
        }, WageItem::find()->where([
            'wage_category_id' => $wageCategoryIds[0],
        ])->all());

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobWageIds = array_map(function (JobWage $wage) {
                return $wage->wage_item_id;
            }, $job->jobWage);
            $diffIds    = array_diff($wageIds, $jobWageIds);
//            codecept_debug(count($wageIds));
//            codecept_debug(count($diffIds));
            $this->assertTrue(count($wageIds) > count($diffIds));
        }
    }

    /**
     * 給与検索
     */
    public function testWageSearch()
    {
        $wageIds = [];
        foreach ($this->jobSearchForm->wages as $k => $wageCategory) {
            foreach ($wageCategory->wageItemValid as $wageItem) {
                $wageIds[] = $wageItem->wage_item_no;
            }
        }
        // 検索実行
        $this->jobSearchForm->load([
            'wage_category' => $wageIds[0],
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobWageIds = array_map(function (JobWage $wage) {
                return $wage->wage_item_id;
            }, $job->jobWage);
            $diffIds    = array_diff([$wageIds[0]], $jobWageIds);
//            codecept_debug(count($wageIds));
//            codecept_debug(count($diffIds));
            $this->assertTrue(1 > count($diffIds));
        }
    }

    /**
     * 職種カテゴリ検索
     */
    public function testJobTypeCategorySearch()
    {
        /** @var JobTypeCategory $jobTypeCategory */
        $jobTypeCategory = ArrayHelper::getValue($this->jobSearchForm->jobTypes, 0);
        // 検索実行
        $this->jobSearchForm->load([
            'job_type_category_first' => $jobTypeCategory->job_type_category_cd,
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        // 職種大を取得
        $jobTypeBigIds = array_map(function (JobTypeBig $jobTypeBig) {
            return $jobTypeBig->job_type_big_no;
        }, JobTypeBig::find()->joinWith([
            'jobTypeCategory',
        ])->where([
            JobTypeCategory::tableName() . '.valid_chk'            => self::FLAG_ON,
            JobTypeBig::tableName() . '.valid_chk'                 => self::FLAG_ON,
            JobTypeCategory::tableName() . '.job_type_category_cd' => $jobTypeCategory->job_type_category_cd,
        ])->all());
        // 職種小を取得
        $jobTypeSmallIds = array_map(function (JobTypeSmall $jobType) {
            return $jobType->id;
        }, JobTypeSmall::find()->joinWith([
            'jobBigTypes',
        ])->where([
            JobTypeSmall::tableName() . '.valid_chk'     => self::FLAG_ON,
            JobTypeBig::tableName() . '.valid_chk'       => self::FLAG_ON,
            JobTypeBig::tableName() . '.job_type_big_no' => $jobTypeBigIds,
        ])->all());

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobSmallIds = array_map(function (JobType $jobType) {
                return $jobType->job_type_small_id;
            }, $job->jobType);
            $diffIds     = array_diff($jobTypeSmallIds, $jobSmallIds);
//            codecept_debug(count($jobTypeSmallIds));
//            codecept_debug(count($diffIds));
            $this->assertTrue(count($jobTypeSmallIds) > count($diffIds));
        }
    }

    /**
     * 職種大検索
     */
    public function testJobTypeBigSearch()
    {
        $jobTypeBigIds = [];
        /** @var JobTypeCategory $jobTypeCategory */
        $jobTypeCategory = ArrayHelper::getValue($this->jobSearchForm->jobTypes, 0);
        foreach ($jobTypeCategory->jobTypeBig as $k => $jobTypeBig) {
            $jobTypeBigIds[] = $jobTypeBig->job_type_big_no;
        }

        // 検索実行
        $this->jobSearchForm->load([
            'job_type_category_parent' => $jobTypeBigIds[0],
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        // 職種小を取得
        $jobTypeSmallIds = array_map(function (JobTypeSmall $jobType) {
            return $jobType->id;
        }, JobTypeSmall::find()->joinWith([
            'jobBigTypes',
        ])->where([
            JobTypeSmall::tableName() . '.valid_chk'     => self::FLAG_ON,
            JobTypeBig::tableName() . '.valid_chk'       => self::FLAG_ON,
            JobTypeBig::tableName() . '.job_type_big_no' => $jobTypeBigIds[0],
        ])->all());

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobSmallIds = array_map(function (JobType $jobType) {
                return $jobType->job_type_small_id;
            }, $job->jobType);
            $diffIds     = array_diff($jobTypeSmallIds, $jobSmallIds);
//            codecept_debug(count($jobTypeSmallIds));
//            codecept_debug(count($diffIds));
            $this->assertTrue(count($jobTypeSmallIds) > count($diffIds));
        }
    }

    /**
     * 職種小検索
     */
    public function testJobTypeSmallSearch()
    {
        $jobTypeSmallIds = [];
        /** @var JobTypeCategory $jobTypeCategory */
        $jobTypeCategory = ArrayHelper::getValue($this->jobSearchForm->jobTypes, 0);
        foreach ($jobTypeCategory->jobTypeBig as $jobTypeBig) {
            foreach ($jobTypeBig->jobTypeSmall as $jobTypeSmall) {
                $jobTypeSmallIds[] = $jobTypeSmall->job_type_small_no;
            }
        }
        // 検索実行
        $this->jobSearchForm->load([
            'job_type_category' => $jobTypeSmallIds[0],
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        // 職種小をNOから取得
        /** @var JobTypeSmall $jobSmallType */
        $jobSmallType = JobTypeSmall::find()->joinWith([
            'jobBigTypes',
        ])->where([
            JobTypeSmall::tableName() . '.valid_chk'         => self::FLAG_ON,
            JobTypeBig::tableName() . '.valid_chk'           => self::FLAG_ON,
            JobTypeSmall::tableName() . '.job_type_small_no' => $jobTypeSmallIds[0],
        ])->one();

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $jobSmallIds = array_map(function (JobType $jobType) {
                return $jobType->job_type_small_id;
            }, $job->jobType);
            $diffIds     = array_diff([$jobSmallType->id], $jobSmallIds);
//            codecept_debug(count($jobTypeSmallIds));
//            codecept_debug(count($diffIds));
            $this->assertTrue(1 > count($diffIds));
        }
    }

    /**
     * フリーワード検索
     */
    public function testFreeWordSearch()
    {
        // 検索実行
        $keyword = '東京';
        $this->jobSearchForm->load([
            'keyword' => $keyword,
        ]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->jobSearchForm->search();

        $searchColumns = array_map(function (JobColumnSet $columnSet) {
            return $columnSet->column_name;
        }, JobColumnSet::find()->where([
            'valid_chk'           => self::FLAG_ON,
            'freeword_search_flg' => self::FLAG_ON,
        ])->all());

        /** @var JobMasterDisp $job */
        foreach ($dataProvider->getModels() as $job) {
            $keywordExists = false;
            foreach ($searchColumns as $searchColumn) {
                if (strpos($job->{$searchColumn}, $keyword) !== false) {
                    $keywordExists = true;
                }
            }
            $this->assertTrue($keywordExists);
        }
    }

    /**
     * 自由検索2階層
     */
    public function testCategorySearch()
    {
        foreach ($this->jobSearchForm->searchKeys as $searchKey) {
            if (!$searchKey->isCategory) {
                continue;
            }
            /** @var SearchCategory $category */
            $category = ArrayHelper::getValue($searchKey->categories, 0);

            // 検索実行
            $jobSearchForm = new JobSearchForm();
            $attributeName = $searchKey->table_name . '_parent';
            $jobSearchForm->load([
                $attributeName => [$category->id],
            ]);
            /** @var ActiveDataProvider $dataProvider */
            $dataProvider = $jobSearchForm->search();
            // テーブルからIDを取得
            preg_match('/(\d+)$/', $searchKey->table_name, $result);
            $tableId = $result[1];
            SearchItem::setTableId($tableId);
            $itemIds = array_map(function (SearchItem $item) {
                return $item->id;
            }, SearchItem::find()->andWhere([
                'valid_chk'             => self::FLAG_ON,
                'searchkey_category_id' => $category->id,
            ])->all());
            $joinModel = JobSearchkeyItem::className() . $tableId;

            /** @var JobMasterDisp $job */
            foreach ($dataProvider->getModels() as $job) {
                /** @var JobSearchkeyItem $itemModel */
                $itemModel = new $joinModel();
                // 設定状態を取得
                /** @var JobSearchkeyItem[] $items */
                $items      = $itemModel->find()->andWhere([
                    'job_master_id' => $job->id,
                ])->all();
                $jobItemIds = array_map(function ($item) {
                    return $item->searchkey_item_id;
                }, $items);
                $diffIds    = array_diff($itemIds, $jobItemIds);
                $this->assertTrue(count($itemIds) > count($diffIds));
            }

            unset($jobSearchForm);
        }
    }

    /**
     * 自由検索1階層
     */
    public function testItemSearch()
    {
        foreach ($this->jobSearchForm->searchKeys as $searchKey) {
            if (!$searchKey->isItem) {
                continue;
            }
            /** @var SearchKeyItem $item */
            $item = ArrayHelper::getValue($searchKey->items, 0);

            // 検索実行
            $jobSearchForm = new JobSearchForm();
            $jobSearchForm->load([
                $searchKey->table_name => [$item->id],
            ]);
            /** @var ActiveDataProvider $dataProvider */
            $dataProvider = $jobSearchForm->search();
            // テーブルからIDを取得
            preg_match('/(\d+)$/', $searchKey->table_name, $result);
            $tableId = $result[1];
            $joinModel = JobSearchkeyItem::className() . $tableId;

            /** @var JobMasterDisp $job */
            foreach ($dataProvider->getModels() as $job) {
                /** @var JobSearchkeyItem $itemModel */
                $itemModel = new $joinModel();
                // 設定状態を取得
                /** @var JobSearchkeyItem[] $items */
                $items      = $itemModel->find()->andWhere([
                    'job_master_id' => $job->id,
                ])->all();
                $jobItemIds = array_map(function ($item) {
                    return $item->searchkey_item_id;
                }, $items);
                $diffIds    = array_diff([$item->id], $jobItemIds);
                $this->assertTrue(1 > count($diffIds));
            }

            unset($jobSearchForm);
        }
    }
}
