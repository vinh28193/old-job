<?php

use app\models\FreeContent;
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\free_content\FreeContentSearchPage;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */

/**
 * 公開状態のレコードが5件以上
 * 非公開状態のレコードが5件以上
 * 全部でレコードが21件以上必要です
 * todo fixtureでレコード依存の解消
 */
// テスト準備(レコード準備等) ////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/free-content/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('フリーコンテンツ検索のテスト');

//----------------------
// 掲載企業と代理店でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('代理店でアクセス');
$loginPage->login('admin02', 'admin02');
$I->wait(2);
FreeContentSearchPage::openBy($I);
$I->wait(1);
$I->see('アクセス権限がありません', 'h1');
$loginPage->logout();

$I->amGoingTo('掲載企業でアクセス');
$loginPage->login('admin03', 'admin03');
$I->wait(2);
FreeContentSearchPage::openBy($I);
$I->wait(1);
$I->see('アクセス権限がありません', 'h1');
$loginPage->logout();

//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = FreeContentSearchPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->name);
$I->see($menu->name, 'h1');

//----------------------
// 公開状況で検索
//----------------------
$I->amGoingTo('公開で検索する');
$I->checkOption('公開');
$I->click('この条件で表示する');
$I->wait(2);
$I->seeInTitle($menu->name);

$I->expect('検索できている（公開のレコードが5件以上必要）');
foreach (range(1, 5) as $row) {
    $page->seeInGrid($row, 6, '公開');
}

$I->amGoingTo('非公開で検索する');
$I->checkOption('非公開');
$I->amGoingTo('検索する');
$I->click('この条件で表示する');
$I->wait(2);
$I->seeInTitle($menu->name);

$I->expect('検索できている（非公開のレコードが5件以上必要）');
foreach (range(1, 5) as $row) {
    $page->seeInGrid($row, 6, '非公開');
}

//----------------------
// クリアする
////----------------------
$I->amGoingTo('クリアする');
$I->click('クリア');
$I->wait(2);
$I->seeInTitle($menu->name);
$I->cantSeeCheckboxIsChecked('公開');
$I->cantSeeCheckboxIsChecked('非公開');

//----------------------
// ソートする
// エラー等にならないことのみ検証
//----------------------
$I->amGoingTo('ソートする');
foreach (range(2, 5) as $column) {
    $page->clickTh($column);
    $I->wait(2);
    $I->seeInTitle($menu->name);
    $page->clickTh($column);
    $I->wait(2);
    $I->seeInTitle($menu->name);
}

//----------------------
// ページャーで遷移する
// エラー等にならないことのみ検証
//----------------------
$I->amGoingTo('ページャーで遷移する（レコード数が21件以上必要）');
$I->click('a[data-page="1"]');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// レコードを消す
//----------------------
$I->click('クリア');
$I->amGoingTo('レコード削除');
$I->wait(2);
$page->clickCheckbox(0);

$count = FreeContent::find()->count();

$deleteRows = [2, 4];
$deleteIds = [];

foreach ($deleteRows as $row) {
    $page->clickCheckbox($row);
    $deleteIds[] = $page->grabTableRowId($row);
}

$I->click('まとめて削除する');
$I->wait(1);
$I->see('削除したものは元に戻せません。削除しますか？');
$I->click('OK');
$I->wait(2);

$I->expect('削除メッセージが出ている');
$I->see(count($deleteRows) . '件のデータが削除されました。');

$I->expect('削除した後の件数が想定通り');
$I->see($count - count($deleteRows), '.pagination_Num');

$I->expect('削除したレコードがDBに無い');
foreach ($deleteIds as $id) {
    $I->cantSeeInDatabase('free_content', ['id' => $id]);
}
