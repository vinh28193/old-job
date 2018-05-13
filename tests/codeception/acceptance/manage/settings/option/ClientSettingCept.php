<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\option\OptionPage;
use app\models\manage\ClientColumnSet;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/option-client/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('掲載企業項目設定検索のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = OptionPage::openBy($I);
$page->go($menu);

//----------------------
// 掲載企業No.(client_no)
//----------------------
$I->amGoingTo('掲載企業No検証');
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
// 掲載企業名(client_name)
//----------------------
$I->amGoingTo('掲載企業名検証');
$page->openModal(3);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// client_name_kana. address. tanto_name. client_business_outline
// 代表してclient_name_kana
// todo 詳細検証実装のタイミングでforeachで全部検証すべきか決める
//----------------------
$I->amGoingTo('client_name_kana検証');
$page->openModal(4);
// todo 詳細検証実装

//DBから初期状態で「使用する」「使用しない」どちらか判定
$validChkArray = ClientColumnSet::find()->select('valid_chk')->where(['column_name' => 'client_name_kana'])->column();
$I->seeCheckboxIsChecked("//input[@name='ClientColumnSet[valid_chk]'][@value='$validChkArray[0]']");
$I->amGoingTo('変更可能項目を変更');
$value = $validChkArray[0] == ClientColumnSet::VALID ? ClientColumnSet::INVALID : ClientColumnSet::VALID;
$I->selectOption("//input[@name='ClientColumnSet[valid_chk]']", $value);
$page->submitModal();
$I->amGoingTo('変更が反映されているかを確認');
$page->openModal(4);//もう一回開く
$I->seeCheckboxIsChecked("//input[@name='ClientColumnSet[valid_chk]'][@value='$value']");
$page->submitModal();

//----------------------
// 電話番号(tel_no)
//----------------------
$I->amGoingTo('電話番号検証');
$page->openModal(7);
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
