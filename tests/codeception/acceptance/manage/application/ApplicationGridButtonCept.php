<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\application\ApplicationSearchPage;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/application/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('応募者CSVダウンロード・削除のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->wantTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = ApplicationSearchPage::openBy($I);
$I->wait(2);

$I->wantTo('一覧を表示');
$I->click('この条件で表示する');
$I->wait(2);

$I->wantTo('一番上だけチェック');
$page->clickCheckbox(0);
$page->clickCheckbox(1);
$applicationId = $page->grabTableRowId(1);

// ファイルダウンロードは自動テストできません
$I->wantTo('CSVダウンロードしてもエラーにならない');
$I->click('CSVダウンロード');
$I->wait(5);
$I->see($menu->title, 'h1');
$I->seeInTitle($menu->title);

$I->wantTo('削除する');
$I->click('まとめて削除する');
$I->wait(1);
$I->see('削除したものは元に戻せません。削除しますか？');
$I->click('OK');
$I->wait(2);
$I->expect('正常に削除できる');
$I->see('1件のデータが削除されました。', 'pre');
$I->see($menu->title, 'h1');
$I->seeInTitle($menu->title);

// todo next 削除チェックとバックアップチェック

$I->wantTo('リロードすると文言が消える');
$I->reloadPage();
$I->cantSee('1件のデータが削除されました。', 'pre');