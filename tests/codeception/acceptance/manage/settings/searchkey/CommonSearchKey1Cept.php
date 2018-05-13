<?php

use app\models\manage\SearchkeyMaster;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\searchkey\CommonSearchkeyPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('汎用検索キー1(～10)のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = CommonSearchkeyPage::openBy($I);
$I->wait(1);

// todo 全部有効にして全部で検証した方がいいかも
//for ($i = 1; $i <= 10; $i++) {
$i = 1;
$searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'searchkey_category' . $i]);
$page->go($searchkeyMaster);

//----------------------
// カテゴリ新規登録
//----------------------
$I->amGoingTo('カテゴリ新規登録');
$page->openModal('//thead/tr/th[1]/a', $page::CREATE); // CSSセレクタでやると厄介そうなのでXPath使ってます

// todo client sideの動きの確認

$page->fillCategoryAndRemember('searchkey_category_name', 'ceptカテゴリ名' . time());
$page->fillCategoryAndRemember('sort', 1);
$page->selectCategoryAndRemember('valid_chk', 1);

$page->submitModal($page::CREATE);
$page->reload();

//----------------------
// 項目新規登録
//----------------------
$I->amGoingTo('項目新規登録');
$page->openModal('//thead/tr/th[2]/a', $page::CREATE); // CSSセレクタでやると厄介そうなのでXPath使ってます

// todo client sideの動きの確認

$value = time();
$page->fillItemAndRemember(1, 'searchkey_item_name', '項目' . $value);
$page->fillItemAndRemember(1, 'sort', 1);
$page->selectItemAndRemember(1, 'valid_chk', 1);
$page->selectItemAndRemember(1, 'searchkey_category_id', $page->attributes['category']['searchkey_category_name']);

$page->submitModal($page::CREATE);
$page->reload();

// もう一つ登録する
$page->openModal('//thead/tr/th[2]/a', $page::CREATE); // CSSセレクタでやると厄介そうなのでXPath使ってます
$value = time();
$page->fillItemAndRemember(2, 'searchkey_item_name', '項目' . $value);
$page->fillItemAndRemember(2, 'sort', 2);
$page->selectItemAndRemember(2, 'valid_chk', 1);
$page->selectItemAndRemember(2, 'searchkey_category_id', $page->attributes['category']['searchkey_category_name']);

$page->submitModal($page::CREATE);

//----------------------
// カテゴリ更新
//----------------------
$I->amGoingTo('カテゴリ更新');
$page->openModal($page->attributes['category']['searchkey_category_name'], $page::UPDATE);
// todo 登録内容が正しく表示されているか
$page->submitModal($page::UPDATE);
$page->reload();

//----------------------
// 項目更新
//----------------------
$I->amGoingTo('項目更新');
$page->openModal($page->attributes['item'][1]['searchkey_item_name'], $page::UPDATE);
// todo 登録内容が正しく表示されているか
$page->submitModal($page::UPDATE);
$page->reload();

//----------------------
// 項目削除
//----------------------
$I->amGoingTo('項目削除');
$page->openModal($page->attributes['item'][1]['searchkey_item_name'], $page::UPDATE);
$page->delete();

$I->expect('項目が正常に項目削除できる');
$I->cantSee($page->attributes['item'][1]['searchkey_item_name']);
unset($page->attributes['item'][1]);
$page->reload();

//----------------------
// カテゴリ削除
//----------------------
$I->amGoingTo('カテゴリ削除');
$page->openModal($page->attributes['category']['searchkey_category_name'], $page::UPDATE);
$page->delete();

$I->expect('カテゴリが正常に削除できる');
$I->cantSee($page->attributes['category']['searchkey_category_name']);
unset($page->attributes['category']);
$I->expect('親を消したら子も消えている');
$I->cantSee($page->attributes['item'][2]['searchkey_item_name']);
unset($page->attributes['item'][2]);

//}