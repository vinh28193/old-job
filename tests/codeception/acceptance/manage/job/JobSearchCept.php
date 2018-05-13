<?php

use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\job\JobSearchPage;
use tests\codeception\_pages\manage\ManageLoginPage;
use proseeds\models\Tenant;

/* @var $scenario Codeception\Scenario */
/** @var CorpMaster $corp */
/** @var ClientMaster $client */
/** @var ClientChargePlan $plan */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/job/list');

// 使うmodelを準備
$corp = CorpMaster::find()->one();
$client = ClientMaster::find()->one();
$plan = ClientChargePlan::find()->one();

// corpが有効であることを担保
$corp->valid_chk = 1;
$corp->save();

// clientが有効かつcorpの管理下であることを担保
// ログインしてからじゃないとClientMaster.php:224でエラーになるのでfalseで保存
$client->valid_chk = 1;
$client->corp_master_id = $corp->id;
$client->save(false);

// clientがplanを持っていることを担保
$client->unlinkAll('clientCharges', true);
$clientCharge = new ClientCharge();
$clientCharge->client_charge_plan_id = $plan->id;
$client->link('clientCharges', $clientCharge);

// 審査機能ONにしておく。
Tenant::updateAll(['review_use' => 1]);

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('求人原稿検索画面のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = JobSearchPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');
// todo パン屑リンク検証

//----------------------
// キーワードの入力
// todo 入力・動作検証
//----------------------
$I->fillField('#jobmastersearch-searchtext', 'test');

//----------------------
// 代理店の入力
// todo 入力・動作検証
//----------------------
// 代理店のselect2に値をinput
$I->amGoingTo('代理店のselect2に値をinput');
$I->click('#jobmastersearch-corpmasterid + span .select2-selection__arrow');
$I->wait(1);
$I->executeJS("$('input.select2-search__field').attr('id', 'groups_input');");
$I->fillField('#groups_input', $corp->corp_name);
$I->wait(1);
// エンターを押す
$I->pressKey('#groups_input', WebDriverKeys::ENTER);
// もしくは選択肢をクリック
//$I->click('li.select2-results__option');
$I->wait(1);

//----------------------
// 掲載企業の入力
// todo 入力・動作検証
//----------------------
// 掲載企業のselect2に値をinput
$I->amGoingTo('掲載企業のselect2に値をinput');
$I->click('#jobmastersearch-client_master_id + span .select2-selection__arrow');
$I->wait(1);
$I->executeJS("$('input.select2-search__field').attr('id', 'groups_input');");
$I->fillField('#groups_input', $client->client_name);
$I->wait(1);
// エンターを押す
$I->pressKey('#groups_input', WebDriverKeys::ENTER);
// もしくは選択肢をクリック
//$I->click('li.select2-results__option');
$I->wait(1);

//----------------------
// 料金プランの入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('planをinput');
$I->selectOption('#jobmastersearch-client_charge_plan_id', $plan->id);

//----------------------
// 掲載状況の入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('掲載状況をinput');
$I->selectOption('#jobmastersearch-isdisplay', 1);

//----------------------
// 日付の入力
//----------------------
$I->amGoingTo('日付入力が独立していて、正しいフォーマットで入力できる');
// from入力
$I->fillField('#jobmastersearch-startfrom', '2016/10/19');
$I->fillField('#jobmastersearch-endfrom', '2016/10/21');
// fromにはちゃんと入っており、toには自動では入っていない
$I->seeInField('#jobmastersearch-startfrom', '2016/10/19');
$I->seeInField('#jobmastersearch-startto', '');
$I->seeInField('#jobmastersearch-endfrom', '2016/10/21');
$I->seeInField('#jobmastersearch-endto', '');
// to入力
$I->fillField('#jobmastersearch-startto', '2016/10/18');
$I->fillField('#jobmastersearch-endto', '2016/10/20');
// それぞれ入力したままの値が入っている
$I->seeInField('#jobmastersearch-startfrom', '2016/10/19');
$I->seeInField('#jobmastersearch-startto', '2016/10/18');
$I->seeInField('#jobmastersearch-endfrom', '2016/10/21');
$I->seeInField('#jobmastersearch-endto', '2016/10/20');

//----------------------
// 状態の入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('状態をinput');
$I->selectOption('input[name=JobMasterSearch\\[valid_chk\\]]', 1);

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
$I->see('審査ステータス', '//div[@id="grid_id"]//table/thead/tr[1]/th[position()=last()-2]');
$I->seeElement('//div[@id="grid_id"]//table/thead/tr[1]/th[position()=last()-1]/a[@id="valid-check-hint"]/span');

//----------------------
// 状態ヒント確認
//----------------------
$I->click('//div[@id="grid_id"]//table/thead/tr[1]/th[position()=last()-1]/a[@id="valid-check-hint"]/span');
$I->wait(1);
$I->see('運営元の審査無しで「公開」「非公開」を変更することができます。', '//div[@id="grid_id"]//table/thead/tr[1]/th[position()=last()-1]/a[@id="valid-check-hint"]/div/div[2]');

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
$I->click('a[data-page="1"]');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// プレビューに遷移する
// todo 動作・内容検証
// todo _targetというwindow.nameだとSelenium上で新窓が開かないため実装コード修正してから実装
//----------------------
$I->amGoingTo('ページャーで遷移する');
$jobId = $page->grabTableRowId(1);
$I->click("#preview-$jobId");
//$I->switchToWindow('_target'); // window.nameがある場合はこれでswitchできる

//$I->executeInSelenium(function (RemoteWebDriver $webDriver) {
//    $handles = $webDriver->getWindowHandles();
//    var_dump($handles);exit;
//    $last_window = end($handles);
//    $webDriver->switchTo()->window($last_window);
//}); // window.nameが無い場合はこうしないといけない

//$I->wait(2);
//$I->see('募集要項');

// todo ポップアップテストとかチェックボックステストとか
