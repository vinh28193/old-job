<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/client/list');

// テスター準備
$I = new AcceptanceTester($scenario);

// 確認文言
$errorMessages = [
    '404エラー',
    '404 File not found',
    '残念ですが、お探しのページは見つかりませんでした',
];
$sideCommonMenu = [
    'jm2_tenant2',
    'ホーム',
    'メニューを閉じる',
];
$sidePermissionMenu = [
    '求人原稿',
    '代理店',
    '掲載企業',
    'ウィジェット',
    'ギャラリー',
    '管理者',
    '応募者',
    'アクセス解析',
    'サイト設定',
];

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('管理画面の404遷移テスト');

//----------------------
// 管理画面ログイン前に404画面へ遷移
//----------------------
$I->amGoingTo('ログイン前に管理画面の存在しないページにアクセス');
$I->amOnPage('/manage/secure/XXXX/XXXX');
$I->wait(3);
$I->expect('404ページチェック');
foreach ($errorMessages as $message) {
    $I->see($message);
}
$I->expect('共通メニューの存在チェック');
foreach ($sideCommonMenu as $menu) {
    $I->see($menu);
}
$I->expect('権限制限メニューが存在しないことのチェック');
foreach ($sidePermissionMenu as $menu) {
    $I->cantSee($menu);
}
//----------------------
// 運営元でログインして404画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage = ManageLoginPage::openBy($I);
$loginPage->login('admin01', 'admin01');
$I->wait(3);


$I->amGoingTo('ログイン後に管理画面の存在しないページにアクセス');
$I->amOnPage('/manage/secure/XXXX/XXXX');
$I->wait(3);
$I->expect('404ページチェック');
foreach ($errorMessages as $message) {
    $I->see($message);
}
$I->expect('共通メニューの存在チェック');
foreach ($sideCommonMenu as $menu) {
    $I->see($menu);
}
$I->expect('権限制限メニューが存在することのチェック');
foreach ($sidePermissionMenu as $menu) {
    $I->see($menu);
}
