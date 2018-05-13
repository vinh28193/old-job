<?php

namespace tests\codeception\_pages\manage\settings\searchkey;


/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class DistGroupPage extends BaseSearchkeyPage
{
    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return parent::__get($name);
    }

    /**
     * 入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * @param $attribute
     * @param $value
     */
    public function fillAndRemember($attribute, $value)
    {
        $this->actor->fillField("#prefdistmaster-$attribute", $value);
        $this->attributes[$attribute] = $value;
    }
}
