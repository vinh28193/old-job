<?php

use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\JobColumnSet;
use app\models\manage\JobMaster;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\job\JobDateInput;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */
/** @var CorpMaster $corp */
/** @var ClientMaster $client */
/** @var ClientChargePlan $unlimitedPlan */
/** @var ClientChargePlan $limitedPlan */
/** @var ClientChargePlan $limitedPlan2 */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため掲載企業権限を代入
Yii::$app->user->identity = Manager::findIdentity(3);

// 項目を全て任意にする
$jobColumnSets = JobColumnSet::find()->where(['is_must' => 1])->all();
foreach ($jobColumnSets as $jobColumnSet) {
    /** @var $jobColumnSet JobColumnSet */
    $jobColumnSet->is_must = 0;
    $jobColumnSet->save();
}

// 各メッセージ
$notDay_s = '掲載開始日の書式が正しくありません。';
$tooOld_s = '掲載開始日は1920/01/01以降の日付にしてください.';
$tooFeature_s = '掲載開始日は2037/12/31以前の日付にしてください.';
$require_s = '掲載開始日は必須項目です。';
$notDay_e = '掲載終了日の書式が正しくありません。';
$tooOld_e = '掲載終了日は1920/01/01以降の日付にしてください.';
$tooFeature_e = '掲載終了日は2037/12/31以前の日付にしてください.';
$compare_e = '掲載終了日は掲載開始日より後の日付にしてください.';
$attention = '料金プランの有効期間と入力された期間に差異があります(登録は可能です)';

// 使うmodelを準備
$corp = CorpMaster::find()->one();
$client = ClientMaster::find()->one();
$plans = ClientChargePlan::find()->all();
$unlimitedPlan = $plans[0];
$limitedPlan = $plans[1];
$limitedPlan2 = $plans[2];

// corpが有効であることを担保
$corp->valid_chk = 1;
$corp->save();

// clientが有効かつcorpの管理下であることを担保
$client->valid_chk = 1;
$client->corp_master_id = $corp->id;
$client->save();

// planの有効日数を調整
$unlimitedPlan->period = null;
$unlimitedPlan->valid_chk = 1;
$limitedPlan->period = 12;
$limitedPlan->valid_chk = 1;
$limitedPlan2->period = 31;
$limitedPlan2->valid_chk = 1;
$unlimitedPlan->save();
$limitedPlan->save();
$limitedPlan2->save();

// clientが3種類のplanを持っていることを担保
$client->unlinkAll('clientCharges', true);

$clientCharge = new ClientCharge();
$clientCharge->client_charge_plan_id = $unlimitedPlan->id;
$client->link('clientCharges', $clientCharge);

$clientCharge = new ClientCharge();
$clientCharge->client_charge_plan_id = $limitedPlan->id;
$client->link('clientCharges', $clientCharge);

$clientCharge = new ClientCharge();
$clientCharge->client_charge_plan_id = $limitedPlan2->id;
$client->link('clientCharges', $clientCharge);

$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('掲載企業権限での求人原稿登録画面の日付入力UIのテスト');
//----------------------
// 管理者でログインしてcreate画面へ遷移
//----------------------
$I->amGoingTo('掲載企業でアクセス');
$loginPage->login('admin03', 'admin03');
$I->wait(1);
$page = JobDateInput::openBy($I);
$I->wait(1);
$I->cantSeeElement('#jobmaster-disp_end_date');

//----------------------
// 初期状態から有効日数無しプランへ変更
//----------------------
$I->amGoingTo('初期状態から有効日数無しプランへ変更');
$page->changePlan($unlimitedPlan);
$I->seeElement('#jobmaster-disp_end_date');

//----------------------
// プラン：有効日数なし
// 開始日：正常な値
// 終了日：開始日より前の値（比較エラー）
// からのプラン変更
//----------------------
$I->amGoingTo('unlimited plan、比較エラーの状態からlimited planへ');
$page->setCompareError();
$page->changePlan($limitedPlan);
$I->wait(1);
// inputが消え、比較エラーも消え、textが表示される
$I->cantSeeElement('#jobmaster-disp_end_date');
$I->cantSee($compare_e);
$I->see(JobDateInput::addDays($page->startDate, $page->plan->period -1), '#dispEndText');

