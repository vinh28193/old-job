<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/option-disptype/list');

// 掲載タイプ名
$typeName = date('Ymdhis') . 'タイプ';

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('掲載タイプ項目設定変更のテスト');

//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);

$I->amGoingTo('掲載タイプ項目設定ページへ移動');
$I->click('//ul[@class="drawer-menu"]/li/a[@href="/manage/secure/settings/list"]');
$I->wait(3);
$I->click('//h4[text()="掲載タイプ項目設定"]');
$I->wait(3);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');
$I->dontSee($typeName);


$I->amGoingTo('掲載タイプ項目を変更する');
$I->click('//*[@id="w2"]/div/table/tbody/tr/td/a[@title="変更"]');
$I->wait(3);
$I->fillField('//*[@id="disptype-disp_type_name"]', $typeName);
$I->wait(3);
$I->click('//div[@class="modal-footer"]/button[text()="変更"]');
$I->wait(3);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');
$I->see('更新が完了しました。');
$I->see($typeName);

