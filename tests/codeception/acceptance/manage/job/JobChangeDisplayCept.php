<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\job\JobSearchPage;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */
/** @var CorpMaster $corp */
/** @var ClientMaster $client */
/** @var ClientChargePlan $plan */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// ■求人を1件登録しておくこと。
// ■admin01～03を有効な状態で登録しておくこと

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/job/list');

// 管理者情報
$admins = [
    [
        'type' => '掲載企業',
        'loginid' => 'admin03',
        'password' => 'admin03',
        'admin_id' => 3,
        'role' => Manager::CLIENT_ADMIN,
    ],
    [
        'type' => '代理店',
        'loginid' => 'admin02',
        'password' => 'admin02',
        'admin_id' => 2,
        'role' => Manager::CORP_ADMIN,
    ],
    [
        'type' => '運営元',
        'loginid' => 'admin01',
        'password' => 'admin01',
        'has_setting_menu' => true,
        'admin_id' => 1,
        'role' => Manager::OWNER_ADMIN,
    ],
];

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('公開／非公開の切り替えのテスト');

//----------------------
// 公開／非公開の切り替えテスト
//----------------------
foreach ($admins as $admin) {
    Yii::$app->user->identity = Manager::find()->where(['admin_no' => $admin['admin_id']])->one();
    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage = ManageLoginPage::openBy($I);
    $loginPage->login($admin['loginid'], $admin['password']);
    $I->wait(3);
    $page = JobSearchPage::openBy($I);
    $I->seeInTitle($menu->title);
    $I->see($menu->title, 'h1');

    $page->checkChangeDisplay(1);

    $I->amGoingTo('ログアウト');
    $I->click('ホーム');
    $I->wait(2);
    $loginPage->logoutOnHome();
}
