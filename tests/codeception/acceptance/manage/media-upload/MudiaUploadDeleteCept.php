<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\media_upload\MediaUploadPage;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/media-upload/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('ギャラリー削除のテスト');
//----------------------
// 運営元でログインしてlistへ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = MediaUploadPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');
// todo パン屑リンク検証

//----------------------
// 作成者の入力
// todo 入力・動作検証
//----------------------
$I->fillField('#mediauploadsearch-adminmastername', '管理者');

//----------------------
// ファイル名の入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('ファイル名をinput');
$I->fillField('#mediauploadsearch-disp_file_name', 'jpg');

//----------------------
// 種別の入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('種別をinput');
$I->selectOption('input[name=MediaUploadSearch\\[role\\]]', 'owner_admin');

//----------------------
// 検索する
// todo 動作検証
//----------------------
$I->amGoingTo('検索する');
$I->click('この条件で表示する');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// 画像を選択
// todo 動作検証
//----------------------
$I->amGoingTo('一番上だけチェック');
$page->clickCheckbox(0);
$page->clickCheckbox(1);
$Id = $page->grabTableRowId(1);

//----------------------
// 削除する
// todo 動作検証
//----------------------
$I->amGoingTo('削除する');
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

//----------------------
// リロード
// todo 動作検証
//----------------------
$I->amGoingTo('リロードすると文言が消える');
$I->reloadPage();
$I->cantSee('1件のデータが削除されました。', 'pre');