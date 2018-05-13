<?php

namespace tests\codeception\_pages\manage\settings\searchkey;


/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class JobTypePage extends BaseSearchkeyPage
{
    /**
     * 大カテゴリ入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * @param $attribute
     * @param $value
     */
    public function fillBigCategoryAndRemember($attribute, $value)
    {
        $this->actor->fillField("#jobtypecategory-$attribute", $value);//
        $this->attributes['category'][$attribute] = $value;
    }

    /**
     * カテゴリ入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * @param $key
     * @param $attribute
     * @param $value
     */
    public function fillCategoryAndRemember($key, $attribute, $value)
    {
        $this->actor->fillField("#jobtypebig-$attribute", $value);
        $this->attributes['big'][$key][$attribute] = $value;
    }

    /**
     * 項目入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * @param $key
     * @param $attribute
     * @param $value
     */
    public function fillItemAndRemember($key, $attribute, $value)
    {
        $this->actor->fillField("#jobtypesmall-$attribute", $value);
        $this->attributes['small'][$key][$attribute] = $value;
    }
}
