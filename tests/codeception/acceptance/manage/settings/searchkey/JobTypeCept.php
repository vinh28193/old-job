<?php
use app\models\manage\SearchkeyMaster;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\searchkey\JobTypePage;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);
// メニュー情報取得
$menu = SearchkeyMaster::findOne(['table_name' => 'job_type_category']);
// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('職種検索キーのテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);
$page = JobTypePage::openBy($I);
$I->wait(3);
$page->go($menu);

//----------------------
// 大カテゴリ新規登録
//----------------------
$I->amGoingTo('大カテゴリ新規登録');
$page->openModal('大カテゴリ', $page::CREATE);

// todo client sideの動きの確認

$page->fillBigCategoryAndRemember('name', 'cept大カテゴリ名');
$page->fillBigCategoryAndRemember('sort', 1);
$I->selectOption('input[name=JobTypeCategory\\[valid_chk\\]]', 1);

$page->submitModal($page::CREATE);
$page->reload();

//----------------------
// カテゴリ新規登録
//----------------------
$I->amGoingTo('カテゴリ新規登録');
$page->openModal('カテゴリ', $page::CREATE);

// todo client sideの動きの確認

$value = time();
$page->fillCategoryAndRemember(1, 'job_type_big_name', $value . 'カテゴリ');
$page->fillCategoryAndRemember(1, 'sort', 1);
$I->selectOption('input[name=JobTypeBig\\[valid_chk\\]]', 1);
$I->selectOption('#jobtypebig-job_type_category_id', 'cept大カテゴリ名');

$page->submitModal($page::CREATE);
$page->reload();

// もう一つ登録する
$page->openModal('カテゴリ', $page::CREATE);

$value = time();
$page->fillCategoryAndRemember(2, 'job_type_big_name', $value . 'カテゴリ');
$page->fillCategoryAndRemember(2, 'sort', 1);
$I->selectOption('input[name=JobTypeBig\\[valid_chk\\]]', 1);
$I->selectOption('#jobtypebig-job_type_category_id', 'cept大カテゴリ名');

$page->submitModal($page::CREATE); // 2回目はリロード検査不要

//----------------------
// 項目新規登録
//----------------------
$I->amGoingTo('項目新規登録');
$page->openModal('項目', $page::CREATE);

// todo client sideの動きの確認

$value = time();
$page->fillItemAndRemember(1,'job_type_small_name', $value . '項目');
$page->fillItemAndRemember(1, 'sort', 1);
$I->selectOption('input[name=JobTypeSmall\\[valid_chk\\]]', 1);
$I->selectOption('#jobtypesmall-job_type_big_id', $page->attributes['big'][1]['job_type_big_name']);

$page->submitModal($page::CREATE);
$page->reload();

// もう一つ登録する
$page->openModal('項目', $page::CREATE);

$value = time();
$page->fillItemAndRemember(2,'job_type_small_name', $value . '項目');
$page->fillItemAndRemember(2, 'sort', 1);
$I->selectOption('input[name=JobTypeSmall\\[valid_chk\\]]', 1);
$I->selectOption('#jobtypesmall-job_type_big_id', $page->attributes['big'][1]['job_type_big_name']);

$page->submitModal($page::CREATE);

// 別のカテゴリ一つ登録する
$page->openModal('項目', $page::CREATE);

$value = time();
$page->fillItemAndRemember(3,'job_type_small_name', $value . '項目');
$page->fillItemAndRemember(3, 'sort', 1);
$I->selectOption('input[name=JobTypeSmall\\[valid_chk\\]]', 1);
$I->selectOption('#jobtypesmall-job_type_big_id', $page->attributes['big'][2]['job_type_big_name']);

$page->submitModal($page::CREATE);
//----------------------
// 大カテゴリ更新
//----------------------
$I->amGoingTo('大カテゴリ更新');
$page->openModal($page->attributes['category']['name'], $page::UPDATE);
// todo 登録内容が正しく表示されているか
$page->submitModal($page::UPDATE);
$page->reload();

//----------------------
// カテゴリ更新
//----------------------
$I->amGoingTo('カテゴリ更新');
$page->openModal($page->attributes['big'][1]['job_type_big_name'], $page::UPDATE);
// todo 登録内容が正しく表示されているか
$page->submitModal($page::UPDATE);
$page->reload();


//----------------------
// 項目更新
//----------------------
$I->amGoingTo('項目更新');
$page->openModal($page->attributes['small'][1]['job_type_small_name'], $page::UPDATE);
// todo 登録内容が正しく表示されているか
$page->submitModal($page::UPDATE);
$page->reload();

//----------------------
// 項目削除
//----------------------
$I->amGoingTo('項目削除');
$page->openModal($page->attributes['small'][1]['job_type_small_name'], $page::UPDATE);
$page->delete();
$I->cantSee($page->attributes['small'][1]['job_type_small_name']);
unset($page->attributes['item'][1]);
$page->reload();

//----------------------
// カテゴリ削除
//----------------------
$I->amGoingTo('カテゴリ削除');
$page->openModal($page->attributes['big'][1]['job_type_big_name'], $page::UPDATE);
$page->delete();
$I->cantSee($page->attributes['big'][1]['job_type_big_name']);
$I->cantSee($page->attributes['small'][2]['job_type_small_name']);
unset($page->attributes['big'][1]);
unset($page->attributes['small'][2]);
$page->reload();

//----------------------
// 大カテゴリ削除
//----------------------
$I->amGoingTo('大カテゴリ削除');
$page->openModal($page->attributes['category']['name'], $page::UPDATE);
$page->delete();
$I->cantSee($page->attributes['category']['name']);
$I->cantSee($page->attributes['big'][2]['job_type_big_name']);
$I->cantSee($page->attributes['small'][3]['job_type_small_name']);
unset($page->attributes['category']);
unset($page->attributes['big'][2]);
unset($page->attributes['small'][3]);