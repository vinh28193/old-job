<?php
use app\models\manage\AdminMaster;
use app\models\manage\JobColumnSet;
use app\models\manage\JobMaster;
use app\models\manage\JobReviewStatus;
use app\models\manage\ManageMenuMain;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Pref;
use app\modules\manage\models\Manager;
use proseeds\models\Tenant;
use tests\codeception\_pages\manage\job\JobReviewPage;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\fixtures\JobColumnSetFixture;
use tests\codeception\fixtures\JobColumnSubsetFixture;
use app\models\manage\CorpMaster;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// メニュー情報取得
$createMenu = ManageMenuMain::findFromRoute('/manage/secure/job/create');
$updateMenu = ManageMenuMain::findFromRoute('/manage/secure/job/update');

// 管理者情報
$admins = [
    [
        'type' => '掲載企業',
        'loginid' => 'admin03',
        'password' => 'admin03',
        'admin_id' => 3,
        'role' => Manager::CLIENT_ADMIN,
    ],
    [
        'type' => '代理店',
        'loginid' => 'admin02',
        'password' => 'admin02',
        'admin_id' => 2,
        'role' => Manager::CORP_ADMIN,
    ],
    [
        'type' => '運営元',
        'loginid' => 'admin01',
        'password' => 'admin01',
        'has_setting_menu' => true,
        'admin_id' => 1,
        'role' => Manager::OWNER_ADMIN,
    ],
];


// 必須項目を無くす
// todo next corp_column_setのレコードの整備・連携
(new JobColumnSetFixture())->unload();
(new JobColumnSetFixture())->load();
(new JobColumnSubsetFixture())->unload();
(new JobColumnSubsetFixture())->load();
JobColumnSet::updateAll(['is_must' => JobColumnSet::NOT_MUST], ['is_must' => JobColumnSet::MUST]);

// disp_type.disp_type_no=3の代理店、掲載企業、プランの組み合わせを準備
$array = JobReviewPage::initPlan();
$corp = $array['corp'];
$client = $array['client'];
$charge = $array['charge'];

// 表示する都道府県のモデル取得
/** @var Pref[] $prefs */
$prefs = Pref::find()->joinWith('area')->where([Area::tableName() . '.valid_chk' => 1])->all();

// 審査機能ONにしておく。
Tenant::updateAll(['review_use' => 1]);
// admin01、02が所属する代理店の代理店審査フラグを「あり」にしておく。
$corpId = AdminMaster::find()->where(['login_id' => 'admin02'])->one()->corp_master_id;
CorpMaster::updateAll(['corp_review_flg' => 1],['id' => $corpId]);


// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('求人審査のテスト');

$I->expect('各権限での新規登録確認');
foreach ($admins as $admin) {
    Yii::$app->user->identity = Manager::find()->where(['admin_no' => $admin['admin_id']])->one();
    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage = ManageLoginPage::openBy($I);
    $loginPage->login($admin['loginid'], $admin['password']);
    $I->wait(3);
    $page = JobReviewPage::openBy($I);
//----------------------
// 新規登録画面
//----------------------
    $page->goCreate('求人原稿を登録する', $createMenu);
    $I->amGoingTo('クリアボタン押下');
    $I->click('クリア');
    $I->wait(1);
    $I->see($createMenu->title, 'h1');
//----------------------
// 審査履歴確認
//----------------------
    $model = new JobMaster();
    $page->checkReviewHistory($model, false);
//----------------------
// ボタン確認(一時保存ボタンとかボタン名とか)
//----------------------
    $page->checkButton($admin['role'], '登録');
//----------------------
// 項目入力
//----------------------
    $I->amGoingTo('各項目を最低限入力');
    $page->minimumInput($admin['role'], $corp, $client, $charge, $prefs[0]);
//----------------------
// 登録（一時保存）
//----------------------
    if ($admin['role'] == Manager::OWNER_ADMIN) {
        $page->submit('登録', $model, false);
    } else {
        $page->submit('登録', $model, true);
    }

    $I->amGoingTo('完了画面から一覧へ遷移');
    $I->click('求人原稿情報一覧へ');
    $I->wait(3);
    $I->click('この条件で表示する');
    $I->wait(3);
//----------------------
// 登録検証
//----------------------
    $I->amGoingTo('一覧表示時のステータス検証');
    $model = new JobReviewStatus();
    if ($admin['role'] == Manager::OWNER_ADMIN) {
        $model->id = JobReviewStatus::STEP_REVIEW_OK;
    } else {
        $model->id = JobReviewStatus::STEP_JOB_EDIT;
    }
    $I->see($model->name, '//div[@id="grid_id"]//table/tbody/tr[1]/td[10]');

    $I->amGoingTo('ログアウト');
    $I->click('ホーム');
    $I->wait(2);
    $loginPage->logoutOnHome();
}


