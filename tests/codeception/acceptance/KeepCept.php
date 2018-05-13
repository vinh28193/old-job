<?php

/* @var $scenario Codeception\Scenario */

use app\models\JobMasterDisp;
use app\models\manage\JobMaster;

$I = new AcceptanceTester($scenario);
$I->wantTo('キープテスト');

$I->amGoingTo('キープ一覧ページを表示');
$I->amOnPage('/keep');
$I->see('キープした求人情報はありません。', 'h1');
$I->expect('キープされている表示件数は0');
$I->see('0', '.keepCountShow');

$I->amGoingTo('検索結果を表示');
$I->amOnPage('/kanto/search-result');

$id = $I->grabAttributeFrom('.btn-favorit', 'data-id');
$I->amGoingTo('取得した求人のキープをクリック IDは' . $id);
$I->click('.btn-favorit[data-id="' . $id . '"]');
$I->expect('キープボタンをクリックすると、keep-doneが付与される');

$I->waitForElement('.keep-done', 3);

$I->amGoingTo('キープ一覧ページを表示');
$I->amOnPage('/keep');
$I->dontSee('キープした求人情報はありません。', 'h1');
$I->expect('キープされている表示件数は1');
$I->see('1', '.keepCountShow');
$I->expect('キープしたIDがあるか検証する');
$I->seeElement('.btn-favorit', ['data-id' => $id]);

// 解除
$I->click('.keep-done[data-id="' . $id . '"]');
$I->comment('js 確認が表示されるが、テストできないので解除はここで終了');

// 求人NOを取得
$job_no = JobMasterDisp::find()->active()->distinct()->select([JobMaster::tableName() . '.job_no'])
    ->andWhere([JobMaster::tableName() . '.id' => $id])->scalar();

$I->amGoingTo('求人詳細ページを表示');
$I->amOnPage('/kyujin/' . $job_no . '/');
$I->expect('キープ済みか検証する');
$I->see('キープ済', '.keep-done');
$I->expect('キープされている表示件数は1');
$I->see('1', '.keepCountShow');

// 解除
$I->click('.keep-done');

$I->comment('js 確認が表示されるが、テストできないのでここで解除はここで終了');

$I->expect('クッキーを削除する');
$I->resetCookie('JMSSID');
$I->amGoingTo('求人詳細ページを表示');
$I->amOnPage('/kyujin/' . $job_no . '/');
$I->expect('キープされている表示件数は0');
$I->see('0', '.keepCountShow');
$I->click('.keepBtn');
$I->expect('キープボタンをクリックすると、keep-doneが付与される');
$I->waitForElement('.keep-done', 3);
$I->expect('キープされている表示件数は1');
$I->see('1', '.keepCountShow');
$I->click('.keep-done');

$I->comment('js 確認が表示されるが、テストできないのでここで解除はここで終了');



