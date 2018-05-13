<?php

namespace tests\codeception\_pages\manage\corp;

use tests\codeception\_pages\manage\BaseRegisterPage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 * todo propertyのdoc comment書く
 */
class CorpRegisterPage extends BaseRegisterPage
{
    public $route = 'manage/secure/corp/list';

    /**
     * 登録・変更する
     * todo まとめられそうならBaseRegisterにまとめる
     * @param string $action 登録 or 変更
     * @param boolean $confirm 確認まで処理するかどうか
     * @param boolean $success 成功チェックするかどうか
     */
    public function submit($action, $confirm = true, $success = true)
    {
        $this->actor->amGoingTo($action);
        $this->actor->click("{$action}する");
        $this->actor->wait(1);
        if (!$confirm) {
            return;
        }
        $this->actor->see("代理店情報を{$action}してもよろしいですか？");
        $this->actor->click('OK');
        $this->actor->wait(2);
        if (!$success) {
            return;
        }
        $this->actor->seeInTitle('代理店情報-完了');
        $this->actor->see("{$action}完了", 'h1');
        if ($action == '登録') {
            $this->actor->see("代理店情報が{$action}されました", 'p');
        } elseif ($action == '変更') {
            $this->actor->see("代理店情報の内容が{$action}されました", 'p');
        }
    }

    /**
     * 入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * todo まとめられそうならBaseRegisterにまとめる
     * @param $attribute
     * @param $value
     */
    public function fillAndRemember($attribute, $value)
    {
        $this->actor->fillField("#corpmaster-$attribute", $value);
        $this->attributes[$attribute] = $value;
    }
}
