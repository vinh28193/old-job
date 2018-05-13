<?php
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use app\models\manage\JobColumnSet;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\option\OptionPage;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// 項目のID
const JOB_NO_ROW = 1;
const JOB_SEARCH_NUMBER_ROW = 2;
const CORPLABEL_ROW = 3;
const CLIENT_CHARGE_PLAN_ROW = 4;
const CLIENT_MASTER_ROW = 5;
const DISP_START_DATE_ROW = 6;
const DISP_END_DATE_ROW = 7;
const CORP_NAME_DISP_ROW = 8;
const JOB_PR_ROW = 9;
const MAIN_COPY_ROW = 10;
const JOB_PR2_ROW = 11;
const MAIN_COPY2_ROW = 12;
const JOB_TYPE_TEXT_ROW = 13;
const WORK_PLACE_ROW = 14;
const STATION_ROW = 15;
const MAP_URL_ROW = 16;
const WAGE_TEXT_ROW = 17;
const TRANSPORT_ROW = 18;
const WORK_PERIOD_ROW = 19;
const WORK_TIME_TEXT_ROW = 20;
const REQUIREMENT_ROW = 21;
const CONDITIONS_ROW = 22;
const HOLIDAYS_ROW = 23;
const JOB_COMMENT_ROW = 24;
const APPLICATION_ROW = 25;
const APPLICATION_TEL_1_ROW = 26;
const APPLICATION_TEL_2_ROW = 27;
const APPLICATION_PLACE_ROW = 28;
const APPLICATION_STAFF_NAME_ROW = 29;
const APPLICATION_MAIL_ROW = 30;
const AGENT_NAME_ROW = 31;
const MAIL_BODY_ROW = 32;
const OPTION100_ROW = 33;
const OPTION101_ROW = 34;
const OPTION102_ROW = 35;
const OPTION103_ROW = 36;
const OPTION104_ROW = 37;
const OPTION105_ROW = 38;
const OPTION106_ROW = 39;
const OPTION107_ROW = 40;
const OPTION108_ROW = 41;
const OPTION109_ROW = 42;
// 汎用項目のタイトル一覧
$generalArray = [
    ['id' => CORP_NAME_DISP_ROW, 'title' => '施設名'],
    ['id' => JOB_PR_ROW, 'title' => 'メインキャッチ'],
    ['id' => MAIN_COPY_ROW, 'title' => 'コメント'],
    ['id' => JOB_PR2_ROW, 'title' => 'メインキャッチ２'],
    ['id' => MAIN_COPY2_ROW, 'title' => 'コメント２'],
    ['id' => JOB_TYPE_TEXT_ROW, 'title' => '施設種別'],
    ['id' => WORK_PLACE_ROW, 'title' => '所在地'],
    ['id' => STATION_ROW, 'title' => '最寄駅'],
    ['id' => WAGE_TEXT_ROW, 'title' => '交通'],
    ['id' => TRANSPORT_ROW, 'title' => '入居金と生活費（円）'],
    ['id' => WORK_PERIOD_ROW, 'title' => '開設年月日'],
    ['id' => WORK_TIME_TEXT_ROW, 'title' => '定員'],
    ['id' => REQUIREMENT_ROW, 'title' => '居室面積'],
    ['id' => CONDITIONS_ROW, 'title' => '居室設備'],
    ['id' => HOLIDAYS_ROW, 'title' => '共用設備'],
    ['id' => JOB_COMMENT_ROW, 'title' => 'ポイント'],
    ['id' => APPLICATION_ROW, 'title' => '入居条件'],
    ['id' => APPLICATION_PLACE_ROW, 'title' => '面接地'],
    ['id' => APPLICATION_STAFF_NAME_ROW, 'title' => '受付担当者'],
];

