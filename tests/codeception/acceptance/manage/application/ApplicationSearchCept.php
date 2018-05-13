<?php

use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\AdminMaster;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\application\ApplicationSearchPage;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
$adminId = AdminMaster::findOne([
    'login_id' => 'admin01'
])->id;
Yii::$app->user->identity = Manager::findIdentity($adminId);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/application/list');

// 使うmodelを準備
/** @var CorpMaster $corp */
$corp = CorpMaster::find()->one();
/** @var ClientMaster $client */
$client = ClientMaster::find()->one();
/** @var ClientChargePlan $client */
$plan = ClientChargePlan::find()->one();

// corpが有効であることを担保
$corp->valid_chk = 1;
$corp->save();

// clientが有効かつcorpの管理下であることを担保
$client->valid_chk = 1;
$client->corp_master_id = $corp->id;
$client->save();

// clientがplanを持っていることを担保
$client->unlinkAll('clientCharges', true);
$clientCharge = new ClientCharge();
$clientCharge->client_charge_plan_id = $plan->id;
$client->link('clientCharges', $clientCharge);

// テスター準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('応募者検索画面のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = ApplicationSearchPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');

//----------------------
// 仕事ID
//----------------------
$I->wantTo('仕事IDの入力ができる');
// 仕事IDの入力
$I->fillField('#applicationmastersearch-jobno', '1111');
// 入力の値が入っている
$I->seeInField('#applicationmastersearch-jobno', '1111');

//----------------------
// キーワード
// todo next 動作検証
//----------------------
$I->wantTo('キーワードの入力ができる');
// キーワードの入力
$I->fillField('#applicationmastersearch-searchtext', 'test');
// 入力の値が入っている
$I->seeInField('#applicationmastersearch-searchtext', 'test');

//----------------------
// 代理店名
// todo next 動作検証
//----------------------
$I->wantTo('代理店のselect2に値をinput');
$I->click('#applicationmastersearch-corpmasterid + span .select2-selection__arrow');
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
$I->click('#applicationmastersearch-clientmasterid + span .select2-selection__arrow');
$I->wait(1);
$I->executeJS("$('input.select2-search__field').attr('id', 'groups_input');");
$I->fillField('#groups_input', $client->client_name);
$I->wait(1);
// エンターを押す
$I->pressKey('#groups_input', WebDriverKeys::ENTER);
$I->wait(1);

//----------------------
// 料金プラン
// todo next 動作検証
//----------------------
$I->wantTo('料金プランの入力ができる');
// 料金プランの入力
$I->selectOption('#applicationmastersearch-clientchargeplanid', $plan->id);

//----------------------
// 状況
//----------------------
$I->wantTo('状況IDの入力ができる');
// 料金プランの入力
$I->selectOption('#applicationmastersearch-application_status_id', 3);

//----------------------
// 日付の入力
//----------------------
$I->wantTo('日付入力が独立していて、正しいフォーマットで入力できる');
// from入力
$I->fillField('#applicationmastersearch-searchstartdate', '2016/10/19');
// fromにはちゃんと入っており、toには自動では入っていない
$I->seeInField('#applicationmastersearch-searchstartdate', '2016/10/19');
$I->seeInField('#applicationmastersearch-searchenddate', '');
// to入力
$I->fillField('#applicationmastersearch-searchenddate', '2016/10/18');
// それぞれ入力したままの値が入っている
$I->seeInField('#applicationmastersearch-searchstartdate', '2016/10/19');
$I->seeInField('#applicationmastersearch-searchenddate', '2016/10/18');

//----------------------
// 都道府県の入力
// todo next 動作検証
//----------------------
$I->wantTo('さらに詳しい条件をあける');
$I->click('#hide_btn2');
$I->wantTo('都道府県の入力ができる');
$I->selectOption('#applicationmastersearch-pref_id',8);

//----------------------
// 生年月日の入力
//----------------------
$I->wantTo('生年月日の入力ができる');
$I->selectOption('#applicationmastersearch-birthdateyear',1994);
$I->selectOption('#applicationmastersearch-birthdatemonth',12);
$I->selectOption('#applicationmastersearch-birthdateday',31);

//----------------------
// 性別の入力
//----------------------
$I->wantTo('性別の入力ができる');
$I->selectOption('input[name=ApplicationMasterSearch\\[sex\\]]', 0);

//----------------------
// 応募原稿の状態の入力
//----------------------
$I->wantTo('応募原稿の状態の入力ができる');
$I->selectOption('input[name=ApplicationMasterSearch\\[isJobDeleted\\]]', 0);

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
// 生年月日が「Y/m/d」表記か確認
//----------------------
$I->wantTo('生年月日の表記が「Y/m/d」である');
$I->click('この条件で表示する');
$I->wait(2);
$birth = $I->grabTextFrom('td#w10.tbl-popover');
$I->seeMatches('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', $birth);

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


// todo next ポップアップテストとかチェックボックステストとか

