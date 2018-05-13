<?php

use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
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
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

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
$I->wantTo('求人原稿登録画面の日付入力UIのテスト');
//----------------------
// 運営元でログインしてcreate画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = JobDateInput::openBy($I);
$I->wait(1);

//----------------------
// テストに必要な初期値(代理店、掲載企業、有効日数無しプラン)を入力
//----------------------
// 代理店のselect2に値をinput
$I->amGoingTo('代理店、掲載企業、有効日数無しプランを入力');
$I->click('#jobmaster-corpmasterid + span .select2-selection__arrow');
$I->wait(1);
$I->executeJS("$('input.select2-search__field').attr('id', 'groups_input');");
$I->fillField('#groups_input', $corp->corp_name);
$I->wait(1);
// エンターを押す
$I->pressKey('#groups_input', WebDriverKeys::ENTER);
// もしくは選択肢をクリック
//$I->click('li.select2-results__option');
$I->wait(1);
$I->click('#jobmaster-client_master_id + span .select2-selection__arrow');
$I->wait(1);




// 日付基本チェック ////////////////////////////////////////////////////////////////////////////////////////////////////
//----------------------
// 日付の書式チェック
//----------------------
$I->amGoingTo('正常な値入力');
$page->setToday();
$page->dateHasNoError();

// 両方正常な状態から開始日に不正な値を入力
// →終了日はエラーなし、開始日にエラー
$I->amGoingTo('両方正常な状態から開始日に不正な文字を入力');
$page->setToday();
$page->fillStart('aaa');
$I->wait(1);
$page->endHasNoError();
$page->checkStartInvalid($notDay_s);

$I->amGoingTo('開始日からフォーカスを外すと、不正な文字列の際は今日の日付が入る');
$page->seeStart(date('Y/m/d'));

$I->amGoingTo('期限自由プランなので終了日は影響されない');
$page->seeEnd($page->endDate);

// 両方正常な状態から開始日に不正な値を入力
// →開始日はエラーなし、終了日にエラー
$I->amGoingTo('終了日に不正な文字を入力');
$page->setToday();
$page->fillEnd('aaa');
$I->wait(1);
$page->startHasNoError();
$page->checkEndInvalid($notDay_e);

$I->amGoingTo('終了日からフォーカスを外すと、不正な文字列の際は今日の日付が入る');
$page->seeEnd(date('Y/m/d'));


//----------------------
// 日付の期間チェック・昔
//----------------------
// 両方正常な状態から開始日に昔過ぎる日付を入力
$I->amGoingTo('開始日に昔過ぎる日付を入力');
$page->setToday();
$page->fillStart('1919/12/31');
$I->wait(1);
// →終了日はエラーなし、開始日にエラー
$page->endHasNoError();
$page->checkStartInvalid($tooOld_s);

$I->amGoingTo('開始日からフォーカスを外すと、範囲外の日付の際は空白が入る');
$page->seeStart('');

$I->amGoingTo('掲載開始日は必須');
$I->see($require_s);

$I->amGoingTo('期限自由プランなので終了日は影響されない');
$page->seeEnd($page->endDate);

// 両方正常な状態から終了日に昔過ぎる日付を入力
$I->amGoingTo('終了日に昔過ぎる日付を入力');
$page->setToday();
$page->fillEnd('1919/12/31');
$I->wait(1);
// →開始日はエラーなし、終了日に過去エラー
$page->startHasNoError();
$page->checkEndInvalid($tooOld_e);

$I->amGoingTo('終了日からフォーカスを外すと、範囲外の日付の際は空白が入る');
$page->seeEnd('');


//----------------------
// 日付の期間チェック・未来
//----------------------
// 両方正常な状態から開始日に未来過ぎる日付を入力

$I->amGoingTo('開始日に未来過ぎる日付を入力');
$page->setToday();
$page->fillStart('2038/01/01');
$I->wait(1);
// →終了日はエラーなし、開始日にエラー
$I->canSee($compare_e);
$page->checkStartInvalid($tooFeature_s);

$I->amGoingTo('開始日からフォーカスを外すかエンターを押すと、範囲外の日付の際は空白が入る');
$page->seeStart('');

