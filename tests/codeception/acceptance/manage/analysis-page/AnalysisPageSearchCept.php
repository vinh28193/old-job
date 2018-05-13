<?php

use app\models\manage\AccessLog;
use app\models\manage\AdminMaster;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\analysis_page\AnalysisPageSearchPage;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
$adminId = AdminMaster::findOne([
    'login_id' => 'admin01',
])->id;
Yii::$app->user->identity = Manager::findIdentity($adminId);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/analysis-page/list');

// 使うmodelを準備
/** @var CorpMaster $corp */
$corp = CorpMaster::find()->one();
/** @var ClientMaster $client */
$client = ClientMaster::find()->one();
/** @var AccessLog $accessLog */
$accessLog = AccessLog::find()->one();

// corpが有効であることを担保
$corp->valid_chk = 1;
$corp->save();

// clientが有効かつcorpの管理下であることを担保
$client->valid_chk = 1;
$client->corp_master_id = $corp->id;
$client->save();

// テスター準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('ページ別アクセス数確認画面のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);
$page = AnalysisPageSearchPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');

//----------------------
// アクセスページ
//----------------------
$I->wantTo('アクセスページの入力ができる');
// アクセスページの入力ができる
$I->selectOption('#accesslogsearch-accesspageid', 0);

//----------------------
// 仕事ID
//----------------------
$I->wantTo('仕事IDの入力ができる');
// 仕事IDの入力
$I->fillField('#accesslogsearch-jobno', '1111');
// 入力の値が入っている
$I->seeInField('#accesslogsearch-jobno', '1111');

//----------------------
// アクセスURL
// todo next 動作検証
//----------------------
$I->wantTo('アクセスURLのselect2に値をinput');
$I->click('#accesslogsearch-access_url + span .select2-selection__arrow');
$I->wait(1);
$I->executeJS("$('input.select2-search__field').attr('id', 'groups_input');");
$I->fillField('#groups_input', $accessLog->access_url);
$I->wait(1);
// エンターを押す
$I->pressKey('#groups_input', WebDriverKeys::ENTER);
$I->wait(1);

//----------------------
// ユーザーエージェント
// todo next 動作検証
//----------------------
$I->wantTo('ユーザーエージェントのauto_completeに値をinput');
$I->fillField('#accesslogsearch-access_user_agent', 'Mozi');
$I->wait(1);

//----------------------
// リファラー
// todo next 動作検証
//----------------------
$I->wantTo('リファラーのauto_completeに値をinput');
$I->fillField('#accesslogsearch-access_referrer', '(dire');
$I->wait(1);

//----------------------
// アクセス日時の入力
//----------------------
$I->wantTo('日付入力が独立していて、正しいフォーマットで入力できる');
// from入力
$I->fillField('#accesslogsearch-searchstartdate', '2016/10/19');
// fromにはちゃんと入っており、toには自動では入っていない
$I->seeInField('#accesslogsearch-searchstartdate', '2016/10/19');
$I->seeInField('#accesslogsearch-searchenddate', '');
// to入力
$I->fillField('#accesslogsearch-searchenddate', '2016/10/18');
// それぞれ入力したままの値が入っている
$I->seeInField('#accesslogsearch-searchstartdate', '2016/10/19');
$I->seeInField('#accesslogsearch-searchenddate', '2016/10/18');

//----------------------
// アクセス機器の入力
//----------------------
$I->wantTo('アクセス機器の入力ができる');
$I->selectOption('input[name=AccessLogSearch\\[carrier_type\\]]', 0);

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
$I->click('a[data-page="2"]');
$I->wait(2);
$I->seeInTitle($menu->title);
