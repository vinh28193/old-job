<?php
namespace models\manage;

use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\JobTypeCategory;
use app\models\manage\searchkey\JobTypeSmall;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\SearchkeyItem1;
use app\models\manage\searchkey\SearchkeyItem11;
use app\models\manage\searchkey\Station;
use app\models\manage\searchkey\WageCategory;
use app\models\manage\searchkey\WageItem;
use tests\codeception\unit\JmTestCase;
use app\models\manage\SearchkeyMaster;

class SearchkeyMasterTest extends JmTestCase
{
    public function testTableName()
    {
        $model = new SearchkeyMaster();
        verify($model->tableName())->equals('searchkey_master');
    }

    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new SearchkeyMaster();
            $model->load([
                'tenant_id' => '文字列',
                'searchkey_no' => '文字列',
                'sort' => '文字列',
                'search_input_tool' => '文字列',
            ], '');
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('searchkey_no'))->true();
            verify($model->hasErrors('sort'))->true();
            verify($model->hasErrors('search_input_tool'))->true();
        });

        $this->specify('boolチェック', function () {
            $model = new SearchkeyMaster();

            //test with string
            $model->load([
                'valid_chk' => '文字列',
                'icon_flg' => '文字列',
                'is_category_label' => '文字列',
                'is_on_top' => '文字列',
                'is_and_search' => '文字列',
            ], '');
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('icon_flg'))->true();
            verify($model->hasErrors('is_category_label'))->true();
            verify($model->hasErrors('is_on_top'))->true();
            verify($model->hasErrors('is_and_search'))->true();

            //test with numberic
            $model->load([
                'valid_chk' => 123,
                'icon_flg' => 123,
                'is_category_label' => 123,
                'is_on_top' => 123,
                'is_and_search' => 123,
            ], '');
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('icon_flg'))->true();
            verify($model->hasErrors('is_category_label'))->true();
            verify($model->hasErrors('is_on_top'))->true();
            verify($model->hasErrors('is_and_search'))->true();
        });

        $this->specify('最大文字数チェック', function () {
            $model = new SearchkeyMaster();
            $model->load([
                'table_name' => str_repeat('a', 51),
                'searchkey_name' => str_repeat('a', 51),
                'first_hierarchy_cd' => str_repeat('a', 11),
                'second_hierarchy_cd' => str_repeat('a', 11),
                'third_hierarchy_cd' => str_repeat('a', 11),
            ], '');
            $model->validate();
            verify($model->hasErrors('table_name'))->true();
            verify($model->hasErrors('searchkey_name'))->true();
            verify($model->hasErrors('first_hierarchy_cd'))->true();
            verify($model->hasErrors('second_hierarchy_cd'))->true();
            verify($model->hasErrors('third_hierarchy_cd'))->true();
        });

        $this->specify('必須チェック', function () {
            $model = new SearchkeyMaster();
            $model->load([
                'searchkey_name' => '',
                'valid_chk' => '',
                'is_on_top' => '',
                'sort' => '',
            ], '');
            $model->validate();
            verify($model->hasErrors('searchkey_name'))->true();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('is_on_top'))->true();
            verify($model->hasErrors('sort'))->true();
        });

        $this->specify('数制限チェック', function () {
            $model = new SearchkeyMaster();
            $model->load([
                'sort' => 0,
            ], '');
            $model->validate();
            verify($model->hasErrors('sort'))->true();

            $model->load([
                'sort' => 100,
            ], '');
            $model->validate();
            verify($model->hasErrors('sort'))->true();
        });

        $this->specify('使用不可能文字列チェック', function () {
            $model = new SearchkeyMaster();
            $model->load([
                'first_hierarchy_cd' => 'FW',
                'second_hierarchy_cd' => 'FW',
                'third_hierarchy_cd' => 'FW',
            ], '');
            $model->validate();
            verify($model->hasErrors('first_hierarchy_cd'))->true();
            verify($model->hasErrors('second_hierarchy_cd'))->true();
            verify($model->hasErrors('third_hierarchy_cd'))->true();
        });

        $this->specify('is_and_searchとsearch_input_toolの必須チェック。', function () {
            foreach (SearchkeyMaster::STATIC_KEYS as $tableName) {
                $model = new SearchkeyMaster();
                $model->load([
                    'table_name' => $tableName,
                    'is_and_search' => '',
                    'search_input_tool' => '',
                ], '');
                verify($model->hasErrors('is_and_search'))->false();
                verify($model->hasErrors('search_input_tool'))->false();
            }
            $model = new SearchkeyMaster();
            $model->load([
                'table_name' => 'aaaa',
                'is_and_search' => '',
                'search_input_tool' => '',
            ], '');
            $model->validate();
            verify($model->hasErrors('is_and_search'))->true();
            verify($model->hasErrors('search_input_tool'))->true();
        });

        $this->specify('is_category_labelの必須チェック。', function () {
            $model = new SearchkeyMaster();
            foreach (SearchkeyMaster::STATIC_KEYS as $tableName) {
                //second_hierarchy_cd = nullかつSTATIC_KEYSの場合
                $model->load([
                    'table_name' => $tableName,
                    'second_hierarchy_cd' => null,
                    'is_category_label' => '',
                ], '');
                $model->validate();
                verify($model->hasErrors('is_category_label'))->false();

                //second_hierarchy_cd != nullかつSTATIC_KEYSの場合
                $model->load([
                    'table_name' => $tableName,
                    'second_hierarchy_cd' => 'FW',
                    'is_category_label' => '',
                ], '');
                $model->validate();
                verify($model->hasErrors('is_category_label'))->false();
            }
            //second_hierarchy_cd = nullかつSTATIC_KEYSではない場合
            $model->load([
                'table_name' => 'aaaa',
                'second_hierarchy_cd' => null,
                'is_category_label' => '',
            ], '');
            $model->validate();
            verify($model->hasErrors('is_category_label'))->false();

            //second_hierarchy_cd != nullかつSTATIC_KEYSではない場合
            $model->load([
                'table_name' => 'aaaa',
                'second_hierarchy_cd' => 'FW',
                'is_category_label' => '',
            ], '');
            $model->validate();
            verify($model->hasErrors('is_category_label'))->true();
        });

        $this->specify('icon_flgの必須チェック', function () {
            foreach (SearchkeyMaster::ICON_STATIC_KEYS as $tableName) {
                $model = new SearchkeyMaster();
                $model->load([
                    'table_name' => $tableName,
                    'icon_flg' => '',
                ], '');
                verify($model->hasErrors('icon_flg'))->false();
            }
            $model = new SearchkeyMaster();
            $model->load([
                'table_name' => 'aaaa',
                'icon_flg' => '',
            ], '');
            $model->validate();
            verify($model->hasErrors('icon_flg'))->true();
        });

        $this->specify('正しい値でのテスト', function () {
            $model = new SearchkeyMaster();
            $model->load([
                'tenant_id' => 1,
                'searchkey_no' => 1,
                'table_name' => str_repeat('a', 50),
                'searchkey_name' => str_repeat('a', 50),
                'first_hierarchy_cd' => str_repeat('a', 10),
                'second_hierarchy_cd' => str_repeat('a', 10),
                'third_hierarchy_cd' => str_repeat('a', 10),
                'is_category_label' => 1,
                'valid_chk' => 1,
                'is_and_search' => 1,
                'sort' => 99,
                'search_input_tool' => 1,
                'is_more_search' => 1,
                'is_on_top' => 1,
                'icon_flg' => 1,
            ], '');
            verify($model->validate())->true();
        });
    }

    public function testGetSearchKeyModels()
    {
        $this->specify('地域の場合', function () {
            $searchKeyMaster = SearchkeyMaster::findName('pref');
            verify($searchKeyMaster->searchKeyModels)->notEmpty();
            foreach ($searchKeyMaster->searchKeyModels as $model) {
                verify($model)->isInstanceOf(Pref::className());
                verify(Pref::findOne(['id' => $model->id])->area->valid_chk)->equals(1);
            }
        });

        $this->specify('駅の場合', function () {
            $searchKeyMaster = SearchkeyMaster::findName('station');
            $searchKeyModels = $searchKeyMaster->searchKeyModels;
            verify($searchKeyModels)->notEmpty();
            verify(array_shift($searchKeyModels))->isInstanceOf(Station::className());
        });

        $this->specify('給与の場合', function () {
            $searchKeyMaster = SearchkeyMaster::findName('wage_category');
            verify($searchKeyMaster->searchKeyModels)->notEmpty();
            foreach ($searchKeyMaster->searchKeyModels as $model) {
                verify($model)->isInstanceOf(WageCategory::className());
                verify($model->valid_chk)->equals(1);
                foreach ($model->wageItem as $item) {
                    verify($item->valid_chk)->equals(1);
                }
            }
        });

        // 職種検索キーは全サイトで無効になるがソースは残す。
//        $this->specify('職種の場合', function () {
//            $searchKeyMaster = SearchkeyMaster::findName('job_type_category');
//            verify($searchKeyMaster->searchKeyModels)->notEmpty();
//            foreach ($searchKeyMaster->searchKeyModels as $model) {
//                verify($model)->isInstanceOf(JobTypeCategory::className());
//                verify($model->valid_chk)->equals(1);
//                foreach ($model->jobTypeBig as $big) {
//                    verify($big->valid_chk)->equals(1);
//                    foreach ($big->jobTypeSmall as $small) {
//                        verify($small->valid_chk)->equals(1);
//                    }
//                }
//            }
//        });

        $this->specify('2階層の場合', function () {
            $searchKeyMaster = SearchkeyMaster::findName('searchkey_category1');
            $className = SearchkeyMaster::MODEL_BASE_PATH . 'SearchkeyCategory1';
            verify($searchKeyMaster->searchKeyModels)->notEmpty();
            foreach ($searchKeyMaster->searchKeyModels as $model) {
                verify($model)->isInstanceOf($className);
                verify($model->valid_chk)->equals(1);
                foreach ($model->items as $item) {
                    verify($item->valid_chk)->equals(1);
                }
            }
        });

        $this->specify('1階層の場合', function () {
            $searchKeyMaster = SearchkeyMaster::findName('searchkey_item11');
            $className = SearchkeyMaster::MODEL_BASE_PATH . 'SearchkeyItem11';
            verify($searchKeyMaster->searchKeyModels)->notEmpty();
            foreach ($searchKeyMaster->searchKeyModels as $model) {
                verify($model)->isInstanceOf($className);
                verify($model->valid_chk)->equals(1);
            }
        });
    }

    public function testGetItemModels()
    {
        $this->specify('地域の場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'pref']);
            verify($searchkeyMaster->itemModels)->notEmpty();
            foreach ($searchkeyMaster->itemModels as $item) {
                /** @var Dist $item */
                verify($item)->isInstanceOf(Dist::className());
                verify($item->pref->area->valid_chk)->equals(1);
            }
        });

        $this->specify('給与の場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'wage_category']);
            verify($searchkeyMaster->itemModels)->notEmpty();
            foreach ($searchkeyMaster->itemModels as $item) {
                /** @var WageItem $item */
                verify($item)->isInstanceOf(WageItem::className());
                verify($item->valid_chk)->equals(1);
                verify($item->wageCategory->valid_chk)->equals(1);
            }
        });

        // 職種検索キーは全サイトで無効になるがソースは残す。