$I->amGoingTo('掲載開始日は必須');
$I->see($require_s);

$I->amGoingTo('期限自由プランなので終了日は影響されない');
$page->seeEnd($page->endDate);

// 両方正常な状態から終了日に未来過ぎる日付を入力
$I->amGoingTo('終了日に未来過ぎる日付を入力');
$page->setToday();
$page->fillEnd('2038/01/01');
$I->wait(1);
// →開始日はエラーなし、終了日にエラー
$page->startHasNoError();
$page->checkEndInvalid($tooFeature_e);

$I->amGoingTo('終了日からフォーカスを外すと、範囲外の日付の際は空白が入る');
$page->seeEnd('');


//----------------------
// 開始日＞終了日(基本)
//----------------------
$I->amGoingTo('開始日＞終了日で両方範囲内');
$page->setToday();
$I->wait(1);
$I->amGoingTo('開始日を後ろにしてエラーを出す');
$page->fillStart(JobDateInput::addDays($page->endDate, 1));
$I->wait(1);
$I->canSee($compare_e);

$I->amGoingTo('終了日を後ろ(開始日と同日)にしてエラーを消す');
$page->fillEnd($page->startDate);
$I->wait(1);
$I->cantSee($compare_e);

$I->amGoingTo('終了日を前にしてエラーを出す');
$page->fillEnd(JobDateInput::addDays($page->endDate, -1));
$I->wait(1);
$I->canSee($compare_e);

$I->amGoingTo('開始日を前(終了日と同日)にしてエラーを消す');
$page->fillStart($page->endDate);
$I->wait(1);
$I->cantSee($compare_e);

//----------------------
// エラー重複時
//----------------------
// 開始日＞終了日→終了日を変更
$I->amGoingTo('開始日＞終了日→終了日が昔過ぎる');
$page->setCompareError();
$page->fillEnd('1919/12/31');
$I->wait(1);
// 比較エラーが消えて過去エラーが出る
$I->cantSee($compare_e);
$I->see($tooOld_e);

$I->amGoingTo('開始日＞終了日→終了日が未来過ぎる');
$page->setCompareError();
$page->fillEnd('2038/01/01');
$I->wait(1);
// 比較エラーが消えて未来エラーが出る
$I->cantSee($compare_e);
$I->see($tooFeature_e);

$I->amGoingTo('開始日＞終了日→終了日の書式が変');
$page->setCompareError();
$page->fillEnd('aaa');
$I->wait(1);
// 比較エラーが消えて書式エラーが出る
$I->cantSee($compare_e);
$I->see($notDay_e);

// 開始日＞終了日→開始日を変更
$I->amGoingTo('開始日＞終了日→開始日が未来過ぎる');
$page->setCompareError();
$page->fillStart('2038/01/01');
$I->wait(1);
// 終了日は比較エラー、開始日は未来エラーが出る
$I->see($compare_e);
$I->see($tooFeature_s);

$I->amGoingTo('開始日＞終了日→開始日が過去過ぎる');
$page->setCompareError();
$page->fillStart('1919/12/31');
$I->wait(1);
// 終了日のエラーは消え、開始日は過去エラーが出る
$page->endHasNoError();
$I->see($tooOld_s);

$I->amGoingTo('開始日＞終了日→開始日の書式が変');
$page->setCompareError();
$page->fillStart('aaa');
$I->wait(1);
// 終了日のエラーは消え、開始日は書式エラーが出る
$page->endHasNoError();
$I->see($notDay_s);

$I->amGoingTo('開始日＞終了日→開始日が空白');
$page->setCompareError();
$page->fillStart('');
$I->wait(1);
// 終了日のエラーは消え、開始日は必須エラーが出る
$page->endHasNoError();
$I->see($require_s);

// プランの有効日数の絡むテスト ////////////////////////////////////////////////////////////////////////////////////////
//----------------------
// プラン:有効日数あり
// 開始日:空
// 終了日:今日の日付
// からの変更
//----------------------
// 有効日数有りプランに変更
$page->changePlan($limitedPlan);
$I->wait(1);

