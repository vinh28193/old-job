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
$I->wantTo('TDK管理変更のテスト');
//----------------------
// 運営元でログインしてindex画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = TdkPage::openBy($I);
$I->wait(1);
$page->go($menu);

// PhantomJs上でpushStateが誤動作するので一旦こうします
// todo 実装コードにも謎のブラウザバックループ現象が起きているのでそこと合わせて問題解決
$I->attachFile('#csv-uploader', 'tdk.csv');
$I->wait(1.2);
$I->see('アップロードされたCSVをチェックしています');
$url = $I->grabFromCurrentUrl();
$fileName = str_replace('/manage/secure/settings/tool-master/verify?filename=', '', $url);
//$I->see('以下の内容で登録してもよろしいですか？', 'p');
