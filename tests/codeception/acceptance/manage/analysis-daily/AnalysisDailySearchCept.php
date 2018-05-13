<?php

use app\models\manage\AccessLog;
use app\models\manage\AdminMaster;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\analysis_daily\AnalysisDailySearchPage;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
$adminId = AdminMaster::findOne([
    'login_id' => 'admin01',
])->id;
Yii::$app->user->identity = Manager::findIdentity($adminId);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/analysis-daily/list');

// 使うmodelを準備
/** @var CorpMaster $corp */
$corp = CorpMaster::find()->one();
/** @var ClientMaster $client */
$client = ClientMaster::find()->one();
/** @var AccessLog $accessLog */
$accessLog = AccessLog::find()->one();


// corpが有効であることを担保
$corp->valid_chk = CorpMaster::VALID;
$corp->save();

// clientが有効かつcorpの管理下であることを担保
$client->valid_chk = 1;
$client->corp_master_id = $corp->id;
$client->save();

// テスター準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('日別アクセス数集計画面のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);
$page = AnalysisDailySearchPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');

//----------------------
// 都道府県
//----------------------
$I->wantTo('都道府県の入力ができる');
// 都道府県の入力ができる
$I->selectOption('#accesslogdailysearch-prefid', 1);

//----------------------
// 仕事ID
//----------------------
$I->wantTo('仕事IDの入力ができる');
// 仕事IDの入力
$I->fillField('#accesslogdailysearch-jobno', '1111');
// 入力の値が入っている
$I->seeInField('#accesslogdailysearch-jobno', '1111');

//----------------------
// 代理店名
// todo next 動作検証
//----------------------
$I->wantTo('代理店のselect2に値をinput');
$I->click('#accesslogdailysearch-corpmasterid + span .select2-selection__arrow');
$I->wait(1);
$I->executeJS("$('input.select2-search__field').attr('id', 'groups_input');");
$I->fillField('#groups_input', $corp->corp_name);
$I->wait(1);
// エンターを押す
$I->pressKey('#groups_input', WebDriverKeys::ENTER);
$I->wait(1);

//----------------------
// 掲載企業名
// todo next 動作検証
//----------------------
$I->wantTo('掲載企業のselect2に値をinput');
$I->click('#accesslogdailysearch-clientmasterid + span .select2-selection__arrow');
$I->wait(1);
$I->executeJS("$('input.select2-search__field').attr('id', 'groups_input');");
$I->fillField('#groups_input', $client->client_name);
$I->wait(1);
// エンターを押す
$I->pressKey('#groups_input', WebDriverKeys::ENTER);
$I->wait(1);

//----------------------
// アクセス月の入力
//----------------------
$I->wantTo('アクセス月の入力ができる');
$I->selectOption('input[name=AccessLogDailySearch\\[accessMonth\\]]', 2);

//----------------------
// 検索する
// todo next 動作検証
//----------------------
$I->click('この条件で表示する');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// クリアする
// todo next 動作検証
////----------------------
$I->click('クリア');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// ソートする
// todo next 動作検証
//----------------------
$I->wantTo('ソートする');
$I->click('th.sort_box.active ~ th');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// ページャーで遷移する
// todo next 動作検証
//----------------------
$I->wantTo('ページャーで遷移する');
$I->click('a[data-page="1"]');
$I->wait(2);
$I->seeInTitle($menu->title);
