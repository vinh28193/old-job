<?php
/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/03
 * Time: 18:22
 */

namespace models\manage;

use yii;
use app\models\manage\searchkey\SearchkeyCategory;
use app\models\SearchCategory;
use tests\codeception\unit\JmTestCase;

class SearchCategoryTest extends JmTestCase
{
    public static function testSetTableId()
    {
        SearchCategory::setTableId(99999);
        verify(SearchCategory::$tableId)->equals(99999);
    }

    /**
     * getItemsのtest
     * tableNameのtestも兼ねる
     */
    public function testGetItems()
    {
        $this->checkGetItems(1);
        $this->checkGetItems(2);
    }

    /**
     * @param $id
     * @throws yii\base\InvalidConfigException
     */
    private function checkGetItems($id)
    {
        SearchCategory::setTableId($id);
        verify(SearchCategory::tableName())->equals(SearchCategory::TABLE_BASE . $id);

        /** @var SearchCategory $model */
        $model = SearchCategory::find()->one();
        verify($model)->notEmpty();
        /** @var SearchkeyCategory $expectedModel */
        $expectedModel = Yii::createObject('app\models\manage\searchkey\SearchkeyCategory' . $id);
        $expectedModel = $expectedModel::find()->one();
        // 正しく取れているか
        verify($model->attributes)->equals($expectedModel->attributes);
        // itemsのrelation条件の確認
        $sort = 1;
        verify($model->items)->notEmpty();
        foreach ($model->items as $item) {
            verify($item->searchkey_category_id)->equals($model->id);
            verify($item->valid_chk)->equals(1);
            verify($item->sort)->greaterOrEquals($sort);
            $sort = $item->sort;
        }
    }
}
