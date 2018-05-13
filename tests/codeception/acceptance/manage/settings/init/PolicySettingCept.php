<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\init\PolicyPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);
// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/settings/policy/list');
// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('規約設定検索のテスト');
//----------------------
// 運営元でログインして規約設定画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);
$page = PolicyPage::openBy($I);
$I->wait(3);
$page->go($menu);

//----------------------
// 適当に入力→検索→一覧
//----------------------
$I->amGoingTo('適当に入力→検索→一覧');
$I->fillField('#policysearch-policy', str_repeat('a', 5));
$I->click('この条件で表示する');
$I->wait(3);
$I->see($menu->title);
//todo 検索条件の確認

//----------------------
// クリア→一覧
//----------------------
$I->amGoingTo('クリア→一覧');
$I->click('クリア');
$I->wait(3);
$I->see($menu->title);

//----------------------
// 変更ボタン→変更画面→変更→完了→一覧画面へ
//----------------------
$I->amGoingTo('変更ボタン→変更画面→変更→完了');
$page->clickActionColumn(1, 1);
$I->wait(3);
$I->see('規約の編集');
//todo 内容変更時の、プレビュー・変更の確認
$I->click('変更');
$I->wait(1);
$I->click('OK');
$I->wait(3);
$I->see('作成・編集完了');
$I->click('規約設定一覧画面へ');
$I->wait(3);
$I->see($menu->title);

//----------------------
// 一覧画面→プレビュー画面
//----------------------
$I->amGoingTo('一覧画面→プレビュー画面');
$name = $I->grabTextFrom('.grid-view tbody tr:nth-child(1) td:nth-child(2)');
$page->clickActionColumn(1, 2);
$I->wait(3);
$I->see($menu->title);