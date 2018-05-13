<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\job\JobSearchPage;
use tests\codeception\_pages\manage\ManageLoginPage;
use proseeds\models\Tenant;

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

// 審査機能ONにしておく。
Tenant::updateAll(['review_use' => 1]);

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('審査ステータスの入力・検索テスト');

//----------------------
// 審査ステータスの入力・検索テスト
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

    $page->checkReviewStatus();

    $I->amGoingTo('ログアウト');
    $I->click('ホーム');
    $I->wait(2);
    $loginPage->logoutOnHome();
}

//----------------------
// 審査機能OFF時のテスト
//----------------------
$I->wantTo('審査機能OFF時のテスト');
Tenant::updateAll(['review_use' => 0]);

//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = JobSearchPage::openBy($I);
$I->wait(1);

$I->amGoingTo('審査ステータス検索条件が表示されないことを確認する');
$I->wait(1);
$I->cantSee('#jobmastersearch-job_review_status_id');

$I->amGoingTo('審査ステータス項目がないことを確認する');
$I->click('この条件で表示する');
$I->wait(2);
$I->cantSee('審査ステータス', '//div[@id="grid_id"]//table/thead/tr[1]/th[position()=last()-2]');
$I->seeElement('//div[@id="grid_id"]//table/thead/tr[1]/th[position()=last()-1]/a[@id="valid-check-hint"]/span');