$I->amGoingTo('unlimited plan、比較エラーの状態から空へ');
$page->changePlan($unlimitedPlan);
$page->setCompareError();
$page->changePlan('');
$I->wait(1);
// inputが消え、空白が表示され、validateの痕跡が消える
$page->cantSeeEnd();

//----------------------
// プラン：空
// 開始日：正常な値
// 終了日：表示なし（裏で比較エラー）
// からのプラン変更
//----------------------
$I->amGoingTo('プラン空、裏で比較エラーの状態からunlimited planに変更');
$page->changePlan($unlimitedPlan);
$I->wait(1);
// 終了日inputが表示され、そこに前の値が入っており、比較エラーが表示される
$I->seeElement('#jobmaster-disp_end_date');
$page->seeEnd($page->endDate);
$I->see($compare_e);

$I->amGoingTo('プラン空、裏で比較エラーの状態からlimited planに変更');
$page->changePlan('');
$page->changePlan($limitedPlan);
// テキストが表示され、比較エラーは表示されない
$I->see(JobDateInput::addDays($page->startDate, $page->plan->period -1), '#dispEndText');
$I->cantSee($compare_e);

//----------------------
// プラン：有効日数有り
// 開始日：正常な値
// 終了日：テキスト（裏で比較エラー）
// からの変更
//----------------------
// プランの変更
$I->amGoingTo('limited plan、裏で比較エラーの状態からlimited planに変更');
$page->changePlan($limitedPlan2);
// テキストが変わり、比較エラーは表示されない
$I->see(JobDateInput::addDays($page->startDate, $page->plan->period -1), '#dispEndText');
$I->cantSee($compare_e);

$I->amGoingTo('limited plan、裏で比較エラーの状態からプランを空に変更');
$page->changePlan('');
// テキストが空白に変化し、validateの痕跡が消える
$page->cantSeeEnd();

// 開始日の変更
$I->amGoingTo('limited plan、裏で比較エラーの状態から開始日に不正な値');
$page->changePlan($limitedPlan);
$page->fillStart('aaa');
// テキストが空白に変化し、validateの痕跡が消える
$page->cantSeeEnd();
$I->cantSee($compare_e);

//----------------------
// プラン：有効日数有り
// 開始日：空
// 終了日：表示なし
// からの変更
//----------------------
//開始日の変更
$page->fillStart('');
$I->amGoingTo('limited plan、開始日空の状態から開始日に不正な値');
$page->fillStart('1919/12/31');
// 空白のままで、validateの痕跡も出ない
$page->cantSeeEnd();

$I->amGoingTo('limited plan、開始日空の状態から開始日に正常な値');
$page->fillStart('');
$page->fillStart(date('Y/m/d'));
// テキストが表示される
$I->see(JobDateInput::addDays($page->startDate, $page->plan->period -1), '#dispEndText');

// プランの変更
$I->amGoingTo('limited plan、開始日空の状態からプランを空に');
$page->fillStart('');
$page->changePlan('');
// 空白のままで、validateの痕跡も出ない
$page->cantSeeEnd();

//----------------------
// プラン：有効日数なし
// 開始日：空
// 終了日：正常な値
// からのプラン変更
//----------------------
$page->changePlan($unlimitedPlan);

$I->amGoingTo('limited plan、終了日のみ入力状態からlimited planに変更');
$page->changePlan($limitedPlan);
// inputが消え、空白が表示され、validateの痕跡が消える
$page->cantSeeEnd();

$I->amGoingTo('limited plan、終了日のみ入力状態からプランを空に変更');
$page->changePlan($unlimitedPlan);
$page->changePlan('');
// inputが消え、空白が表示され、validateの痕跡が消える
$page->cantSeeEnd();

//----------------------
// プラン：有効日数なし
// 開始日：正常な値
// 終了日：正常な値
// からのプラン変更
//----------------------
$page->changePlan($unlimitedPlan);
$page->setToday();

