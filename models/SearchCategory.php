<?php

namespace app\models;

use app\models\manage\searchkey\SearchkeyCategory;
use Yii;

/**
 * Class SearchCategory
 * @package app\models
 *
 * @property SearchItem[] $items
 */
class SearchCategory extends SearchkeyCategory
{
    /**
     * テーブルベース文字列
     */
    const TABLE_BASE = 'searchkey_category';
    /**
     * 自由設定項目アイテムカテゴリ上限
     */
    const CATEGORY_MAX = 10;

    /**
     * テーブル名
     * @var string
     */
    public static $tableId;

    /**
     * テーブル名を返す
     * @return string
     */
    public static function tableName()
    {
        return self::TABLE_BASE . self::$tableId;
    }

    /**
     * テーブルIDを更新する
     * @param $tableId
     */
    public static function setTableId($tableId)
    {
        self::$tableId = $tableId;
    }

    /**
     * @return \yii\db\ActiveQuery|null
     */
    public function getItems()
    {
        if (self::$tableId > self::CATEGORY_MAX) {
            return null;
        }

        SearchItem::setTableId(self::$tableId);

        return $this->hasMany(SearchItem::className(), ['searchkey_category_id' => 'id'])
            ->where([SearchItem::tableName() . '.valid_chk' => JobSearch::FLAG_VALID])
            ->orderBy([SearchItem::tableName() . '.sort' => SORT_ASC]);
    }
}
