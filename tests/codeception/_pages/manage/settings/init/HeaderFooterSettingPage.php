<?php

namespace tests\codeception\_pages\manage\settings\init;

use app\models\manage\HeaderFooterSetting;
use tests\codeception\_pages\manage\BaseGridPage;
use app\models\manage\ManageMenuMain;
use yii;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class HeaderFooterSettingPage extends BaseGridPage
{
    public $route = '/manage/secure/settings/list';

    /**
     * サイト設定画面から項目設定画面へ遷移する
     * その際にmenuも代入する
     * @param ManageMenuMain $menu
     */
    public function go($menu)
    {
        $this->actor->amGoingTo("{$menu->name}へ遷移");
        $this->actor->wait(3);
        $this->actor->click("//h4[contains(., '$menu->name')]"); // CSSセレクタでやると厄介そうなのでXPath使ってます
        $this->actor->wait(3);
        $this->actor->seeInTitle($menu->title);
        $this->actor->see($menu->title, 'h1');
    }

    /**
     * @param $i int
     */
    public function fillHeaderLink($i)
    {
        $this->actor->fillField("//*[@id='headerfootersetting-header_text{$i}']", "ヘッダーのテキストリンク更新の{$i}");
        if (($i % 2) == 0) {
            $this->actor->fillField("//*[@id='headerfootersetting-header_url{$i}']", "http://demo.job-maker.jp/?id={$i}");
        } else {
            $this->actor->fillField("//*[@id='headerfootersetting-header_url{$i}']", "/test{$i}.html");
        }
    }

    /**
     * @param $i int
     */
    public function fillFooterLink($i)
    {
        $this->actor->fillField("//*[@id='headerfootersetting-footer_text{$i}']", "フッターのテキストリンク更新の{$i}");
        if (($i % 2) == 0) {
            $this->actor->fillField("//*[@id='headerfootersetting-footer_url{$i}']", "http://demo.job-maker.jp/?id={$i}");
        } else {
            $this->actor->fillField("//*[@id='headerfootersetting-footer_url{$i}']", "/test{$i}.html");
        }
    }

    /**
     * @param $i int
     */
    public function seeHeaderForm($i)
    {
        $this->actor->seeInField("//*[@id='headerfootersetting-header_text{$i}']", "ヘッダーのテキストリンク更新の{$i}");
        if (($i % 2) == 0) {
            $this->actor->seeInField("//*[@id='headerfootersetting-header_url{$i}']", "http://demo.job-maker.jp/?id={$i}");
        } else {
            $this->actor->seeInField("//*[@id='headerfootersetting-header_url{$i}']", "/test{$i}.html");
        }
    }

    /**
     * @param $i int
     */
    public function seeFooterForm($i)
    {
        $this->actor->seeInField("//*[@id='headerfootersetting-footer_text{$i}']", "フッターのテキストリンク更新の{$i}");
        if (($i % 2) == 0) {
            $this->actor->seeInField("//*[@id='headerfootersetting-footer_url{$i}']", "http://demo.job-maker.jp/?id={$i}");
        } else {
            $this->actor->seeInField("//*[@id='headerfootersetting-footer_url{$i}']", "/test{$i}.html");
        }
    }

    /**
     * @param $i int
     */
    public function seeHeaderLink($i)
    {
        if (($i % 2) == 0) {
            $this->actor->seeLink("ヘッダーのテキストリンク更新の{$i}", "http://demo.job-maker.jp/?id={$i}");
        } else {
            $this->actor->seeLink("ヘッダーのテキストリンク更新の{$i}", "/test{$i}.html");
        }
    }

    /**
     * @param $i int
     */
    public function seeFooterLink($i)
    {
        if (($i % 2) == 0) {
            $this->actor->seeLink("フッターのテキストリンク更新の{$i}", "http://demo.job-maker.jp/?id={$i}");
        } else {
            $this->actor->seeLink("フッターのテキストリンク更新の{$i}", "/test{$i}.html");
        }
    }

    /**
     * テキストフォームに入力できる文字数超過のメソッドを追加
     *
     * @param string $attr
     * @param int $i
     * @param string $cha
     */
    public function seeTooManyCharacters($attr, $i, $cha = 'a')
    {
        $label = (new HeaderFooterSetting())->getAttributeLabel($attr);
        $this->actor->fillField("//*[@id='headerfootersetting-$attr']", str_repeat($cha, $i + 1));
        $this->actor->wait(1);
        $this->actor->see("{$label}は{$i}文字以下で入力してください。");
        $this->actor->fillField("//*[@id='headerfootersetting-$attr']", ''); //後のテスト工程に影響しないようにするため空にする
        $this->actor->wait(1);
    }

    /**
     * テキストフォームに入力できる文字数超過のメソッドを追加
     *
     * @param string $attr
     * @param string $str
     * @param string $mess
     */
    public function seeCharactersStyle($attr, $str, $mess)
    {
        $this->actor->fillField("//*[@id='headerfootersetting-$attr']", $str);
        $this->actor->wait(1);
        $this->actor->see($mess);
        $this->actor->fillField("//*[@id='headerfootersetting-$attr']", ''); //後のテスト工程に影響しないようにするため空にする
        $this->actor->wait(1);
    }
}