<?php

namespace tests\codeception\_pages\manage\settings\init;

use app\models\manage\ManageMenuMain;
use tests\codeception\_pages\manage\BaseGridPage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class HotJob extends BaseGridPage
{
    public $route = '/manage/secure/settings/list';
    /** @var ManageMenuMain */

    /**
     * サイト設定画面から項目設定画面へ遷移する
     * @param ManageMenuMain $menu
     */
    public function go($menu)
    {
        $this->actor->amGoingTo("{$menu->title}へ遷移");
        $this->actor->wait(1);
        $this->actor->click("//h4[text()='$menu->title']"); // CSSセレクタでやると厄介そうなのでXPath使ってます
    }
}