//        $this->specify('職種の場合', function () {
//            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'job_type_category']);
//            verify($searchkeyMaster->itemModels)->notEmpty();
//            foreach ($searchkeyMaster->itemModels as $item) {
//                /** @var JobTypeSmall $item */
//                verify($item)->isInstanceOf(JobTypeSmall::className());
//                verify($item->valid_chk)->equals(1);
//                verify($item->jobTypeBig->valid_chk)->equals(1);
//                verify($item->jobTypeBig->jobTypeCategory->valid_chk)->equals(1);
//            }
//        });

        $this->specify('駅の場合', function () {
            $searchkeyMaster = SearchkeyMaster::findName('station');
            $items = $searchkeyMaster->itemModels;
            verify($items)->notEmpty();
            verify(array_shift($items))->isInstanceOf(Station::className());
        });

        $this->specify('汎用キーの場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'searchkey_category1']);
            verify($searchkeyMaster->itemModels)->notEmpty();
            foreach ($searchkeyMaster->itemModels as $item) {
                /** @var SearchkeyItem1 $item */
                verify($item)->isInstanceOf(SearchkeyItem1::className());
                verify($item->valid_chk)->equals(1);
                verify($item->category->valid_chk)->equals(1);
            }
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'searchkey_item11']);
            verify($searchkeyMaster->itemModels)->notEmpty();
            foreach ($searchkeyMaster->itemModels as $item) {
                /** @var SearchkeyItem11 $item */
                verify($item)->isInstanceOf(SearchkeyItem11::className());
                verify($item->valid_chk)->equals(1);
            }
        });
    }

    public function testGetItemNos()
    {
        $this->specify('地域の場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'pref']);
            $expect = Dist::find()
                ->select('dist_cd')
                ->innerJoinWith('pref.area')
                ->where([Area::tableName() . '.valid_chk' => SearchkeyMaster::FLAG_VALID])
                ->column();
            $this->checkNos($searchkeyMaster, $expect);
        });

        $this->specify('給与の場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'wage_category']);
            $expect = WageItem::find()
                ->select('wage_item_name')
                ->innerJoinWith('wageCategory')
                ->where(['wage_category.valid_chk' => 1, 'wage_item.valid_chk' => 1])
                ->column();
            $this->checkNos($searchkeyMaster, $expect);
        });

        // 職種検索キーは全サイトで無効になるがソースは残す。
