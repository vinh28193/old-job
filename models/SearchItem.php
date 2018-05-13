<?php

namespace app\models;

use app\models\manage\searchkey\SearchkeyItem;
use Yii;

/**
 * Class SearchItem
 * @package app\models
 */
class SearchItem extends SearchkeyItem
{
    const ITEM_MAX = 20;

    /**
     * テーブルベース文字列
     */
    const TABLE_BASE = 'searchkey_item';

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
}
