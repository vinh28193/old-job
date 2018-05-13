<?php
/**
 * Created by Thang Tran.
 * User: User
 * Date: 12/1/2017
 * Time: 9:44 AM
 */
namespace models\manage;

use app\models\manage\SearchkeyMaster;
use app\models\manage\SearchkeyMasterSearch;
use tests\codeception\unit\JmTestCase;
use yii\data\ActiveDataProvider;

class SearchkeyMasterSearchTest extends JmTestCase
{
    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('booleanチェック', function () {
            $model = new SearchkeyMasterSearch();
            $model->load([$model->formName() => [
                'valid_chk' => 3,
                'is_on_top' => 3,
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('is_on_top'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new SearchkeyMasterSearch();
            $model->load([$model->formName() => [
                'hierarchyType' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('hierarchyType'))->true();
        });
        $this->specify('正しいチェック', function () {
            $model = new SearchkeyMasterSearch();
            $model->load([$model->formName() => [
                'is_on_top' => 1,
                'valid_chk' => 1,
                'hierarchyType' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * サーチを通して検索テスト
     */
    public function testSearch()
    {
        $this->specify('正しく一覧表示されているかチェック', function () {
            $dataProvider = (new SearchkeyMasterSearch())->search([]);
            verify($dataProvider)->isInstanceOf(ActiveDataProvider::className());
            verify($dataProvider->models)->notEmpty();
            verify($dataProvider->models)->count(23);
            foreach ($dataProvider->models as $model) {
                /** @var SearchkeyMasterSearch $model */
                verify($model->table_name)->notEquals('area');
                verify($model->table_name)->notEquals('pref_dist_master');
                verify($model->table_name)->notEquals('job_type_category');
                verify($model->job_relation_table)->notEmpty();
            }
        });

        $this->specify('階層タイプで検索（一階層）', function () {
            $models = $this->getSearchkeyMaster([
                'hierarchyType' => 1,
            ]);
            verify($models)->notEmpty();
            $result = array_column($models, 'table_name');
            verify(count($result))->equals(10);
            $expectColumnNames = [
                'searchkey_item11',
                'searchkey_item12',
                'searchkey_item13',
                'searchkey_item14',
                'searchkey_item15',
                'searchkey_item16',
                'searchkey_item17',
                'searchkey_item18',
                'searchkey_item19',
                'searchkey_item20',
            ];
            foreach ($expectColumnNames as $columnName) {
                verify(in_array($columnName, $result))->true();
            }
        });

        $this->specify('階層タイプで検索（二階層）', function () {
            $models = $this->getSearchkeyMaster([
                'hierarchyType' => 2,
            ]);
            verify($models)->notEmpty();
            $result = array_column($models, 'table_name');
            verify(count($result))->equals(10);
            $expectColumnNames = [
                'searchkey_category1',
                'searchkey_category2',
                'searchkey_category3',
                'searchkey_category4',
                'searchkey_category5',
                'searchkey_category6',
                'searchkey_category7',
                'searchkey_category8',
                'searchkey_category9',
                'searchkey_category10',
            ];
            foreach ($expectColumnNames as $columnName) {
                verify(in_array($columnName, $result))->true();
            }
        });

        $this->specify('階層タイプで検索（その他）', function () {
            $models = $this->getSearchkeyMaster([
                'hierarchyType' => 3,
            ]);
            verify($models)->notEmpty();
            $result = array_column($models, 'table_name');
            verify(count($result))->equals(3);
            $expectColumnNames = [
                'pref',
                'station',
                'wage_category',
            ];
            foreach ($expectColumnNames as $columnName) {
                verify(in_array($columnName, $result))->true();
            }
        });

        $this->specify('表示場所で検索', function () {
            $models = $this->getSearchkeyMaster([
                'is_on_top' => 1,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify($model->is_on_top)->equals(1);
            }
        });

        $this->specify('有効・無効で検索', function () {
            $models = $this->getSearchkeyMaster([
                'valid_chk' => 1,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify($model->valid_chk)->equals(1);
            }
        });
    }

    public function testGetIsLabelForGrid()
    {
        $model = new SearchkeyMasterSearch();
        foreach (SearchkeyMaster::STATIC_KEYS as $key) {
            $model->table_name = $key;
            verify($model->isLabelForGrid)->equals('選択する（固定）');
        }
        $model->table_name = 'test';
        verify($model->isLabelForGrid)->equals('-');
        $model->is_category_label = SearchkeyMaster::CATEGORY_SELECTABLE;
        verify($model->isLabelForGrid)->equals('選択する');
        $model->is_category_label = SearchkeyMaster::CATEGORY_UNSELECTABLE;
        verify($model->isLabelForGrid)->equals('選択しない');
    }

    private function getSearchkeyMaster($searchParam)
    {
        $model = new SearchkeyMasterSearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);
        return $dataProvider->query->all();
    }
}