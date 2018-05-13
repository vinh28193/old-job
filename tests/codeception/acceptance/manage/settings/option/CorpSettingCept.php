<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\option\OptionPage;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/option-corp/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('代理店項目設定のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でサイト設定画面へアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = OptionPage::openBy($I);
$page->go($menu);

//----------------------
// 代理店No(corp_no)
//----------------------
$I->amGoingTo('代理店No検証');
$page->openModal(1);
$page->submitModal();

//----------------------
// 代理店名(corp_name)
//----------------------
$I->amGoingTo('代理店名検証');
$page->openModal(2);
$page->submitModal();

//----------------------
// 電話番号(tel_no)
//----------------------
$I->amGoingTo('電話番号検証');
$page->openModal(3);
$page->submitModal();

//----------------------
// 担当者名(tanto_name)
//----------------------
$I->amGoingTo('担当者名検証');
$page->openModal(4);
$page->submitModal();

//----------------------
// オプション項目(option100～109)
// todo 詳細検証実装のタイミングでforeachで全部検証すべきか決める
//----------------------
$I->amGoingTo('オプション項目検証');
$page->openModal(6);
$page->submitModal();
