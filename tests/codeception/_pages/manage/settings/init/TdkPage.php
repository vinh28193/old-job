<?php

namespace tests\codeception\_pages\manage\settings\init;

use app\models\manage\ManageMenuMain;
use tests\codeception\_pages\manage\BaseGridPage;
use RemoteWebDriver;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class TdkPage extends BaseGridPage
{
    public $route = '/manage/secure/settings/list';
    /** @var ManageMenuMain */
    public $menu;

    /**
     * サイト設定画面から項目設定画面へ遷移する
     * その際にmenuも代入する
     * @param ManageMenuMain $menu
     */
    public function go($menu)
    {
        $this->actor->amGoingTo("{$menu->title}へ遷移");
        $this->menu = $menu;
        $this->actor->wait(1);
        $this->actor->click("//h4[text()='$menu->title']"); // CSSセレクタでやると厄介そうなのでXPath使ってます
        $this->actor->wait(3);
        $this->actor->seeInTitle($menu->title);
        $this->actor->see($menu->title, 'h1');
    }

    /**
     * n行目の変更ボタンを押してモーダルを表示する
     * @param int $row
     */
    public function openModal($row)
    {
        $this->clickActionColumn($row, 1);
        $this->actor->wait(1);
        $this->actor->see('項目変更', 'div.modal-header');
    }

    /**
     * モーダルのsubmitが正常に行われていることを検査
     */
    public function submitModal()
    {
        // 変更をクリック
        $this->actor->click('変更');
        $this->actor->wait(4);
        // 完了メッセージが出ている
        $this->actor->see('更新が完了しました', 'p');
        // ちゃんと元の画面に戻っている
        $this->actor->seeInTitle($this->menu->title);
        $this->actor->see($this->menu->title, 'h1');
        // モーダルが消えている
        $this->actor->cantSeeElement('#modal');
        // リロードすると完了メッセージが消えている
        $this->actor->reloadPage();
        $this->actor->wait(3);
        $this->actor->cantSee('更新が完了しました');
    }

    /**
     * 元のCSV一括登録のwindowを開く処理（window.nameが取得できないため下記処理にしている）
     */
    public function openWindow()
    {
        $this->actor->executeInSelenium(function (RemoteWebDriver $webDriver) {
            $handles = $webDriver->getWindowHandles();
            $firstWindow = reset($handles); // CSV一括登録のwindow
            $webDriver->switchTo()->window($firstWindow);
        });
    }
}