// 開始日を変更
$I->amGoingTo('終了日のみ入力状態から正常な開始日を入力');
$page->onlyEnd();
$page->fillStart('2016/10/13');
$I->wait(1);
// 終了日が自動入力され、警告文は無し
$page->seeEnd(JobDateInput::addDays($page->startDate, $limitedPlan->period - 1));
$I->cantSee($attention);

$I->amGoingTo('終了日のみ入力状態から未来過ぎる開始日を入力');
$page->onlyEnd();
$page->fillStart('2038/01/01');
$I->wait(1);
// 終了日はそのまま、警告文は無し
$page->seeEnd($page->endDate);
$I->cantSee($attention);

$I->amGoingTo('終了日のみ入力状態から昔過ぎる開始日を入力');
$page->onlyEnd();
$page->fillStart('1919/12/31');
$I->wait(1);
// 終了日はそのまま、警告文は無し
$page->seeEnd($page->endDate);
$I->cantSee($attention);

$I->amGoingTo('終了日のみ入力状態から書式の違う開始日を入力');
$page->onlyEnd();
$page->fillStart('aaa');
$I->wait(1);
// 終了日はそのまま、警告文は無し
$page->seeEnd($page->endDate);
$I->cantSee($attention);

// プランを変更
$I->amGoingTo('終了日のみ入力状態からlimited planに変更');
$page->onlyEnd();
$page->changePlan($limitedPlan2);
$I->wait(1);
// 終了日はそのまま、警告文は無し
$page->seeEnd($page->endDate);
$I->cantSee($attention);

$I->amGoingTo('終了日のみ入力状態からunlimited planに変更');
$page->onlyEnd();
$page->changePlan($unlimitedPlan);
$I->wait(1);
// 終了日はそのまま、警告文は無し
$page->seeEnd($page->endDate);
$I->cantSee($attention);

//----------------------
// プラン:有効日数あり
// 開始日:今日の日付
// 終了日:今日の日付（警告文あり）
// からの変更
//----------------------
$I->amGoingTo('警告文が出ている');
$page->changePlan($limitedPlan);
$page->setToday();
$I->see($attention);

// 開始日を変更
$I->amGoingTo('警告文が出ている状態から正常な開始日を入力');
$page->setToday();
$page->fillStart('2016/01/01');
$I->wait(1);
// 警告文は消え、終了日は自動入力される
$I->cantSee($attention);
$page->seeEnd(JobDateInput::addDays('2016/01/01', $limitedPlan->period -1));

$I->amGoingTo('警告文が出ている状態から未来過ぎる開始日を入力');
$page->setToday();
$page->fillStart('2038/01/01');
$I->wait(1);
// 警告文は消え、終了日は変化しない
$I->cantSee($attention);
$page->seeEnd($page->endDate);

$I->amGoingTo('警告文が出ている状態から昔過ぎる開始日を入力');
$page->setToday();
$page->fillStart('1919/12/31');
$I->wait(1);
// 警告文は消え、終了日は変化しない
$I->cantSee($attention);
$page->seeEnd($page->endDate);

$I->amGoingTo('警告文が出ている状態から書式の違う開始日を入力');
$page->setToday();
$page->fillStart('aaa');
$I->wait(1);
// 警告文は消え、終了日は変化しない
$I->cantSee($attention);
$page->seeEnd($page->endDate);

$I->amGoingTo('警告文が出ている状態から空白の開始日を入力');
$page->setToday();
$page->fillStart('');
$I->wait(1);
// 警告文は消え、終了日は変化しない
$I->cantSee($attention);
$page->seeEnd($page->endDate);

// 終了日を変更
$I->amGoingTo('警告文が出ている状態から有効日数と矛盾する終了日を入力');
$page->setToday();
$page->fillEnd(JobDateInput::addDays($page->endDate, 1));
// 警告文が出続ける
$I->see($attention);

$I->amGoingTo('警告文が出ている状態から有効日数と矛盾しない終了日を入力');
$page->setToday();
$page->fillEnd(JobDateInput::addDays($page->startDate, $limitedPlan->period -1));
// 警告文が消える
$I->cantSee($attention);