$I->wantTo('掲載企業 審査依頼 => 代理店OK => 運営元OK');
$nameCheck = true;
$errCheck = true;
foreach ($admins as $admin) {
    Yii::$app->user->identity = Manager::find()->where(['admin_no' => $admin['admin_id']])->one();
    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage = ManageLoginPage::openBy($I);
    $loginPage->login($admin['loginid'], $admin['password']);
    $I->wait(3);
    $page = JobReviewPage::openBy($I);
    $I->wait(2);
//----------------------
// 一覧で検索
//----------------------
    $I->amGoingTo('全検索');
    $I->click('この条件で表示する');
    $I->wait(2);
//----------------------
// 更新画面
//----------------------
    $jobId = $page->grabTableRowId(1);
    $model = JobMaster::findOne($jobId);
    $page->clickActionColumn(1, 1);
    $I->wait(3);
//----------------------
// 審査履歴確認
//----------------------
    $page->checkReviewHistory($model, false);
//----------------------
// 更新・審査依頼 or 審査
//----------------------
    if ($admin['role'] === Manager::CLIENT_ADMIN) {
        // 審査依頼
        $page->submit('変更', $model, false);
        $page->reviewComment('審査依頼を出します。');
        $I->click('審査依頼する');
        $I->wait(3);
        $I->click('OK');
        $I->wait(3);
        $reqFlg = true;
    } else {
        // 審査
        $I->click('審査する');
        $I->wait(7);
        $page->review($model, 'OK', "{$admin['type']}審査OKです。", $nameCheck, $errCheck);
        $nameCheck = false;
        $errCheck = false;
        $reqFlg = false;
    }
//----------------------
// 完了画面確認
//----------------------
    $page->reviewComplete($reqFlg);
//----------------------
// 一覧へ遷移
//----------------------
    $I->amGoingTo('完了画面から一覧へ遷移');
    $I->click('求人原稿情報一覧へ');
    $I->wait(3);
    $I->click('この条件で表示する');
    $I->wait(3);
//----------------------
// 審査検証
//----------------------
    $I->amGoingTo('一覧表示時のステータス検証');
    $model = JobMaster::findOne($jobId);
    $I->see($model->jobReviewStatus->name, '//div[@id="grid_id"]//table/tbody/tr[1]/td[10]');

    $I->amGoingTo('ログアウト');
    $I->click('ホーム');
    $I->wait(3);
    $loginPage->logoutOnHome();
}


$I->wantTo('掲載企業 審査依頼 => 代理店NG => 掲載企業 審査依頼 => 代理店OK => 運営元NG => 代理店 審査依頼 => 運営元OK');
$admins = [
    [
        'type' => '掲載企業',
        'loginid' => 'admin03',
        'password' => 'admin03',
        'admin_id' => 3,
        'role' => Manager::CLIENT_ADMIN,
        'review' => 'request',
    ],
    [
        'type' => '代理店',
        'loginid' => 'admin02',
        'password' => 'admin02',
        'admin_id' => 2,
        'role' => Manager::CORP_ADMIN,
        'review' => 'NG',
    ],
    [
        'type' => '掲載企業',
        'loginid' => 'admin03',
        'password' => 'admin03',
        'admin_id' => 3,
        'role' => Manager::CLIENT_ADMIN,
        'review' => 'request',
    ],
    [
        'type' => '代理店',
        'loginid' => 'admin02',
        'password' => 'admin02',
        'admin_id' => 2,
        'role' => Manager::CORP_ADMIN,
        'review' => 'OK',
    ],
    [
        'type' => '運営元',
        'loginid' => 'admin01',
        'password' => 'admin01',
        'has_setting_menu' => true,
        'admin_id' => 1,
        'role' => Manager::OWNER_ADMIN,
        'review' => 'NG',
    ],
    [
        'type' => '代理店',
        'loginid' => 'admin02',
        'password' => 'admin02',
        'admin_id' => 2,
        'role' => Manager::CORP_ADMIN,
        'review' => 'request',
    ],
    [
        'type' => '運営元',
        'loginid' => 'admin01',
        'password' => 'admin01',
        'has_setting_menu' => true,
        'admin_id' => 1,
        'role' => Manager::OWNER_ADMIN,
        'review' => 'OK',
    ],
];
$nameCheck = false;
$errCheck = false;
foreach ($admins as $admin) {
    Yii::$app->user->identity = Manager::find()->where(['admin_no' => $admin['admin_id']])->one();
    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage = ManageLoginPage::openBy($I);
    $loginPage->login($admin['loginid'], $admin['password']);
    $I->wait(5);
    $page = JobReviewPage::openBy($I);
    $I->wait(2);
//----------------------
// 一覧で検索
//----------------------
    $I->amGoingTo('全検索');
    $I->click('この条件で表示する');
    $I->wait(2);
//----------------------
// 更新画面(審査依頼時のみ)
//----------------------
    $jobId = $page->grabTableRowId(1);
    $model = JobMaster::findOne($jobId);
    if ($admin['review'] === 'request') {
        $page->clickActionColumn(1, 1);
        $I->wait(3);
        // 審査履歴確認
        $page->checkReviewHistory($model, false);
    }
//----------------------
// 更新・審査依頼 or 審査
//----------------------
    if ($admin['review'] === 'request') {
        // 審査依頼
        $page->submit('変更', $model, false);
        $page->reviewComment("【{$admin['type']}】審査依頼を出します。");
        $I->click('審査依頼する');
        $I->wait(3);
        $I->click('OK');
        $I->wait(3);
        $reqFlg = true;
    } else {
        // 審査(※一覧から)
        $I->click('//div[@id="grid_id"]//table/tbody/tr[1]/td[10]/a');
        $I->wait(8);
        // 審査履歴確認
        $page->checkReviewHistory($model, true);
        $page->review($model, $admin['review'], "{$admin['type']}審査{$admin['review']}です。", $nameCheck, $errCheck);
        $nameCheck = false;
        $errCheck = false;
        $reqFlg = false;
    }
//----------------------
// 完了画面確認
//----------------------
    $page->reviewComplete($reqFlg);
//----------------------
// 一覧へ遷移
//----------------------
    $I->amGoingTo('完了画面から一覧へ遷移');
    $I->click('求人原稿情報一覧へ');
    $I->wait(3);
    $I->click('この条件で表示する');
    $I->wait(3);
//----------------------
// 審査検証
//----------------------
    $I->amGoingTo('一覧表示時のステータス検証');
    $model = JobMaster::findOne($jobId);
    $I->see($model->jobReviewStatus->name, '//div[@id="grid_id"]//table/tbody/tr[1]/td[10]');

    $I->amGoingTo('ログアウト');
    $I->click('ホーム');
    $loginPage->logoutOnHome();
}


