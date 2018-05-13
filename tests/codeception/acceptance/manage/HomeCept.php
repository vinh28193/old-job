<?php
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use app\models\manage\AccessCount;
use app\models\manage\ApplicationMaster;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////

$admins = [
    [
        'type' => '運営元',
        'loginid' => 'admin01',
        'password' => 'admin01',
        'has_setting_menu' => true,
        'admin_id' => 1,
    ],
    [
        'type' => '代理店',
        'loginid' => 'admin02',
        'password' => 'admin02',
        'admin_id' => 2,
    ],
    [
        'type' => '掲載企業',
        'loginid' => 'admin03',
        'password' => 'admin03',
        'admin_id' => 3,
    ],
];

$I = new AcceptanceTester($scenario);
$I->wantTo('ホーム表示確認のテスト');

foreach ($admins as $admin) {
    // 各権限のデータでテストを行う
    Yii::$app->user->identity = Manager::findIdentity($admin['admin_id']);
    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage = ManageLoginPage::openBy($I);
    $loginPage->login($admin['loginid'], $admin['password']);
    $I->wait(10);
    $I->seeInTitle('ホーム');
    $I->see('ホーム', 'h1');
    if (empty($admin['has_setting_menu'])) {
        $I->dontSee('サイト設定');
    } else {
        $I->see('サイト設定');
    }

//----------------------
// 管理画面「応募者」クリックで応募者情報確認画面へ遷移する
//----------------------
    $I->amGoingTo('応募者画面');
    $I->click('.dashboad_box.dashboad_box02');
    $I->wait(5);
    $I->seeInTitle('応募者情報確認');
    $I->wait(5);
    $I->amGoingTo('ホーム画面');
    $I->click('ホーム');
    $I->see('ホーム', 'h1');

//----------------------
// 管理画面「ホーム」表示内容確認（応募者数）
//----------------------
    $applicationMaster = new ApplicationMaster();
    $accessCount = new AccessCount();

    $I->see($applicationMaster->totalCount, '//div[@class=\'dashboad_fig\']/p');

//----------------------
// 管理画面「ホーム」表示内容確認（アクセス数）
//----------------------
    $I->see('応募数', '//table[@class=\'table table-bordered\']/thead/tr[1]/th[2]');
    $I->see('求人情報閲覧数', '//table[@class=\'table table-bordered\']/thead/tr[1]/th[3]');
    
    $I->see('PC', '//table[@class=\'table table-bordered\']/thead/tr[2]/th[1]');
    $I->see('スマホ', '//table[@class=\'table table-bordered\']/thead/tr[2]/th[2]');
    $I->see('PC', '//table[@class=\'table table-bordered\']/thead/tr[2]/th[3]');
    $I->see('スマホ', '//table[@class=\'table table-bordered\']/thead/tr[2]/th[4]');
    
    $I->see('本日', '//table[@class=\'table table-bordered\']/tbody/tr[1]/td[1]');
    $I->see($applicationMaster->todayPcCount, '//table[@class=\'table table-bordered\']/tbody/tr[1]/td[2]');
    $I->see($applicationMaster->todaySmartPhoneCount, '//table[@class=\'table table-bordered\']/tbody/tr[1]/td[3]');
    $I->see($accessCount->todayPcCount, '//table[@class=\'table table-bordered\']/tbody/tr[1]/td[4]');
    $I->see($accessCount->todaySpCount, '//table[@class=\'table table-bordered\']/tbody/tr[1]/td[5]');
    
    $I->see('昨日', '//table[@class=\'table table-bordered\']/tbody/tr[2]/td[1]');
    $I->see($applicationMaster->yesterdayPcCount, '//table[@class=\'table table-bordered\']/tbody/tr[2]/td[2]');
    $I->see($applicationMaster->yesterdaySmartPhoneCount, '//table[@class=\'table table-bordered\']/tbody/tr[2]/td[3]');
    $I->see($accessCount->yesterdayPcCount, '//table[@class=\'table table-bordered\']/tbody/tr[2]/td[4]');
    $I->see($accessCount->yesterdaySpCount, '//table[@class=\'table table-bordered\']/tbody/tr[2]/td[5]');

//----------------------
// 管理画面「求職者画面」選択で新規タブが開く
// テスト条件：「ワンエリアではないこと」
//----------------------
    $I->amGoingTo('求職者画面');
    $I->click('求職者画面');
    $I->wait(2);
    $loginPage->switchNewTab();
    $I->seeInTitle('ProseedsJMTenant');
    $I->see('勤務地で探す');
    $loginPage->switchOriginTab();

    $I->amGoingTo('ログアウト');
    $loginPage->logoutOnHome();
    $I->dontSeeElement('h1');
    $I->seeInField('//*[@id="loginid"]', '');
    $I->seeInField('//*[@id="password"]', '');
}
