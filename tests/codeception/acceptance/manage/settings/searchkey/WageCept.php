<?php
use app\models\manage\SearchkeyMaster;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\searchkey\WagePage;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);
// メニュー情報取得
$menu = SearchkeyMaster::findOne(['table_name' => 'wage_category']);
// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);
// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('給与検索キー設定のテスト');
//----------------------
// 運営元でログインして検索キー設定画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = WagePage::openBy($I);
$I->wait(1);
$page->go($menu);

//----------------------
// カテゴリ新規登録
//----------------------
$I->amGoingTo('カテゴリ新規登録');
$page->openModal('//thead/tr/th[1]/a', $page::CREATE); // CSSセレクタでやると厄介そうなのでXPath使ってます

// todo client sideの動きの確認

$page->fillCategoryAndRemember('wage_category_name', 'ceptカテゴリ名');
$page->fillCategoryAndRemember('sort', 1);
$I->selectOption('input[name=WageCategory\\[valid_chk\\]]', 1);

$page->submitModal($page::CREATE);
$page->reload();

//----------------------
// 項目新規登録
//----------------------
$I->amGoingTo('項目新規登録');
$page->openModal('//thead/tr/th[2]/a', $page::CREATE); // CSSセレクタでやると厄介そうなのでXPath使ってます

// todo client sideの動きの確認

$value = time();
$page->fillItemAndRemember(1, 'disp_price', $value . '円以上');
$page->fillItemAndRemember(1, 'wage_item_name', 1000000000);
$I->selectOption('input[name=WageItem\\[valid_chk\\]]', 1);
$I->selectOption('#wageitem-wage_category_id', 'ceptカテゴリ名');

$page->submitModal($page::CREATE);
$page->reload();

// もう一つ登録する
$page->openModal('//thead/tr/th[2]/a', $page::CREATE); // CSSセレクタでやると厄介そうなのでXPath使ってます

$value = time();
$page->fillItemAndRemember(2, 'disp_price', $value . '円以上');
$page->fillItemAndRemember(2, 'wage_item_name', 1000000);
$I->selectOption('input[name=WageItem\\[valid_chk\\]]', 1);
$I->selectOption('#wageitem-wage_category_id', 'ceptカテゴリ名');

$page->submitModal($page::CREATE); // 2回目はリロード検査不要

//----------------------
// カテゴリ更新
//----------------------
$I->amGoingTo('カテゴリ更新');
$page->openModal($page->attributes['category']['wage_category_name'], $page::UPDATE);
// todo 登録内容が正しく表示されているか
$page->submitModal($page::UPDATE);
$page->reload();

//----------------------
// 項目更新
//----------------------
$I->amGoingTo('項目更新');
$page->openModal($page->attributes['item'][1]['disp_price'], $page::UPDATE);
// todo 登録内容が正しく表示されているか
$page->submitModal($page::UPDATE);
$page->reload();

//----------------------
// 項目削除
//----------------------
$I->amGoingTo('項目削除');
$page->openModal($page->attributes['item'][1]['disp_price'], $page::UPDATE);
$page->delete();

$I->expect('項目が正常に項目削除できている');
$I->cantSee($page->attributes['item'][1]['disp_price']);
unset($page->attributes['item'][1]);
$page->reload();

//----------------------
// カテゴリ削除
//----------------------
$I->amGoingTo('カテゴリ削除');
$page->openModal($page->attributes['category']['wage_category_name'], $page::UPDATE);
$page->delete();

$I->expect('カテゴリが正常に削除できている');
$I->cantSee($page->attributes['category']['wage_category_name']);
unset($page->attributes['category']);
$I->expect('親を消したら子も消えている');
$I->cantSee($page->attributes['item'][2]['disp_price']);
unset($page->attributes['item'][2]);

//----------------------
// 項目登録 給与検索キーの最大値設定
//----------------------
$I->amGoingTo('給与検索キーのvalidationチェック');
$page->openModal('//thead/tr/th[2]/a', $page::CREATE); // CSSセレクタでやると厄介そうなのでXPath使ってます

$page->fillItemAndRemember(3, 'wage_item_name', 1000000001);
$I->wait(2);
$I->see('金額は"1000000000"以下の数字で入力してください。');
