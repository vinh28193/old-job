<?php

use app\models\manage\JobMaster;
use app\models\manage\JobReviewStatus;
use app\models\manage\ManageMenuMain;
use app\models\manage\CorpColumnSet;
use app\modules\manage\models\Manager;
use proseeds\models\Tenant;
use tests\codeception\_pages\manage\corp\CorpRegisterPage;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\fixtures\CorpColumnSetFixture;
use tests\codeception\fixtures\CorpMasterFixture;
use app\models\manage\CorpMasterSearch;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

(new CorpMasterFixture())->initTable();
(new CorpColumnSetFixture())->initTable();

// メニュー情報取得
$createMenu = ManageMenuMain::findFromRoute('/manage/secure/corp/create');
$updateMenu = ManageMenuMain::findFromRoute('/manage/secure/corp/update');

// 審査機能ONにしておく。
Tenant::updateAll(['review_use' => 1]);

// 代理店審査中求人を用意しておく ※代理店ID：21、掲載企業ID：58に紐付けておく。
// ※本来はここの画面操作でやるべきかもしれないが、そこまでたどり着く手順が長いため割愛する
$corpMasterId = 21;
$clientMasterId = 58;
$jobNoLists = JobMaster::find()->select('job_no')->where(['client_master_id' => $clientMasterId, 'job_review_status_id' => JobReviewStatus::STEP_CORP_REVIEW])->column();
$jobNoLists = implode(', ', $jobNoLists);

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('代理店登録・変更のテスト');
//----------------------
// 運営元でログインして一覧からcreate画面へ遷移
//----------------------
$I->amGoingTo('運営元で一覧にアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = CorpRegisterPage::openBy($I);
$I->wait(1);

//----------------------
// 新規登録画面
//----------------------
$page->goCreate('代理店を登録する', $createMenu);
// todo パン屑リンク検証

// todo クリアボタンの動作検証(今回は変な遷移が起きないかだけ)
$I->amGoingTo('クリアボタン押下');
$I->click('クリア');
$I->wait(1);
$I->see($createMenu->title);

// エラー確認
// todo 各項目client side動作検証
$page->submit('登録', false, false);
$I->see('代理店審査は必須項目です。');

// 各項目入力
$I->amGoingTo('各項目入力');
$page->fillAndRemember('corp_name', 'cept代理店名' . time());
$page->fillAndRemember('tel_no', time());
$page->fillAndRemember('tanto_name', 'cept担当者名');
$page->fillAndRemember('option100', 'ceptテキスト500文字');
$page->fillAndRemember('option101', '5');
$page->fillAndRemember('option102', 'septOption102Mail@pro-seeds.co.jp');
$page->fillAndRemember('option105', 'ceptOption105テキスト200文字');
$page->fillAndRemember('option109', 'septOption109Mail@pro-seeds.co.jp');
$I->selectOption('input[name=CorpMaster\\[corp_review_flg\\]]', 1);
$I->selectOption('input[name=CorpMaster\\[valid_chk\\]]', 1);

// 登録する
$page->submit('登録');

//----------------------
// 登録検証
//----------------------
$I->amGoingTo('完了画面から一覧へ遷移');
$I->click('代理店一覧へ');
$I->wait(1);
$I->seeInTitle('代理店情報一覧');
// todo 一覧画面に登録した内容が表示されているか

//----------------------
// 変更画面
//----------------------
$I->amGoingTo('一覧から更新へ遷移');
$I->click('この条件で表示する');
$I->wait(1);
$page->clickActionColumn(1, 1);
$I->wait(1);
$I->seeInTitle($updateMenu->title);
$I->see($updateMenu->title, 'h1');
// todo 更新画面に登録した内容がinputされているか
$I->seeCheckboxIsChecked('//div[@id="corpmaster-corp_review_flg"]//input[@value="1"]');

// todo パン屑リンク検証

// todo クリアボタンの動作検証(今回は変な遷移が起きないかだけ)
$I->amGoingTo('クリアボタン押下');
$I->click('クリア');
$I->wait(1);
$I->see($updateMenu->title);

// 変更する
$page->submit('変更');

// 非表示項目のチェック
$I->wantTo('非表示項目のチェック');
$columnSet = CorpColumnSet::findOne(['column_name' => 'tel_no']);
$columnSet->valid_chk = 0;
$columnSet->save();
$columnSet = CorpColumnSet::findOne(['column_name' => 'tanto_name']);
$columnSet->valid_chk = 0;
$columnSet->save();
$page->goCreate('新しく追加登録する', $createMenu);
$I->dontSeeElement('#corpmaster-tel_no');
$I->dontSeeElement('#corpmaster-tanto_name');

//----------------------
// 特殊エラーチェック
//----------------------
$I->amGoingTo('代理店審査中原稿チェック');
$I->amOnPage($page->getUrl());
$I->click('この条件で表示する');
$I->wait(2);
$page->clickActionColumnById($corpMasterId, 1, CorpMasterSearch::PAGE_SIZE_LIMIT);
$I->wait(1);
$I->selectOption('input[name=CorpMaster\\[corp_review_flg\\]]', 0);
$page->submit('変更', false, false);
$jobNoLabel = Yii::$app->functionItemSet->job->attributeLabels['job_no'];
$I->see("代理店審査中の原稿が存在するため変更できません。対象{$jobNoLabel}：{$jobNoLists}");

//----------------------
// todo 完了画面リンク検証
//----------------------


// 審査機能OFFにする
Tenant::updateAll(['review_use' => 0]);

// 再アクセス
$I->amGoingTo('代理店審査フラグが無いことを確認する');
$I->amOnPage($page->getUrl());
$page->goCreate('代理店を登録する', $createMenu);
$I->wait(2);
$I->cantSeeElement('#corpmaster-corp_review_flg');
