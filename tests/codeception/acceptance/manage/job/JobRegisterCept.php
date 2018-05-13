<?php
use app\models\manage\JobColumnSet;
use app\models\manage\ManageMenuMain;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\Dist;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\job\JobRegisterPage;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\fixtures\JobColumnSetFixture;
use tests\codeception\fixtures\JobColumnSubsetFixture;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$createMenu = ManageMenuMain::findFromRoute('/manage/secure/job/create');
$updateMenu = ManageMenuMain::findFromRoute('/manage/secure/job/update');

// 必須項目を無くす
// todo next corp_column_setのレコードの整備・連携
(new JobColumnSetFixture())->unload();
(new JobColumnSetFixture())->load();
(new JobColumnSubsetFixture())->unload();
(new JobColumnSubsetFixture())->load();
JobColumnSet::updateAll(['is_must' => JobColumnSet::NOT_MUST], ['is_must' => JobColumnSet::MUST]);

// disp_type.disp_type_no=3の代理店、掲載企業、プランの組み合わせを準備
$array = JobRegisterPage::initPlan();
$corp = $array['corp'];
$client = $array['client'];

// 表示する都道府県のモデル取得
/** @var Pref[] $prefs */
$prefs = Pref::find()->joinWith('area')->where([Area::tableName() . '.valid_chk' => 1])->all();

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('求人情報登録変更のテスト');

//----------------------
// 運営元でログインして一覧へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);
$page = JobRegisterPage::openBy($I);
$I->wait(1);

//----------------------
// 新規登録画面
//----------------------
$page->goCreate('求人原稿を登録する', $createMenu);
// todo next クリアボタンの動作検証(今回は変な遷移が起きないかだけ)
$I->amGoingTo('クリアボタン押下');
$I->click('クリア');
$I->wait(1);
$I->see($createMenu->title, 'h1');

// 各項目入力
// todo next 日付以外の各項目client side動作検証
$I->amGoingTo('各項目入力');
// 代理店のselect2に値をinput
$I->amGoingTo('代理店、掲載企業、プランを入力');

$page->checkHintInput('corpLabel');
$page->checkHintInput('client_master_id');
$page->checkHintInput('client_charge_plan_id');
$page->checkHintInput('disp_start_date');
$page->checkHintInput('disp_end_date', false);

$page->checkHintInput('application_tel_1', false);
$page->checkHintInput('application_tel_2', false);
$page->checkHintInput('application_mail', false);
$page->checkHintInput('agent_name', false);
$page->checkHintInput('mail_body', false);

$I->click('//*[@id="select2-jobmaster-corpmasterid-container"]');
$I->wait(2);
$I->fillField('//span[@class="select2-search select2-search--dropdown"]/input', $corp->corp_name);
$I->wait(2);
$I->click("//*[@id='select2-jobmaster-corpmasterid-results']/li[text()='$corp->corp_name']");
$I->wait(2);

//$I->click('#select2-jobmaster-client_master_id-container');
//$I->wait(2);
//$I->click("//*[@id='select2-jobmaster-client_master_id-results']/li[text()='$client->client_name']");
//$I->wait(2);

// 日付を入力
$I->amGoingTo('日付を入力');
$page->fillAndRemember('disp_start_date', date('Y/m/d'));

// 状態を有効に
$I->amGoingTo('状態を有効に');
$I->selectOption('input[name=JobMaster\\[valid_chk\\]]', 1);

// 検索キーの入力
$I->amGoingTo('検索キーの入力');
$I->amGoingTo('都道府県モーダルの表示検証');
$I->click('選択する');
$I->wait(1);
// 表示されている都道府県の確認
foreach ($prefs as $key => $pref) {
    $i = $key + 1;
    $I->see($pref->pref_name, "//div[@id='pref-accordion']/div[{$i}]/div[1]/h4");
}
$I->amGoingTo("{$prefs[0]->dist[0]->dist_name}をチェック");
$I->executeJS("$('#pref{$prefs[0]->id}').collapse('show')"); // アコーディオンを開く
$I->wait(1);
$I->checkOption("div#pref{$prefs[0]->id} input[name=JobDist\\[itemIds\\]\\[\\]]", $prefs[0]->dist[0]->dist_name);

// チェックボックスの検証（3番目の都道府県で検証）
$pref = $prefs[2];
$I->amGoingTo("{$pref->pref_name}をチェック");
$I->click('(//label[@class="pref-selection-label checkbox"])[3]');
$I->expect('全体チェックを入れると配下が全てチェックされている');
foreach ($pref->dist as $dist) {
    $I->seeCheckboxIsChecked('input[name=JobDist\[itemIds\]\[\]][value="' . $dist->id . '"]');
}
$I->wait(1);
$I->expect('全体チェックを外すと配下全てのチェックが外れる');
$I->click('(//label[@class="pref-selection-label checkbox"])[3]');
foreach ($pref->dist as $dist) {
    $I->cantSeeCheckboxIsChecked('input[name=JobDist\[itemIds\]\[\]][value="' . $dist->id . '"]');
}

$I->click('(//label[@class="pref-selection-label checkbox"])[3]');
$I->wait(1);
$I->executeJS("$('#pref{$pref->id}').collapse('show')"); // アコーディオンを開く
$I->wait(1);

