<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use proseeds\models\Tenant;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\corp\CorpSearchPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/corp/list');

// 審査機能ONにしておく。
Tenant::updateAll(['review_use' => 1]);

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('代理店検索画面のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = CorpSearchPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');

//----------------------
// キーワードの入力
// todo 入力・動作検証
//----------------------
$I->fillField('#corpmastersearch-searchtext', '株式会社');

//----------------------
// 代理店審査
// todo 入力・動作検証
//----------------------
$I->amGoingTo('審査ステータスをinput');
$I->selectOption('input[name=CorpMasterSearch\\[corp_review_flg\\]]', 1);

//----------------------
// 取引状態の入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('取引状態をinput');
$I->selectOption('input[name=CorpMasterSearch\\[valid_chk\\]]', 1);

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
////----------------------
$I->amGoingTo('クリアする');
$I->click('クリア');
$I->wait(2);
$I->seeInTitle($menu->title);
// 最後から3番目のth
$I->see('代理店審査', '//div[@id="grid_id"]//table/thead/tr[1]/th[position()=last()-2]');

//----------------------
// ソートする
// todo 動作検証
//----------------------
$I->amGoingTo('ソートする');
$I->click('th.sort_box.active ~ th');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// ページャーで遷移する
// todo 動作検証
//----------------------
$I->amGoingTo('ページャーで遷移する');
$I->click('a[data-page="2"]');
$I->wait(2);
$I->seeInTitle($menu->title);

// 審査機能OFFにする
Tenant::updateAll(['review_use' => 0]);

// 再アクセス
$I->amGoingTo('代理店審査検索条件が表示されないことを確認する');
$I->amOnPage($page->getUrl());
$I->wait(1);
$I->cantSee('input[name=CorpMasterSearch\\[corp_review_flg\\]]');

$I->amGoingTo('代理店審査項目がないことを確認する');
$I->click('この条件で表示する');
$I->wait(2);
$I->cantSee('代理店審査', '//div[@id="grid_id"]//table/thead/tr[1]/th[position()=last()-2]');
