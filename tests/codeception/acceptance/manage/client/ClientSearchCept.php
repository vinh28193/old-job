<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\client\ClientSearchPage;
use app\models\manage\ClientChargePlan;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/client/list');

// 使うmodelを準備
$type = 1;  // 課金タイプを「掲載課金」に指定しておく
/** @var ClientChargePlan $plan */
$plan = ClientChargePlan::find()->where(['valid_chk' => 1, 'client_charge_type' => $type])->one();

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('掲載企業検索のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = ClientSearchPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');
// todo パン屑リンク検証

//----------------------
// キーワードの入力
// todo 入力・動作検証
//----------------------
$I->fillField('#clientmastersearch-searchtext', 'テスト');

//----------------------
// 課金タイプの入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('課金タイプをinput');
$I->selectOption('#clientmastersearch-clientchargetype', $type);
$I->wait(1);

//----------------------
// 料金プランの入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('planをinput');
$I->selectOption('#clientmastersearch-clientchargeplanid', $plan->id);

//----------------------
// 取引状態の入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('取引状態をinput');
$I->selectOption('input[name=ClientMasterSearch\\[valid_chk\\]]', 1);

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