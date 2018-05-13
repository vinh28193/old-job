<?php

namespace tests\codeception\_pages\manage\settings\init;

use app\models\manage\ManageMenuMain;
use tests\codeception\_pages\manage\BaseGridPage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class MailSettingPage extends BaseGridPage
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

        $this->actor->click("//h4[contains(., \"{$menu->title}\")]"); // CSSセレクタでやると厄介そうなのでXPath使ってます
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
        $this->actor->wait(2);
        $this->actor->see('メール設定変更', 'div.modal-header');
    }

    /**
     * モーダルのsubmitが正常に行われていることを検査
     */
    public function submitModal()
    {
        // 変更をクリック
        $this->actor->click('変更');
        $this->actor->wait(4);
        // ちゃんと元の画面に戻っている
        $this->actor->seeInTitle($this->menu->title);
        $this->actor->see($this->menu->title, 'h1');
        // モーダルが消えている
        $this->actor->cantSeeElement('#modal');
    }

    /**
     * モーダルが閉じられることを検査
     */
    public function closeModal()
    {
        // 変更をクリック
        $this->actor->click('閉じる');
        $this->actor->wait(4);
        // ちゃんと元の画面に戻っている
        $this->actor->seeInTitle($this->menu->title);
        $this->actor->see($this->menu->title, 'h1');
        // モーダルが消えている
        $this->actor->cantSeeElement('#modal');
    }

    /**
     * Email制約のチェック
     * @param $field
     */
    public function checkRuleEmail($field)
    {
        $this->actor->amGoingTo("{$field}はメールアドレス制約がある");
        $this->actor->fillField($field, 'aaaaa');
        $this->actor->wait(1);
        $this->actor->see('正しいメールアドレス表記で入力してください。');
    }

    /**
     * 最大文字数254文字のEmail制約のチェック
     * @param $field
     */
    public function checkRuleOver255Email($field)
    {
        $over255Email = str_repeat('a', 64) . "@" . str_repeat('b', 100) . "." . str_repeat('c', 255 - 64 - 1 - 100 - 1);

        $this->actor->amGoingTo("{$field}は最大254文字の制限がある");
        $this->actor->fillField($field, $over255Email);
        $this->actor->wait(1);
        $this->actor->see("{$field}は254文字以下で入力してください。");
    }
}
