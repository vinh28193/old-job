<?php

namespace tests\codeception\_pages\manage\settings\searchkey;


/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class WagePage extends BaseSearchkeyPage
{
    /**
     * カテゴリ入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * @param $attribute
     * @param $value
     */
    public function fillCategoryAndRemember($attribute, $value)
    {
        $this->actor->fillField("#wagecategory-$attribute", $value);
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
        $this->actor->fillField("#wageitem-$attribute", $value);
        $this->attributes['item'][$key][$attribute] = $value;
    }
}
