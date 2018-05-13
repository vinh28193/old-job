<?php
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);
// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/admin/profile');

$admins = [
    [
        'type' => '運営元',
        'loginid' => 'admin01',
        'password' => 'admin01',
    ],
    [
        'type' => '代理店',
        'loginid' => 'admin02',
        'password' => 'admin02',
    ],
    [
        'type' => '掲載企業',
        'loginid' => 'admin03',
        'password' => 'admin03',
    ],
];

$I = new AcceptanceTester($scenario);
$I->wantTo('マイプロフィール変更のテスト');

foreach ($admins as $admin) {

    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage = ManageLoginPage::openBy($I);
    $loginPage->login($admin['loginid'], $admin['password']);
    $I->wait(3);

    $I->amGoingTo('マイプロフィール編集ページへ移動');
    $I->click('//*[@id="w3"]/li/a[@href="#"]');
    $I->wait(3);
    $I->click('//*[@id="w4"]/li/a[@href="/manage/secure/admin/profile"]');
    $I->wait(3);
    $I->seeInTitle($menu->title);
    $I->see($menu->title, 'h1');

    $I->amGoingTo('マイプロフィールを編集');
    $I->fillField('//*[@id="adminmaster-name_sei"]', $admin['type']);
    $I->fillField('//*[@id="adminmaster-name_mei"]', $admin['type'] . '管理者');
    $I->fillField('//*[@id="adminmaster-login_id"]', $admin['loginid']);
    $I->fillField('//*[@id="adminmaster-password"]', $admin['password']);
    $I->fillField('//*[@id="adminmaster-mail_address"]', $admin['loginid'] . '@example.com');
    $I->fillField('//*[@id="adminmaster-option100"]', 'オプション1');
    $I->click('complete');
    $I->wait(3);
    $I->click('//div[@class="modal-footer"]/button[@class="btn btn-primary"]');
    $I->wait(3);
    $I->see('変更完了', 'h1');

    $I->amGoingTo('ログアウト');
    $loginPage->logout();
    $I->dontSeeElement('h1');
    $I->seeInField('//*[@id="loginid"]', '');
    $I->seeInField('//*[@id="password"]', '');
}