$I->amGoingTo('警告文が出ている状態から未来過ぎる終了日を入力');
$page->setToday();
$page->fillEnd('2038/01/01');
// 警告文が消える(終了日にはエラー)
$I->cantSee($attention);
$I->see($tooFeature_e);

$I->amGoingTo('警告文が出ている状態から昔過ぎる終了日を入力');
$page->setToday();
$page->fillEnd('1919/12/31');
// 警告文が消える(終了日にはエラー)
$I->cantSee($attention);
$I->see($tooOld_e);

$I->amGoingTo('警告文が出ている状態から書式の違う終了日を入力');
$page->setToday();
$page->fillEnd('aaa');
// 警告文が消える(終了日にはエラー)
$I->cantSee($attention);
$I->see($notDay_e);

$I->amGoingTo('警告文が出ている状態から比較エラーの出る終了日を入力');
$page->setToday();
$page->fillEnd(JobDateInput::addDays($page->startDate, -1));
// 警告文が消える(終了日にはエラー)
$I->cantSee($attention);
$I->see($compare_e);

// プランを変更
$I->amGoingTo('警告文が出ている状態からlimited planに変更');
$page->setToday();
$page->changePlan($limitedPlan2);
// 警告文は消え、終了日が自動入力される
$I->cantSee($attention);
$page->seeEnd(JobDateInput::addDays($page->startDate, $limitedPlan2->period -1));


$I->amGoingTo('警告文が出ている状態からunlimited planに変更');
$page->setToday();
$page->changePlan($unlimitedPlan);
// 警告文は消え、終了日は変化しない
$I->cantSee($attention);
$page->seeEnd($page->endDate);

//----------------------
// プラン:有効日数あり
// 開始日:明日の日付
// 終了日:今日の日付（比較エラー文あり）
// からの変更
//----------------------
$page->changePlan($limitedPlan);
// 開始日を変更
$I->amGoingTo('比較エラーが出ている状態から正常な開始日を入力');
$page->setCompareError();
$page->fillStart('2016/01/01');
$I->wait(1);
// 警告文は出ず、エラー文も消え、終了日は自動入力される
$I->cantSee($attention);
$I->cantSee($compare_e);
$page->seeEnd(JobDateInput::addDays($page->startDate, $limitedPlan->period -1));

$I->amGoingTo('比較エラーが出ている状態から未来過ぎる開始日を入力');
$page->setCompareError();
$page->fillStart('2038/01/01');
$I->wait(1);
// 警告文は出ず、比較エラーは出続け、終了日は変化しない
$I->cantSee($attention);
$I->see($compare_e);
$page->seeEnd($page->endDate);

$I->amGoingTo('比較エラーが出ている状態から昔過ぎる開始日を入力');
$page->setCompareError();
$page->fillStart('1919/12/31');
$I->wait(1);
// 警告文は出ず、エラー文も消え、終了日は変化しない
$I->cantSee($attention);
$I->cantSee($compare_e);
$page->seeEnd($page->endDate);

$I->amGoingTo('比較エラーが出ている状態から書式の違う開始日を入力');
$page->setCompareError();
$page->fillStart('aaa');
$I->wait(1);
// 警告文は出ず、エラー文も消え、終了日は変化しない
$I->cantSee($attention);
$I->cantSee($compare_e);
$page->seeEnd($page->endDate);

$I->amGoingTo('比較エラーが出ている状態から空白の開始日を入力');
$page->setCompareError();
$page->fillStart('');
$I->wait(1);
// 警告文は出ず、エラー文も消え、終了日は変化しない
$I->cantSee($attention);
$I->cantSee($compare_e);
$page->seeEnd($page->endDate);

// プランを変更
$I->amGoingTo('比較エラーが出ている状態からlimited planに変更');
$page->setCompareError();
$page->changePlan($limitedPlan2);
// 警告文は出ず、エラー文は消え、終了日が自動入力される
$I->cantSee($attention);
$I->cantSee($compare_e);
$page->seeEnd(JobDateInput::addDays($page->startDate, $limitedPlan2->period -1));


