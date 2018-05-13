<?php

namespace tests\codeception\_pages\manage\settings\searchkey;

use app\models\manage\SearchkeyMaster;
use tests\codeception\_pages\manage\BaseGridPage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class CommonSearchkeyPage extends BaseSearchkeyPage
{
    private $categoryInputId;
    private $itemInputId;

    public function getCommonId()
    {
        return str_replace(['searchkey_category', 'searchkey_item'], ['', ''], $this->searchKey->table_name);
    }

    public function go($searchKey)
    {
        parent::go($searchKey);
        $id = $this->getCommonId();
        $this->categoryInputId = '#searchkeycategory' . $id;
        $this->itemInputId = '#searchkeyitem' . $id;
    }

    /**
     * カテゴリ入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * @param $attribute
     * @param $value
     */
    public function fillCategoryAndRemember($attribute, $value)
    {
        $this->actor->fillField($this->categoryInputId . "-" . $attribute, $value);
        $this->attributes['category'][$attribute] = $value;
    }

    public function selectCategoryAndRemember($attribute, $value)
    {
        $this->actor->selectOption($this->categoryInputId . "-" . $attribute, $value);
        $this->attributes['category'][$attribute] = $value;
    }

    /**
     * 項目入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * @param $attribute
     * @param $value
     */
    public function fillItemAndRemember($key, $attribute, $value)
    {
        $this->actor->fillField($this->itemInputId . "-" . $attribute, $value);
        $this->attributes['item'][$key][$attribute] = $value;
    }

    public function selectItemAndRemember($key, $attribute, $value)
    {
        $this->actor->selectOption($this->itemInputId . "-" . $attribute, $value);
        $this->attributes['item'][$key][$attribute] = $value;
    }
}