<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\application\ApplicationRegisterPage;
use app\models\manage\ApplicationMaster;
use app\models\manage\JobMaster;
use app\models\manage\ClientCharge;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////

// メニュー情報取得
$listMenu = ManageMenuMain::findFromRoute('/manage/secure/application/list');
$updateMenu = ManageMenuMain::findFromRoute('/manage/secure/application/update');

// レコード準備
// todo 表示チェックのテストも出来るように、正常なデータをfixtureでロードすることを検討
Yii::$app->user->identity = Manager::findIdentity(1);
/** @var JobMaster $job */
$job = JobMaster::find()->one();
$clientId = Manager::findByLoginId('admin03')->client_master_id;
$job->client_master_id = $clientId;
/** @var ClientCharge $charge */
$charge = ClientCharge::find()->where(['client_master_id' => $clientId])->one();
$job->client_charge_plan_id = $charge->client_charge_plan_id;
$job->save(false);
$application = new ApplicationMaster();
$application->job_master_id = $job->id;
$application->mail_address = 'test@a.com';
$application->application_no = ApplicationMaster::find()->max('application_no') + 1;
$application->birth_date = '1980-12-31';
$application->save(false);

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// 各権限ごとの管理者情報
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

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('応募者変更・メール送信のテスト');
foreach ($admins as $admin) {
//----------------------
// 各権限でログインしてlist画面へ遷移
//----------------------
    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage->login($admin['loginid'], $admin['password']);
    $I->wait(3);
    $page = ApplicationRegisterPage::openBy($I);
    $I->wait(3);
    $I->seeInTitle($listMenu->title);

//----------------------
// list画面から変更画面へ遷移
//----------------------
    $I->amGoingTo('一覧から更新へ遷移');
    $I->click('この条件で表示する');
    $I->wait(3);
    $page->clickActionColumn(1, 1);
    $I->wait(3);
    $I->seeInTitle($updateMenu->title);
    $I->see($updateMenu->title, 'h1');

//----------------------
// 応募者情報変更
// 生年月日表記の「YYYY/MM/DD」確認
//----------------------
    $I->amGoingTo('生年月日表記が「YYYY/MM/DD」である');
    $I->click('すべて表示');
    $I->wait(3);
    $birth = $I->grabTextFrom('#closeOubodata > div:nth-child(2) > div:nth-child(2)');
    $I->seeMatches('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', $birth);

//----------------------
// 応募者情報変更
//----------------------
    $I->amGoingTo('応募者情報変更');
    $I->click('変更する');
    $I->wait(2);
    $I->see('閉じる', 'div.modal-footer');
    $I->see('変更', 'div.modal-footer');
    $value = time();
    $page->fillApplicationAndRemember('application_memo', $value);
    $I->selectOption("#applicationmaster-application_status_id", 4);
    $I->click('変更');
    $I->wait(1);
    $I->see("応募ステータスを変更してもよろしいですか？");
    $I->click('OK');
    $I->wait(3);
    $I->seeInTitle($updateMenu->title);
    $I->see($updateMenu->title, 'h1');
// todo 変更された内容・対応履歴の確認

//----------------------
// 応募者情報変更
//----------------------
    $I->amGoingTo('メール送信');
    $I->click('メールを送信する');
    $I->wait(2);
    $I->see('閉じる');
    $I->see('送信');
    $value = time();
    $admin = Manager::findByLoginId($admin['loginid']);
    $I->seeInField('#mailsend-from_mail_address', $admin->mail_address);
    $page->fillMailAndRemember('mail_title', $value);
    $page->fillMailAndRemember('mail_body', $value);
    $I->click('送信');
    $I->wait(1);
    $I->see("応募者へメールを送信してもよろしいですか？");
    $I->click('OK');
    $I->wait(3);
    $I->seeInTitle($updateMenu->title);
    $I->see($updateMenu->title, 'h1');
// todo 対応履歴の確認

// todo ボタンの開閉・応募者情報・仕事情報の確認

//----------------------
// 応募者一覧画面への遷移
//----------------------
    $I->amGoingTo('応募者一覧画面への遷移');
    $I->click('応募者一覧に戻る');
    $I->wait(3);
    $I->seeInTitle($listMenu->title);

//----------------------
// ログアウト
//----------------------
    $I->amGoingTo('ログアウト');
    //todo ManageLoginPageに下記の操作群を追加した方がいいかも
    $I->click($admin->name_sei . ' ' . $admin->name_mei);
    $I->wait(1);
    $I->click('ログアウト');
    $I->wait(3);
    $I->see('ログイン');
}