//        $this->specify('職種の場合', function () {
//            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'job_type_category']);
//            $expect = JobTypeSmall::find()
//                ->select('job_type_small_no')
//                ->innerJoinWith('jobTypeBig.jobTypeCategory')
//                ->where(['job_type_small.valid_chk' => 1, 'job_type_big.valid_chk' => 1, 'job_type_category.valid_chk' => 1])
//                ->column();
//            $this->checkNos($searchkeyMaster, $expect);
//        });

        $this->specify('駅の場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'station']);
            $expect = Station::find()->select('station_no')->column();
            $this->checkNos($searchkeyMaster, $expect);
        });

        $this->specify('汎用キーの場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'searchkey_category1']);
            $expect = SearchkeyItem1::find()
                ->select('searchkey_item_no')
                ->innerJoinWith('category')
                ->where(['searchkey_category1.valid_chk' => 1, 'searchkey_item1.valid_chk' => 1])
                ->column();
            $this->checkNos($searchkeyMaster, $expect);

            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'searchkey_item11']);
            $expect = SearchkeyItem11::find()
                ->select('searchkey_item_no')
                ->where(['searchkey_item11.valid_chk' => 1])
                ->column();
            $this->checkNos($searchkeyMaster, $expect);
        });
    }

    /**
     * @param $serachkeyMaster SearchkeyMaster
     * @param $expect array
     */
    private function checkNos($serachkeyMaster, $expect)
    {
        verify($serachkeyMaster->itemNos)->notEmpty();
        foreach (array_flip($serachkeyMaster->itemNos) as $no) {
            verify(in_array($no, $expect))->true();
        }
    }

    public function testFromNosToIds()
    {
        $this->specify('地域の場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'pref']);
            $id1 = $searchkeyMaster->getItemModels()[0]->id;
            $id2 = $searchkeyMaster->getItemModels()[1]->id;
            $model1 = Dist::findOne($id1);
            $model2 = Dist::findOne($id2);
            verify($searchkeyMaster->itemNos[$model1->dist_cd])->equals($id1);
            verify($searchkeyMaster->itemNos[$model2->dist_cd])->equals($id2);
        });

        $this->specify('給与の場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'wage_category']);
            $id1 = $searchkeyMaster->getItemModels()[0]->id;
            $id2 = $searchkeyMaster->getItemModels()[1]->id;
            $model1 = WageItem::findOne($id1);
            $model2 = WageItem::findOne($id2);
            verify($searchkeyMaster->itemNos[$model1->wage_item_name])->equals($id1);
            verify($searchkeyMaster->itemNos[$model2->wage_item_name])->equals($id2);
        });

        $this->specify('駅の場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'station']);
            $id1 = 1111;
            $id2 = 2222;
            verify($searchkeyMaster->itemNos)->contains($id1);
            verify($searchkeyMaster->itemNos)->contains($id2);
        });

        // 職種検索キーは全サイトで無効になるがソースは残す。
