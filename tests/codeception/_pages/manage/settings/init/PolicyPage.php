<?php

namespace tests\codeception\_pages\manage\settings\init;

use tests\codeception\_pages\manage\BaseGridPage;
use app\models\manage\ManageMenuMain;
/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class PolicyPage extends BaseGridPage
{
    public $route = '/manage/secure/settings/list';

    /**
     * サイト設定画面から項目設定画面へ遷移する
     * その際にmenuも代入する
     * @param ManageMenuMain $menu
     */
    public function go($menu)
    {
        $this->actor->amGoingTo("{$menu->title}へ遷移");
        $this->actor->wait(1);
        $this->actor->click("//h4[text()='$menu->title']"); // CSSセレクタでやると厄介そうなのでXPath使ってます
        $this->actor->wait(3);
        $this->actor->seeInTitle($menu->title);
        $this->actor->see($menu->title, 'h1');
    }
}
