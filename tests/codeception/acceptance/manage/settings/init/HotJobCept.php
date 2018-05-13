<?php
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use \tests\codeception\_pages\manage\settings\init\HotJob;

/* @var $scenario Codeception\Scenario */
/* @var $this yii\codeception\TestCase */

CONST URL = '/manage/secure/settings/hot-job/update';

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute(URL);

// テスター準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

$admins = [
    [
        'type' => '代理店',
        'loginId' => 'admin02',
        'password' => 'admin02',
        'resultSee' => 'アクセス権限がありません',
    ],
    [
        'type' => '掲載企業',
        'loginId' => 'admin03',
        'password' => 'admin03',
        'resultSee' => 'アクセス権限がありません',
    ],
    [
        'type' => '運営元',
        'loginId' => 'admin01',
        'password' => 'admin01',
        'resultSee' => "{$menu->title}",
    ],
];

//----------------------
// アクセス権限の確認
//----------------------
foreach((array) $admins as $admin) {
    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage->login($admin['loginId'], $admin['password']);
    $I->wait(3);
    $I->see('ホーム', 'h1');

    $I->amGoingTo('注目情報設定画面へ移動');
    $I->amOnPage(URL);
    $I->wait(1);
    $I->see($admin['resultSee'], 'h1');
    $I->amOnPage('manage/logout');
    $I->wait(3);
}

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('注目情報設定が表示確認（200 OK取得)');

//----------------------
// 描画が確認のみでOK
// 運営元でログインして注目情報設定ページへ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);

$I->amGoingTo('注目情報設定ページへ移動');
$page = HotJob::openBy($I);
$page->go($menu);
$I->wait(3);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');

$I->amGoingTo('確認・完了ページへ移動');
$I->click('変更する');
$I->wait(1);
$I->click('OK');
$I->wait(5);
$I->see('更新完了');
$I->click('注目情報の設定へ戻る');
$I->wait(1);
$I->see($menu->title);