//        $this->specify('職種の場合', function () {
//            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'job_type_category']);
//            $id1 = $searchkeyMaster->getItemModels()[0]->id;
//            $id2 = $searchkeyMaster->getItemModels()[1]->id;
//            $model1 = JobTypeSmall::findOne($id1);
//            $model2 = JobTypeSmall::findOne($id2);
//            verify($searchkeyMaster->itemNos[$model1->job_type_small_no])->equals($id1);
//            verify($searchkeyMaster->itemNos[$model2->job_type_small_no])->equals($id2);
//        });

        $this->specify('汎用キーの場合', function () {
            $searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'searchkey_category1']);
            $id1 = $searchkeyMaster->getItemModels()[0]->id;
            $id2 = $searchkeyMaster->getItemModels()[1]->id;
            $model1 = SearchkeyItem1::findOne($id1);
            $model2 = SearchkeyItem1::findOne($id2);
            verify($searchkeyMaster->itemNos[$model1->searchkey_item_no])->equals($id1);
            verify($searchkeyMaster->itemNos[$model2->searchkey_item_no])->equals($id2);
        });
    }

// todo :書く
//    public function testSaveRouteSetting()
//    {
//    }

    public function testGetSettingMenus()
    {
        $model = new SearchkeyMaster();

        $method = new \ReflectionMethod($model, 'getExcludeTableNames');
        $method->setAccessible(true);
        $excludeTables = $method->invoke($model);
        $results = $model->getSettingMenus();
        foreach ($results as $menu) {
            verify(in_array($menu->table_name, $excludeTables))->false();
            verify(count($results))->equals(23);
        }
    }

    public function testGetExcludeTableNames()
    {
        $model = new SearchkeyMaster();

        $method = new \ReflectionMethod($model, 'getExcludeTableNames');
        $method->setAccessible(true);
        verify($method->invoke($model))->equals(['pref', 'station','job_type_category']);
    }

