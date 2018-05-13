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
$menu = ManageMenuMain::findFromRoute('/manage/secure/option-admin/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('管理者項目設定検索のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = OptionPage::openBy($I);
$page->go($menu);

//----------------------
// 管理者No(admin_no)
//----------------------
$I->amGoingTo('管理者No検証');
$page->openModal(1);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 代理店名(corp_master_id)
//----------------------
$I->amGoingTo('代理店名検証');
$page->openModal(2);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 掲載企業名(client_master_id)
//----------------------
$I->amGoingTo('掲載企業名検証');
$page->openModal(3);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// フルネーム(name_sei,name_mei)
//----------------------
$I->amGoingTo('フルネーム検証');
$page->openModal(4);
$page->submitModal();

//----------------------
// ログインID
//----------------------
$I->amGoingTo('login_id検証');
$page->openModal(5);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// パスワード(password)
//----------------------
$I->amGoingTo('パスワード検証');
$page->openModal(6);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 電話番号(tel_no)
//----------------------
$I->amGoingTo('電話番号検証');
$page->openModal(7);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 除外する管理権限
//----------------------
$I->amGoingTo('除外する管理権限検証');
$page->openModal(8);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// メールアドレス(mail_address)
//----------------------
$I->amGoingTo('メールアドレス検証');
$page->openModal(9);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// オプション項目(option100～109)
// todo 詳細検証実装のタイミングでforeachで全部検証すべきか決める
//----------------------
$I->amGoingTo('オプション項目検証');
$page->openModal(10);
// todo 詳細検証実装
$page->submitModal();