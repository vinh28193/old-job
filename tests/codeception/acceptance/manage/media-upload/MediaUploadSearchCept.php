<?php

use app\models\manage\ManageMenuMain;
use app\models\manage\MediaUpload;
use app\models\manage\MediaUploadSearch;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\media_upload\MediaUploadPage;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// ログインする管理者の情報取得
$ownerAdmin = Manager::findOne(['login_id' => 'admin01']);
$clientAdmin = Manager::findOne(['login_id' => 'admin03']);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/media-upload/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// 設定のため運営元権限を代入
Yii::$app->user->identity = $ownerAdmin;

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('ギャラリー検索のテスト');
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
// 合計容量の表示
//----------------------
$size = Yii::$app->formatter->asShortSize(MediaUploadSearch::getTotalFileSize());
$I->see("画像の合計サイズは{$size}です", 'p');

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
// タグの入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('タグをinput');
$tags = MediaUploadSearch::tagDropDownSelections();
$i = 1;
foreach ($tags as $value => $tag) {
    $I->seeElementInDOM("//select[@id='mediauploadsearch-tag']/option[{$i}][@value='{$value}' and text()='{$tag}']");
    $i++;
}
$I->selectOption('#mediauploadsearch-tag', 'タグ無し');

//----------------------
// 検索する
// todo 動作検証
//----------------------
$I->amGoingTo('検索する');
$I->click('この条件で表示する');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// クリアする
// todo 動作検証
//----------------------
$I->amGoingTo('クリアする');
$I->click('クリア');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// ソートする
// todo 動作検証
//----------------------
$I->amGoingTo('ソートする');
$I->selectOption('//*[@id="mediauploadsearch-role"]', 'client_admin');
$I->click('この条件で表示する');
$I->wait(3);
$I->click('//table/thead/tr/th[3]');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// 一覧表示の検証
//----------------------
$id = $page->grabTableRowId(1);
$page->checkGridValues(MediaUpload::findOne($id), 1);

//----------------------
// 掲載企業でログインしてlistへ遷移
//----------------------
// 設定のため掲載企業権限を代入
Yii::$app->user->identity = $clientAdmin;
$I->amGoingTo('掲載企業でアクセス');
$I->amOnPage('/manage/logout');
$I->wait(1);
$loginPage->login('admin03', 'admin03');
$I->wait(1);
$page = MediaUploadPage::openBy($I);

//----------------------
// 合計容量の表示
//----------------------
$size = Yii::$app->formatter->asShortSize(MediaUploadSearch::getTotalFileSize());
$I->see("貴社の画像の合計サイズは{$size}です", 'p');

//----------------------
// タグの内容検証
//----------------------
$I->amGoingTo('タグの内容検証');
$tags = MediaUploadSearch::tagDropDownSelections();
$i = 1;
foreach ($tags as $value => $tag) {
    $I->seeElementInDOM("//select[@id='mediauploadsearch-tag']/option[{$i}][@value='{$value}' and text()='{$tag}']");
    $i++;
}
$I->selectOption('#mediauploadsearch-tag', 'タグ無し');