//    public function testCsvDescription()
//    {
//
//    }

    public function testGetValidArray()
    {
        $this->specify('有効', function () {
            verify(SearchkeyMaster::getValidArray()[SearchkeyMaster::FLAG_VALID])->equals('有効');
        });
        $this->specify('無効', function () {
            verify(SearchkeyMaster::getValidArray()[SearchkeyMaster::FLAG_INVALID])->equals('無効');
        });
    }

    public function testGetIsCategoryLabel()
    {
        $this->specify('選択する', function () {
            verify(SearchkeyMaster::getIsCategoryLabel()[SearchkeyMaster::CATEGORY_SELECTABLE])->equals('選択する');
        });
        $this->specify('選択しない', function () {
            verify(SearchkeyMaster::getIsCategoryLabel()[SearchkeyMaster::CATEGORY_UNSELECTABLE])->equals('選択しない');
        });
    }

    public function testGetIsAndSearch()
    {
        $this->specify('and', function () {
            verify(SearchkeyMaster::getIsAndSearch()[SearchkeyMaster::IS_SEARCH_AND])->equals('and');
        });
        $this->specify('or', function () {
            verify(SearchkeyMaster::getIsAndSearch()[SearchkeyMaster::IS_SEARCH_OR])->equals('or');
        });
    }

    public function testGetSearchInputTool()
    {
        $this->specify('モーダル', function () {
            verify(SearchkeyMaster::getSearchInputTool()[SearchkeyMaster::SEARCH_INPUT_TOOL_MODAL])->equals('モーダル');
        });
        $this->specify('チェックボックス', function () {
            verify(SearchkeyMaster::getSearchInputTool()[SearchkeyMaster::SEARCH_INPUT_TOOL_CHECKBOX])->equals('チェックボックス');
        });
        $this->specify('プルダウン', function () {
            verify(SearchkeyMaster::getSearchInputTool()[SearchkeyMaster::SEARCH_INPUT_TOOL_DROPDOWN])->equals('プルダウン');
        });
    }

    public function testGetIsMoreSearch()
    {
        $this->specify('最初から表示', function () {
            verify(SearchkeyMaster::getIsMoreSearch()[SearchkeyMaster::DISPLAY_FIRST])->equals('最初から表示');
        });
        $this->specify('ボタンを押すと表示', function () {
            verify(SearchkeyMaster::getIsMoreSearch()[SearchkeyMaster::DISPLAY_WHEN_PRESS_BUTTON])->equals('ボタンを押すと表示');
        });
    }

    public function testGetIsOnTop()
    {
        $this->specify('詳細検索およびPCトップに表示', function () {
            verify(SearchkeyMaster::getIsOnTop()[SearchkeyMaster::DISPLAY_IN_TOP_PAGE])->equals('詳細検索およびPCトップに表示');
        });
        $this->specify('詳細検索にのみ表示', function () {
            verify(SearchkeyMaster::getIsOnTop()[SearchkeyMaster::DISPLAY_IN_SEARCH_ONLY])->equals('詳細検索にのみ表示');
        });
    }

    public function testGetIconFlg()
    {
        $this->specify('表示する', function () {
            verify(SearchkeyMaster::getIconFlg()[SearchkeyMaster::ICON_FLG_VALID])->equals('表示する');
        });
        $this->specify('表示しない', function () {
            verify(SearchkeyMaster::getIconFlg()[SearchkeyMaster::ICON_FLG_INVALID])->equals('表示しない');
        });
    }

    public function testGetFormatTable()
    {
        $this->specify('is_category_label', function () {
            $model = new SearchkeyMaster();
            $formatArrays = $model->formatTable['is_category_label'];
            verify($formatArrays[SearchkeyMaster::CATEGORY_SELECTABLE])->equals('選択する');
            verify($formatArrays[SearchkeyMaster::CATEGORY_UNSELECTABLE])->equals('選択しない');
            verify($formatArrays[null])->equals('選択する');
        });
        $this->specify('is_and_search', function () {
            $model = new SearchkeyMaster();
            $formatArrays = $model->formatTable['is_and_search'];
            verify($formatArrays[SearchkeyMaster::IS_SEARCH_AND])->equals('and');
            verify($formatArrays[SearchkeyMaster::IS_SEARCH_OR])->equals('or');
            verify($formatArrays[null])->equals('or');
        });
        $this->specify('search_input_tool', function () {
            $model = new SearchkeyMaster();
            $formatArrays = $model->formatTable['search_input_tool'];
            verify($formatArrays[SearchkeyMaster::SEARCH_INPUT_TOOL_MODAL])->equals('モーダル');
            verify($formatArrays[SearchkeyMaster::SEARCH_INPUT_TOOL_CHECKBOX])->equals('チェックボックス');
            verify($formatArrays[SearchkeyMaster::SEARCH_INPUT_TOOL_DROPDOWN])->equals('プルダウン');
            verify($formatArrays[null])->equals('モーダル');
        });
        $this->specify('icon_flg', function () {
            $model = new SearchkeyMaster();
            $formatArrays = $model->formatTable['icon_flg'];
            verify($formatArrays[SearchkeyMaster::ICON_FLG_VALID])->equals('表示する');
            verify($formatArrays[SearchkeyMaster::ICON_FLG_INVALID])->equals('表示しない');
            verify($formatArrays[null])->equals('表示しない');
        });
        $this->specify('valid_chk', function () {
            $model = new SearchkeyMaster();
            $formatArrays = $model->formatTable['valid_chk'];
            verify($formatArrays[SearchkeyMaster::FLAG_VALID])->equals('有効');
            verify($formatArrays[SearchkeyMaster::FLAG_INVALID])->equals('無効');
        });
    }
}