$I->wantTo('【代理店審査なし】掲載企業 審査依頼 => 運営元OK');
CorpMaster::updateAll(['corp_review_flg' => 0],['id' => $corpId]);
$admins = [
    [
        'type' => '掲載企業',
        'loginid' => 'admin03',
        'password' => 'admin03',
        'admin_id' => 3,
        'role' => Manager::CLIENT_ADMIN,
        'review' => 'request',
    ],
    [
        'type' => '運営元',
        'loginid' => 'admin01',
        'password' => 'admin01',
        'has_setting_menu' => true,
        'admin_id' => 1,
        'role' => Manager::OWNER_ADMIN,
        'review' => 'OK',
    ],
];
$nameCheck = false;
$errCheck = false;
foreach ($admins as $admin) {
    Yii::$app->user->identity = Manager::find()->where(['admin_no' => $admin['admin_id']])->one();
    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage = ManageLoginPage::openBy($I);
    $loginPage->login($admin['loginid'], $admin['password']);
    $I->wait(5);
    $page = JobReviewPage::openBy($I);
    $I->wait(2);
    //----------------------
    // 一覧で検索
    //----------------------
    $I->amGoingTo('全検索');
    $I->click('この条件で表示する');
    $I->wait(2);
    //----------------------
    // 更新画面(審査依頼時のみ)
    //----------------------
    $jobId = $page->grabTableRowId(1);
    $model = JobMaster::findOne($jobId);
    if ($admin['review'] === 'request') {
        $page->clickActionColumn(1, 1);
        $I->wait(3);
        // 審査履歴確認
        $page->checkReviewHistory($model, false);
    }
    //----------------------
    // 更新・審査依頼 or 審査
    //----------------------
    if ($admin['review'] === 'request') {
        // 審査依頼
        $page->submit('変更', $model, false);
        $page->reviewComment("【{$admin['type']}】審査依頼を出します。");
        $I->click('審査依頼する');
        $I->wait(3);
        $I->click('OK');
        $I->wait(3);
        $reqFlg = true;
    } else {
        // 審査(※一覧から)
        $I->click('//div[@id="grid_id"]//table/tbody/tr[1]/td[10]/a');
        $I->wait(8);
        // 審査履歴確認
        $page->checkReviewHistory($model, true);
        $page->review($model, $admin['review'], "{$admin['type']}審査{$admin['review']}です。", $nameCheck, $errCheck);
        $nameCheck = false;
        $errCheck = false;
        $reqFlg = false;
    }
    //----------------------
    // 完了画面確認
    //----------------------
    $page->reviewComplete($reqFlg);
    //----------------------
    // 一覧へ遷移
    //----------------------
    $I->amGoingTo('完了画面から一覧へ遷移');
    $I->click('求人原稿情報一覧へ');
    $I->wait(3);
    $I->click('この条件で表示する');
    $I->wait(3);
    //----------------------
    // 審査検証
    //----------------------
    $I->amGoingTo('一覧表示時のステータス検証');
    $model = JobMaster::findOne($jobId);
    $I->see($model->jobReviewStatus->name, '//div[@id="grid_id"]//table/tbody/tr[1]/td[10]');

    $I->amGoingTo('ログアウト');
    $I->click('ホーム');
    $loginPage->logoutOnHome();
}

//----------------------
// 特殊エラーチェック
// ※なぜかモーダル上のエラーメッセージをphantomJS上から確認できないので割愛。手動テストで確認する。
//----------------------