$I->amGoingTo('limited plan、両方入力状態からlimited planに変更');
$page->changePlan($limitedPlan);
$I->see(JobDateInput::addDays($page->startDate, $page->plan->period -1), '#dispEndText');

// 初期化test //////////////////////////////////////////////////////////////////////////////////////////////////////////
//----------------------
// 有効日数有りプランを登録して
// update画面の初期状態を確認
//----------------------
// 状態を有効に
$I->selectOption('input[name=JobMaster\\[valid_chk\\]]', 1);

// 必須な募集要項入力
// todo 今現在必須になっている会社名のみに入力しているが、job_column_setでの設定に柔軟に対応できるよう修正する
// todo 上で必須を外しているので不要だが、いろいろノウハウが書いてあるのでコメントアウトで残してます
//$I->switchToIFrame("iframeName"); // 操作対象をiframeにスイッチ
//$I->click('#main-corp_name_disp'); // Editableのinputを表示
//$I->fillField('.editable-input > textarea', '会社名'); // Editableのinputに入力
//$I->pressKey('.editable-input > textarea', [WebDriverKeys::CONTROL, WebDriverKeys::ENTER]); // ctrl + enterで変更をsubmit
//$I->switchToIFrame(); // 操作対象を親画面にスイッチ

// 検索キーの入力
$I->click('選択する');
$I->wait(1);
$I->executeJS('$("#pref13").collapse("show")'); // アコーディオンを開く
$I->checkOption('input[name=JobDist\\[itemIds\\]\\[\\]]', '千代田区');
$I->click('変更を保存');
$I->wait(1);

// 登録
$I->click('登録する');
$I->wait(1);
$I->click('OK');
$I->wait(2);

// 登録した原稿のupdate画面へ
$jobId = JobMaster::find()->select('max(id)')->scalar();
$I->amOnPage('/manage/secure/job/update?id=' . $jobId);
$I->wait(1);

$I->amGoingTo('有効日数有りプランの時はinput自体表示されない');
$I->cantSeeElement('#jobmaster-client_charge_plan');
$I->see($page->plan->plan_name, '.field-clientchargeplan-plan_name');
$I->cantSeeElement('#jobmaster-disp_start_date');
$I->see($page->startDate, '.field-jobmaster-disp_start_date');
$I->cantSeeElement('#jobmaster-disp_end_date');
$I->see(JobDateInput::addDays($page->startDate, $page->plan->period -1), '.field-jobmaster-disp_end_date');

// 登録
$I->amGoingTo('有効日数有りプランが登録できる');
$I->click('変更する');
$I->wait(1);
$I->click('OK');
$I->wait(2);
$I->seeInTitle('求人原稿情報 - 完了');

//----------------------
// 有効日数無しプランを登録して
// update画面の初期状態を確認
//----------------------
// 画面から編集できないのでモデルからplanを変更する
$jobModel = JobMaster::findOne($jobId);
$jobModel->client_charge_plan_id = $unlimitedPlan->id;
$jobModel->save();
$I->wait(1);

// 登録した原稿のupdate画面へ
$I->amOnPage('/manage/secure/job/update?id=' . $jobId);
$I->wait(1);

$I->amGoingTo('有効日数有りプランの時はplan以外のinputは表示される');
$I->cantSeeElement('#jobmaster-client_charge_plan');
$I->see($unlimitedPlan->plan_name, '.field-clientchargeplan-plan_name');
$I->seeElement('#jobmaster-disp_start_date');
$I->canSeeInField('#jobmaster-disp_start_date', $page->startDate);
$I->seeElement('#jobmaster-disp_end_date');
// 有効日数有りプランで作ったレコードなのでこの値が入る
$I->canSeeInField('#jobmaster-disp_end_date', JobDateInput::addDays($page->startDate, $page->plan->period -1));

// 登録
$I->amGoingTo('有効日数なしプランが登録できる');
$I->click('変更する');
$I->wait(1);
$I->click('OK');
$I->wait(2);
$I->seeInTitle('求人原稿情報 - 完了');