$I->expect('配下のチェックを1つ外すと全体チェックが外れる');
$I->uncheckOption("div#pref{$pref->id} input[name=JobDist\\[itemIds\\]\\[\\]]", $pref->dist[0]->dist_name);
$I->cantSeeCheckboxIsChecked('(//input[@class="hasChildren pref-selection-checkbox custom-checkbox"])[3]');

$I->expect('配下が全てチェック状態となると、全体チェックが入る');
$I->checkOption("div#pref{$pref->id} input[name=JobDist\\[itemIds\\]\\[\\]]", $pref->dist[0]->dist_name);
$I->wait(1);
$I->seeCheckboxIsChecked('(//input[@class="hasChildren pref-selection-checkbox custom-checkbox"])[3]');

$I->click('変更を保存');
$I->wait(1);
$I->expect('選択された市町村の名前が表示されているかチェック');
foreach ($pref->dist as $dist) {
    $I->see($dist->dist_name, "//*[@id='distSelected']");
}
$I->wait(1);

// 登録
$page->submit('登録');

//----------------------
// 登録検証
//----------------------
$I->amGoingTo('完了画面から一覧へ遷移');
$I->click('求人原稿情報一覧へ');
$I->wait(1);
$I->seeInTitle('求人情報一覧');

//----------------------
// 一覧表示検証
//----------------------
$I->amGoingTo('一覧表示検証');
$I->click('この条件で表示する');
$I->wait(5);
$jobNo = $I->grabTextFrom('//tbody/tr[1]/td[2]');
// todo next 一覧画面に登録した内容が表示されているか

//----------------------
// 一覧からコピーして登録
//----------------------
$I->amGoingTo('一覧からコピーして登録');
$page->clickActionColumn(1, 2);
$I->wait(5);
$I->seeInTitle($createMenu->title);
$I->see($createMenu->title, 'h1');
// todo next コピー画面に登録した内容がinputされているか

// todo next クリアボタンの動作検証(今回は変な遷移が起きないかだけ)
$I->amGoingTo('クリアボタン押下');
$I->click('クリア');
$I->wait(5);
$I->see($createMenu->title, 'h1');

// 登録する
$page->submit('登録', true);

//----------------------
// コピーのnoを確認した上で削除する
//----------------------
$I->amGoingTo('完了画面から一覧へ遷移');
$I->click('求人原稿情報一覧へ');
$I->wait(3);
$I->click('この条件で表示する');
$I->wait(5);
$I->expect('登録した原稿のナンバー+1が登録されている');
$I->see($jobNo + 1, '//tbody/tr[1]/td[2]');

$I->amGoingTo('コピーを削除する');
$page->clickCheckbox(0);
$page->clickCheckbox(1);
$I->click('まとめて削除する');
$I->wait(1);
$I->click('OK');
$I->wait(3);

//----------------------
// 変更画面
//----------------------
$I->amGoingTo('一覧から更新へ遷移');
$page->clickActionColumn(1, 1);
$I->wait(5);
$I->seeInTitle($updateMenu->title);
$I->see($updateMenu->title, 'h1');
// todo next 更新画面に登録した内容がinputされているか

// todo next パン屑リンク検証

// todo next クリアボタンの動作検証(今回は変な遷移が起きないかだけ)
$I->amGoingTo('クリアボタン押下');
$I->click('クリア');
$I->wait(5);
$I->see($updateMenu->title, 'h1');

// 変更する
$page->submit('変更');

//----------------------
// 変更画面からプレビューとコピー
//----------------------
$I->amGoingTo('完了画面から一覧へ遷移');
$I->click('求人原稿情報一覧へ');
$I->wait(3);
$I->amGoingTo('一覧から更新へ遷移');
$I->click('この条件で表示する');
$I->wait(5);
$page->clickActionColumn(1, 1);
$I->wait(5);

// document.input_form.targetとdocument.input_form.actionの書き換えをPhantomJsが認識していない模様
// 恐らくPhantomJsのバグ
//$I->amGoingTo('PCプレビューを見る');
//$I->click('PC版プレビュー');
//$I->wait(3);
//$I->switchToWindow('preview');
//$I->see('メールで転送する');
//$I->switchToWindow();

// PC版とスマホ版のwindowの名前が一緒なので検証できない
//$I->amGoingTo('スマホプレビューを見る');
//$I->click('スマホ版プレビュー');
//$I->wait(2);
//$I->switchToWindow('preview');
//$I->see('メールで転送する');

$I->click('この原稿をコピーして新規作成する');
$I->wait(5);
$I->see($createMenu->title, 'h1');

// 変更する
$page->submit('登録');

//----------------------
// ナンバー確認
//----------------------
$I->amGoingTo('完了画面から一覧へ遷移');
$I->click('求人原稿情報一覧へ');
$I->wait(3);
$I->amGoingTo('登録ナンバー検証');
$I->click('この条件で表示する');
$I->wait(5);
$I->expect('最初に登録した原稿のナンバー+2が登録されている');
$I->see($jobNo + 2, '//tbody/tr[1]/td[2]');

//----------------------
// todo next 完了画面リンク検証
//----------------------