// オプション項目のタイトル一覧
$optionArray = [
    ['id' => OPTION102_ROW, 'title' => 'オプション102'],
    ['id' => OPTION103_ROW, 'title' => 'オプション103'],
    ['id' => OPTION104_ROW, 'title' => 'オプション104'],
    ['id' => OPTION105_ROW, 'title' => 'オプション105'],
    ['id' => OPTION106_ROW, 'title' => 'オプション106'],
    ['id' => OPTION107_ROW, 'title' => 'オプション107'],
    ['id' => OPTION108_ROW, 'title' => 'オプション108'],
    ['id' => OPTION109_ROW, 'title' => 'オプション109'],
];

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/option-job/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('求人原稿項目設定画面のテスト');
//----------------------
// 運営元でログインして項目設定画面へ遷移
//----------------------
$I->amGoingTo('運営元でサイト設定画面へアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = OptionPage::openBy($I);
$page->go($menu);

//----------------------
// 仕事ID
//----------------------
$I->amGoingTo('仕事ID検証');
$page->openModal(JOB_NO_ROW);
$page->checkInputOfExplain(true);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 代理店名
//----------------------
$I->amGoingTo('代理店検証');
$page->openModal(CORPLABEL_ROW);
$page->checkInputOfExplain(true);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 料金プラン
//----------------------
$I->amGoingTo('料金プラン検証');
$page->openModal(CLIENT_CHARGE_PLAN_ROW);
$page->checkInputOfExplain(true);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 掲載企業名
//----------------------
$I->amGoingTo('掲載企業名検証');
$page->openModal(CLIENT_MASTER_ROW);
$page->checkInputOfExplain(true);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 掲載開始日
//----------------------
$I->amGoingTo('掲載開始日検証');
$page->openModal(DISP_START_DATE_ROW);
$page->checkInputOfExplain(true);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 掲載終了日
//----------------------
$I->amGoingTo('掲載終了日検証');
$page->openModal(DISP_END_DATE_ROW);
$page->checkInputOfExplain(true);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 勤務地MAP
//----------------------
$I->amGoingTo('勤務地MAP検証');
$page->openModal(MAP_URL_ROW);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, JobColumnSet::MAX_LENGTH_EXPLAIN);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 電話番号
//----------------------
$I->amGoingTo('電話番号検証');
$page->openModal(APPLICATION_TEL_1_ROW);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, JobColumnSet::MAX_LENGTH_EXPLAIN);
// todo 詳細検証実装
$page->submitModal();

$I->amGoingTo('電話番号検証');
$page->openModal(APPLICATION_TEL_2_ROW);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, JobColumnSet::MAX_LENGTH_EXPLAIN);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 応募先メールアドレス
//----------------------
$I->amGoingTo('応募先メールアドレス検証');
$page->openModal(APPLICATION_MAIL_ROW);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, JobColumnSet::MAX_LENGTH_EXPLAIN);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 担当者
//----------------------
$I->amGoingTo('担当者検証');
$page->openModal(AGENT_NAME_ROW);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, JobColumnSet::MAX_LENGTH_EXPLAIN);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// 自動返信メール文面
//----------------------
$I->amGoingTo('自動返信メール文面検証');
$page->openModal(AGENT_NAME_ROW);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, JobColumnSet::MAX_LENGTH_EXPLAIN);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// お仕事No
//----------------------
$I->amGoingTo('お仕事No検証');
$page->openModal(JOB_SEARCH_NUMBER_ROW);
$page->chkInputSpecialCharacter();
$page->checkInputOfExplain(true, JobColumnSet::MAX_LENGTH_EXPLAIN);

//DBから初期状態で「使用する」「使用しない」どちらか判定
$validChkArray = JobColumnSet::find()->select('valid_chk')->where(['column_name' => 'job_search_number'])->column();
$I->seeCheckboxIsChecked("//input[@name='JobColumnSet[valid_chk]'][@value='$validChkArray[0]']");
$I->amGoingTo('変更可能項目を変更');
$value = $validChkArray[0] == JobColumnSet::VALID ? JobColumnSet::INVALID : JobColumnSet::VALID;
$I->selectOption("//input[@name='JobColumnSet[valid_chk]']", $value);
$page->submitModal();
$I->amGoingTo('変更が反映されているかを確認');
$page->openModal(JOB_SEARCH_NUMBER_ROW);//もう一回開く
$I->seeCheckboxIsChecked("//input[@name='JobColumnSet[valid_chk]'][@value='$value']");
$page->submitModal();

//----------------------
// お仕事No以外の汎用項目の項目説明文表示・非表示、最大文字数を検証
//----------------------
foreach ($generalArray as $data) {
    $id = $data['id'];
    $title = $data['title'];
    $I->amGoingTo($title . '検証');
    $page->openModal($id);
    $page->checkInputOfExplain(true);

    $page->submitModal();
};

//----------------------
// オプション(option100)
// 入力方法ごとに項目説明文の入力検証を行う
//----------------------
$I->amGoingTo('オプション100検証');
$page->openModal(OPTION100_ROW);
$page->chkInputSpecialCharacter();
$page->optionColumnExplain(JobColumnSet::MAX_LENGTH_EXPLAIN);
// todo 詳細検証実装
$page->submitModal();

// ----------------------
// オプション項目(option101)
// 項目説明文にエラーが出ている状態で
// 項目説明文の入力できない入力方法に変更した際に
// 問題なく登録できることを確認
// ----------------------
$I->amGoingTo('オプション項目検証');
$page->openModal(OPTION101_ROW);
$page->optionChangeDataType(JobColumnSet::MAX_LENGTH_EXPLAIN);
// todo 詳細検証実装
$page->submitModal();

//----------------------
// オプション項目102~109
// 簡易検証
//----------------------
foreach ($optionArray as $data) {
    $id = $data['id'];
    $title = $data['title'];
    $I->amGoingTo($title . '検証');
    $page->openModal($id);

    // column_explain：data_typeによる表示・非表示の切り替えを検証
    $page->optionColumnExplain();

    $page->submitModal();
};
