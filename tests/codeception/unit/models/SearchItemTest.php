<?php
/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/03
 * Time: 18:22
 */

namespace models\manage;

use yii;
use app\models\SearchItem;
use tests\codeception\unit\JmTestCase;

class SearchItemTest extends JmTestCase
{
    /**
     * tableNameのtest
     */
    public static function testTableName()
    {
        self::tableNameCheck(1);
        self::tableNameCheck(2);
        self::tableNameCheck(11);
    }

    /**
     * 指定した番号の汎用検索キーでの、tableNameのtest
     * @param int $id
     */
    private static function tableNameCheck($id)
    {
        // tebleNameのtest
        SearchItem::setTableId($id);
        verify(SearchItem::tableName())->equals(SearchItem::TABLE_BASE . $id);

        // 実際にfindして、モデルを取得できているか。
        $model = SearchItem::find()->one();
        /** @var yii\db\ActiveRecord $expectModel */
        $expectModel = Yii::createObject('app\models\manage\searchkey\SearchkeyItem'.$id);
        $expectModel = $expectModel::find()->one();
        verify($model->attributes)->equals($expectModel->attributes);
    }

    public static function testSetTableId()
    {
        SearchItem::setTableId(99999);
        verify(SearchItem::$tableId)->equals(99999);
    }
}
