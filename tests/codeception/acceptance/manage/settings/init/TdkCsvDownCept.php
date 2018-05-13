<?php
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\init\TdkPage;
/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);
// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/settings/tool-master/index');
// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);
// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////

$I->wantTo('TDK管理表示確認のテスト');

//----------------------
// 運営元でログインしてindexへ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(2);
$page = TdkPage::openBy($I);
$I->wait(2);
$page->go($menu);

//----------------------
// CSVテンプレートのダウンロード
//----------------------
// ファイルダウンロードは自動テストできません
$I->amGoingTo('CSVダウンロードしてもエラーにならない');
$I->click('CSVダウンロード');
$I->wait(3);

//----------------------
// CSV入力方法
//----------------------
$I->amGoingTo('CSV入力方法');
$I->click('CSVの入力方法');
$I->switchToWindow('help');
$I->wait(3);
$I->seeInTitle('TDK管理CSVの入力方法');
