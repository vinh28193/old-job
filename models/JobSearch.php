<?php

namespace app\models;


use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\JobTypeCategory;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\PrefDistMaster;
use app\models\manage\searchkey\Station;
use app\models\manage\searchkey\WageCategory;
use app\models\manage\SearchkeyMaster;
use Yii;

/**
 * Class JobSearch
 * @package app\models
 * @author  Nobuhiro Ueda <ueda@tech-vein.com>
 *
 * @property boolean          $isArea
 * @property boolean          $isPref
 * @property boolean          $isStation
 * @property boolean          $isPrefDist
 * @property boolean          $isWage
 * @property boolean          $isJobType
 * @property boolean          $isCategory
 * @property boolean          $isItem
 * @property integer          $categoryId
 * @property integer          $itemId
 *
 * @property SearchCategory[] $categories
 * @property SearchItem[]     $items
 */
class JobSearch extends SearchkeyMaster
{
    /**
     * エリアテーブル名
     * @var string
     */
    private $tableArea;

    /**
     * 都道府県テーブル名
     * @var string
     */
    private $tablePref;

    /**
     * 都道府県テーブル名
     * @var string
     */
    private $tableStation;

    /**
     * 地域グループテーブル名
     * @var string
     */
    private $tablePrefDist;

    /**
     * 給与テーブル名
     * @var string
     */
    private $tableWage;

    /**
     * 職種テーブル名
     * @var string
     */
    private $tableJobCategory;

    /**
     * 初期処理
     */
    public function init()
    {
        parent::init();

        // 基本検索項目のテーブルをセット
        $this->tableArea = Area::tableName();
        $this->tablePref = Pref::tableName();
        $this->tableStation = Station::tableName();
        $this->tablePrefDist = PrefDistMaster::tableName();
        $this->tableWage = WageCategory::tableName();
        $this->tableJobCategory = JobTypeCategory::tableName();
    }

    /**
     * エリア項目か否か
     * @return bool
     */
    public function getIsArea()
    {
        return $this->table_name == $this->tableArea;
    }

    /**
     * 都道府県項目か否か
     * @return bool
     */
    public function getIsPref()
    {
        return $this->table_name == $this->tablePref;
    }

    /**
     * 路線駅項目か否か
     * @return bool
     */
    public function getIsStation()
    {
        return $this->table_name == $this->tableStation;
    }

    /**
     * 地域グループ項目か否か
     * @return bool
     */
    public function getIsPrefDist()
    {
        return $this->table_name == $this->tablePrefDist;
    }

    /**
     * 給与項目か否か
     * @return bool
     */
    public function getIsWage()
    {
        return $this->table_name == $this->tableWage;
    }

    /**
     * 職種項目か否か
     * @return bool
     */
    public function getIsJobType()
    {
        return $this->table_name == $this->tableJobCategory;
    }

    /**
     * カテゴリか否か
     * @return bool
     */
    public function getIsCategory()
    {
        return strpos($this->table_name, SearchCategory::TABLE_BASE) === 0 ? true : false;
    }

    /**
     * アイテムか否か
     * @return bool
     */
    public function getIsItem()
    {
        return strpos($this->table_name, SearchItem::TABLE_BASE) === 0 ? true : false;
    }

    /**
     * 1階層目カテゴリ項目を返す
     * @return null|array|\yii\db\ActiveRecord[]
     */
    public function getCategories()
    {
        if (!$this->isCategory) {
            return null;
        }

        SearchCategory::setTableId($this->categoryId);
        return SearchCategory::find()->where(['valid_chk' => self::FLAG_VALID])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
    }

    /**
     * 1階層目アイテム項目を返す
     * @return null|array|\yii\db\ActiveRecord[]
     */
    public function getItems()
    {
        if (!$this->isItem) {
            return null;
        }

        SearchItem::setTableId($this->itemId);
        return SearchItem::find()->where(['valid_chk' => self::FLAG_VALID])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
    }

    /**
     * category検索クエリを返す
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryQuery()
    {
        if (!$this->isCategory) {
            return null;
        }

        SearchCategory::setTableId($this->categoryId);
        return SearchCategory::find()->where(['valid_chk' => self::FLAG_VALID])
            ->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * item検索クエリを返す
     * @return \yii\db\ActiveQuery
     */
    public function getItemQuery()
    {
        SearchItem::setTableId($this->itemId);
        return SearchItem::find()->where(['valid_chk' => self::FLAG_VALID])
            ->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * カテゴリテーブル名からカテゴリIDを返す
     * @return int|null
     */
    public function getCategoryId()
    {
        if (!$this->isCategory) {
            return null;
        }

        return (int)str_replace(SearchCategory::TABLE_BASE, '', $this->table_name);
    }

    /**
     * カテゴリテーブル名からアイテムIDを返す
     * @return int|null
     */
    public function getItemId()
    {
        if (!$this->isItem) {
            return null;
        }

        return (int)str_replace(SearchItem::TABLE_BASE, '', $this->table_name);
    }
}