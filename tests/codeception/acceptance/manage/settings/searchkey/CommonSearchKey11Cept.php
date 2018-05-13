<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\searchkey\CommonSearchkeyPage;
use app\models\manage\SearchkeyMaster;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/searchkey11/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('汎用検索キー11(～20)のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = CommonSearchkeyPage::openBy($I);
$I->wait(1);

//for ($i = 11; $i <= 20; $i++) {
$i = 11;
$searchkeyMaster = SearchkeyMaster::findOne(['table_name' => 'searchkey_item' . $i]);
$page->go($searchkeyMaster);

//----------------------
// 項目新規登録
//----------------------
$I->amGoingTo('項目新規登録');
$page->openModal('//thead/tr/th[1]/a', $page::CREATE); // CSSセレクタでやると厄介そうなのでXPath使ってます

// todo client sideの動きの確認

$page->fillItemAndRemember($i, 'searchkey_item_name', 'ceptカテゴリ名');
$page->fillItemAndRemember($i, 'sort', 1);
$page->selectItemAndRemember($i, 'valid_chk', 1);

$page->submitModal($page::CREATE);

//----------------------
// 項目更新
//----------------------
$I->amGoingTo('項目更新');
$page->openModal($page->attributes['item'][$i]['searchkey_item_name'], $page::UPDATE);
// todo 登録内容が正しく表示されているか
$page->submitModal($page::UPDATE);
$page->reload();

//----------------------
// 項目更新
//----------------------
$I->amGoingTo('項目削除');
$page->openModal($page->attributes['item'][$i]['searchkey_item_name'], $page::UPDATE);
$page->delete();

$I->expect('項目が正常に項目削除できる');
$I->cantSee($page->attributes['item'][$i]['searchkey_item_name']);
unset($page->attributes['item'][$i]);
$page->reload();
//}
