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
$menu = ManageMenuMain::findFromRoute('/manage/secure/option-inquiry/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('掲載問い合わせ項目設定検索のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = OptionPage::openBy($I);
$page->go($menu);

//----------------------
// company_name, post_name. tanto_name. job_type
// 代表してcompany_name
// todo 詳細検証実装のタイミングでforeachで全部検証すべきか決める
//----------------------
$I->amGoingTo('企業名検証');
$page->openModal(1);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 郵便番号(postal_code)
//----------------------
$I->amGoingTo('郵便番号検証');
$page->openModal(5);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// address. fax_no
// 代表してaddress
// todo 詳細検証実装のタイミングでforeachで全部検証すべきか決める
//----------------------
$I->amGoingTo('ご住所');
$page->openModal(6);
// todo 詳細検証実装
$page->submitModal();

//----------------------
//ご連絡先電話番号(tel_no)
//----------------------
$I->amGoingTo('ご連絡線電話番後');
$page->openModal(7);
$page->submitModal();

//----------------------
// 	ご連絡先メールアドレス(mail_address)
//----------------------
$I->amGoingTo('ご連絡先メールアドレス');
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