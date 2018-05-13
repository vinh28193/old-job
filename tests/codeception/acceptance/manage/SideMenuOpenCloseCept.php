<?php
use app\modules\manage\models\Manager;

use app\models\manage\AccessCount;
use app\models\manage\ApplicationMaster;
use app\models\manage\ManageMenuMain;
use app\models\manage\SearchkeyMaster;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\init\CustomFieldPage;
use tests\codeception\_pages\manage\settings\init\SearchkeyPage;
use tests\codeception\_pages\manage\settings\option\OptionPage;
use tests\codeception\_pages\manage\settings\searchkey\AreaPage;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
Yii::$app->user->identity = Manager::findIdentity(1);
$sideMenuXpath = '//a[@id="menu"]';

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('サイドメニュー開閉テスト');
//----------------------
// 運営元でログインして項目設定画面へ遷移
//----------------------
$I->amGoingTo('運営元でサイト設定画面へアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);

//----------------------
// メニューの開閉チェック option-form.php
//----------------------
// メニュー情報取得
$I->amGoingTo('PjaxModalのoption-form.phpのテスト');
$menu = ManageMenuMain::findFromRoute('/manage/secure/option-job/list');
$page = OptionPage::openBy($I);
$page->go($menu);

// メニュー開閉1回目（モーダル0回表示）
$I->click($sideMenuXpath);
$I->wait(1);
$I->seeElement('body', ['class' => 'drawer drawer-left drawer-responsive min-menu']);
$I->click($sideMenuXpath);
$I->wait(1);
$I->seeElement('body', ['class' => 'drawer drawer-left drawer-responsive']);

for ($i = 0; $i < 2; $i++) {
    // メニュー開閉$i回目（モーダル$i回表示）
    $page->openModal(1);
    $page->closeModal();

    $I->click($sideMenuXpath);
    $I->wait(1);
    $I->seeElement('body', ['class' => 'drawer drawer-left drawer-responsive min-menu']);
    $I->click($sideMenuXpath);
    $I->wait(1);
    $I->seeElement('body', ['class' => 'drawer drawer-left drawer-responsive']);
}


//----------------------
// メニューの開閉チェック searchkey-form.php
//----------------------
$I->amGoingTo('PjaxModalのsearchkey-form.phpのテスト');
$searchKey = SearchkeyMaster::findOne(['table_name' => 'area']);
$page = AreaPage::openBy($I);
$page->go($searchKey);
for ($i = 0; $i < 2; $i++) {
    // メニュー開閉$i回目（モーダル$i回表示）
    $page->openModal('関東', AreaPage::UPDATE2);
    $page->closeModal();

    $I->click($sideMenuXpath);
    $I->wait(1);
    $I->seeElement('body', ['class' => 'drawer drawer-left drawer-responsive min-menu']);
    $I->click($sideMenuXpath);
    $I->wait(1);
    $I->seeElement('body', ['class' => 'drawer drawer-left drawer-responsive']);
}


//----------------------
// メニューの開閉チェック searchkey-form.php
//----------------------
$I->amGoingTo('PjaxModalのitem-update.phpのテスト');
$menu = ManageMenuMain::findFromRoute('/manage/secure/settings/searchkey/list');
$page = SearchkeyPage::openBy($I);
$page->go($menu);
for ($i = 0; $i < 2; $i++) {
    // メニュー開閉$i回目（モーダル$i回表示）
    $page->openModal(1);
    $page->closeModal();

    $I->click($sideMenuXpath);
    $I->wait(1);
    $I->seeElement('body', ['class' => 'drawer drawer-left drawer-responsive min-menu']);
    $I->click($sideMenuXpath);
    $I->wait(1);
    $I->seeElement('body', ['class' => 'drawer drawer-left drawer-responsive']);
}


//----------------------
// メニューの開閉チェック input-fields.php
//----------------------
$I->amGoingTo('PjaxModalのinput-fields.phpのテスト');
$menu = ManageMenuMain::findFromRoute('/manage/secure/settings/custom-field/list');
$page = CustomFieldPage::openBy($I);
$page->go($menu);
$I->click('この条件で表示する');
$I->wait(2);
for ($i = 0; $i < 2; $i++) {
    // メニュー開閉$i回目（モーダル$i回表示）
    $page->openModal(1);
    $page->closeModal();

    $I->click($sideMenuXpath);
    $I->wait(1);
    $I->seeElement('body', ['class' => 'drawer drawer-left drawer-responsive min-menu']);
    $I->click($sideMenuXpath);
    $I->wait(1);
    $I->seeElement('body', ['class' => 'drawer drawer-left drawer-responsive']);
}
