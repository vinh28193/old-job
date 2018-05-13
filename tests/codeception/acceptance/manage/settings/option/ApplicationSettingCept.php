<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use app\models\manage\ApplicationColumnSet;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\option\OptionPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/option-application/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// child番号
$child = 1;

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('応募者項目設定検索のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = OptionPage::openBy($I);
$page->go($menu);

//----------------------
// 応募者ID
//----------------------
$I->amGoingTo('応募No検証');
$page->openModal($child);
$page->checkInputOfExplain(false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 代理店名
//----------------------
$I->amGoingTo('代理店名2検証');
$page->openModal(++$child);
$page->checkInputOfExplain(false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 掲載企業名
//----------------------
$I->amGoingTo('掲載企業名検証');
$page->openModal(++$child);
$page->checkInputOfExplain(false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 氏名
//----------------------
$I->amGoingTo('氏名検証');
$page->openModal(++$child);
$page->chkInputSpecialCharacterFullName();
$page->checkInputOfFullName(ApplicationColumnSet::MAX_LENGTH_FULLNAME);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// フリガナ
//----------------------
$I->amGoingTo('フリガナ検証');
$page->openModal(++$child);
$page->chkInputSpecialCharacterFullName();
$page->checkInputOfFullName(ApplicationColumnSet::MAX_LENGTH_FULLNAME);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 性別
//----------------------
$I->amGoingTo('性別検証');
$page->openModal(++$child);
$page->checkInputOfExplain(false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 生年月日
//----------------------
$I->amGoingTo('生年月日検証');
$page->openModal(++$child);
$page->checkInputOfExplain(false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 都道府県
//----------------------
$I->amGoingTo('都道府県検証');
$page->openModal(++$child);
$page->checkInputOfExplain(false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 住所(市区町村以降)
//----------------------
$I->amGoingTo('住所検証');
$page->openModal(++$child);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, ApplicationColumnSet::MAX_LENGTH_OTHER);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 電話番号
//----------------------
$I->amGoingTo('電話番号検証');
$page->openModal(++$child);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, ApplicationColumnSet::MAX_LENGTH_TELMAIL, false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// メールアドレス
//----------------------
$I->amGoingTo('メールアドレス検証');
$page->openModal(++$child);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, ApplicationColumnSet::MAX_LENGTH_TELMAIL, false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 現在の職業
//----------------------
$I->amGoingTo('現在の職業検証');
$page->openModal(++$child);
$page->checkInputOfExplain(false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// PR
//----------------------
$I->amGoingTo('PR検証');
$page->openModal(++$child);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, ApplicationColumnSet::MAX_LENGTH_OTHER);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 応募機器
//----------------------
$I->amGoingTo('応募機器検証');
$page->openModal(++$child);
$page->checkInputOfExplain(false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 応募日
//----------------------
$I->amGoingTo('応募日検証');
$page->openModal(++$child);
$page->checkInputOfExplain(false);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 状況
//----------------------
$I->amGoingTo('状況検証');
$page->openModal(++$child);
$page->checkInputOfExplain(false);
// todo 詳細検証実装
$page->submitModal();

// ----------------------
// オプション項目(option100)
// ----------------------
$I->amGoingTo('オプション項目検証');
$page->openModal(++$child);
$page->chkInputSpecialCharacter();
$page->optionColumnExplain(ApplicationColumnSet::MAX_LENGTH_OTHER);
// todo 詳細検証実装
$page->submitModal();

// ----------------------
// オプション項目(option101) ※4個以上の選択肢のチェックボックスを作成。
// ----------------------
$I->amGoingTo('オプション項目検証事前準備');
$page->openModal(++$child);
$page->createCheckBox(4);
$page->submitModal();
$I->amGoingTo('オプション項目検証');
$page->openModal($child);
$page->chkInputSubsetName(255, false);
$page->optionChangeDataType(ApplicationColumnSet::MAX_LENGTH_OTHER);
// todo 詳細検証実装
$page->submitModal();

// ----------------------
// オプション項目(option102) ※チェックボックス（ラジオ）以外
// ----------------------
$I->amGoingTo('オプション項目検証');
$page->openModal(++$child);
$page->chkInputSubsetName(255, true);
// todo 詳細検証実装
$page->submitModal();
