<?php

namespace tests\codeception\_pages\manage\application;

use tests\codeception\_pages\manage\BaseRegisterPage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 * todo propertyのdoc comment書く
 */
class ApplicationRegisterPage extends BaseRegisterPage
{
    public $route = 'manage/secure/application/list';

    /**
     * 応募者情報の入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * todo まとめられそうならBaseRegisterにまとめる
     * @param $attribute
     * @param $value
     */
    public function fillApplicationAndRemember($attribute, $value)
    {
        $this->actor->fillField("#applicationmaster-$attribute", $value);
        $this->attributes['application'][$attribute] = $value;
    }

    /**
     * 応募者へ送信するメールの入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * @param $attribute
     * @param $value
     */
    public function fillMailAndRemember($attribute, $value)
    {
        $this->actor->fillField("#mailsend-$attribute", $value);
        $this->attributes['mail'][$attribute] = $value;
    }
}