$I->amGoingTo('比較エラーが出ている状態からunlimited planに変更');
$page->setCompareError();
$page->changePlan($unlimitedPlan);
// 警告文は出ず、エラー文は出続け、終了日は変化しない
$I->cantSee($attention);
$page->seeEnd($page->endDate);
$I->see($compare_e);

// 初期化test //////////////////////////////////////////////////////////////////////////////////////////////////////////
//----------------------
// 有効日数なしプランを登録して
// update画面の初期状態を確認
//----------------------
// 日付を両方今日に（プランは引き続き有効日数無しプラン）
$page->setToday();

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
$I->amGoingTo('有効日数なしプランが登録できる');
$I->click('登録する');
$I->wait(1);
$I->click('OK');
$I->wait(2);
$I->seeInTitle('求人原稿情報 - 完了');

// 登録した原稿のupdate画面へ
$jobId = JobMaster::find()->select('max(id)')->scalar();
$I->amOnPage('/manage/secure/job/update?id=' . $jobId);
$I->wait(1);

$I->amGoingTo('有効日数なしプランの時は警告文は出ず、inputはすべて表示される');
$I->cantSee($attention);
$I->seeElement('#jobmaster-client_charge_plan_id');
$I->seeOptionIsSelected('#jobmaster-client_charge_plan_id', $page->plan->plan_name);
$I->seeElement('#jobmaster-disp_start_date');
$I->canSeeInField('#jobmaster-disp_start_date', $page->startDate);
$I->seeElement('#jobmaster-disp_end_date');
$I->canSeeInField('#jobmaster-disp_end_date', $page->endDate);

//----------------------
// 有効日数ありプランを
// 有効日数通りに登録して
// update画面の初期状態を確認
//----------------------
// プランを有効日数有りに変更して(同時にプランの有効日数通りの終了日が入る)登録
$page->changePlan($limitedPlan);
$I->amGoingTo('有効日数有りプランが登録できる');
$I->wait(1);
$I->click('変更する');
$I->wait(1);
$I->click('OK');
$I->wait(2);
$I->seeInTitle('求人原稿情報 - 完了');

$I->amOnPage('/manage/secure/job/update?id=' . $jobId);
$I->wait(1);

$I->amGoingTo('有効日数有りプランで有効日数と日付に矛盾が無い場合、警告文は出ず、inputはすべて表示される');
$I->cantSee($attention);
$I->seeElement('#jobmaster-client_charge_plan_id');
$I->seeOptionIsSelected('#jobmaster-client_charge_plan_id', $page->plan->plan_name);
$I->seeElement('#jobmaster-disp_start_date');
$I->canSeeInField('#jobmaster-disp_start_date', $page->startDate);
$I->seeElement('#jobmaster-disp_end_date');
$I->canSeeInField('#jobmaster-disp_end_date', JobDateInput::addDays($page->startDate, $page->plan->period -1));

//----------------------
// 有効日数ありプランを
// 有効日とずらして登録して
// update画面の初期状態を確認
//----------------------
// 両方今日の日付にして警告文を出して登録
$page->setToday();
$I->amGoingTo('有効日数有りプランが日付をずらしても登録できる');
$I->wait(1);
$I->click('変更する');
$I->wait(1);
$I->click('OK');
$I->wait(2);
$I->seeInTitle('求人原稿情報 - 完了');

$I->amOnPage('/manage/secure/job/update?id=' . $jobId);
$I->wait(1);

$I->amGoingTo('有効日数有りプランで有効日数と日付に矛盾がある場合は警告文が出て、inputはすべて表示される');
$I->see($attention);
$I->seeElement('#jobmaster-client_charge_plan_id');
$I->seeOptionIsSelected('#jobmaster-client_charge_plan_id', $page->plan->plan_name);
$I->seeElement('#jobmaster-disp_start_date');
$I->canSeeInField('#jobmaster-disp_start_date', $page->startDate);
$I->seeElement('#jobmaster-disp_end_date');
$I->canSeeInField('#jobmaster-disp_end_date', $page->endDate);
