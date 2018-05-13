<?php
use app\models\manage\ManageMenuMain;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Pref;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\jobCsv\JobCsvToolsPage;
use app\modules\manage\controllers\secure\CsvHelperController;
/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);
// fixture読み込み
(new \tests\codeception\fixtures\SearchkeyMasterFixture())->load();
// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/job-csv/index');
// 表示する都道府県のモデル取得
/** @var Pref[] $prefs */
$prefs = Pref::find()->joinWith('area')->where([Area::tableName() . '.valid_chk' => 1])->all();
// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

//検索キーコード一覧のプルダウンリスト
$pullDown = array_filter(array_map(function ($key) {
    if ($key == 'plan') {
        return Yii::t('app', '料金プラン');
    } else {
        /** @var \app\models\manage\SearchkeyMaster|null $model */
        $model = \yii\helpers\ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, $key);
        return ($model) ? $model->searchkey_name : null;
    }
}, CsvHelperController::HELPS));
// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('求人情報CSV一括登録変更表示確認のテスト');
//----------------------
// 運営元でログインしてindexへ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(2);
$page = JobCsvToolsPage::openBy($I);
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// CSVテンプレートのダウンロード
//----------------------
// ファイルダウンロードは自動テストできません
$I->amGoingTo('CSVダウンロードしてもエラーにならない');
$I->click('CSVテンプレートをダウンロード');
$I->wait(3);

//----------------------
// CSV入力方法
//----------------------
$I->amGoingTo('CSV入力方法');
$I->click('CSVの入力方法');
$I->wait(3);
$I->switchToWindow('help');
$I->wait(3);
$I->seeInTitle('求人情報登録用CSVの入力方法');
$I->see('求人情報登録用CSVの入力方法');
//todo 表示内容の確認

//----------------------
// 求人情報の管理へ
//----------------------
$I->amGoingTo('求人情報の管理へ');
$jobMenu = ManageMenuMain::findFromRoute('/manage/secure/job/list');
$page->openDefaultWindow();
$I->click('求人情報の管理へ');
$I->switchToWindow('job');
$I->wait(3);
$I->seeInTitle($jobMenu->title);
$I->see($jobMenu->title);

//----------------------
// 検索キーコード一覧
//----------------------
$I->amGoingTo('検索キーコード一覧');
$clientChargePlanLabel = Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label;
$title = Yii::t('app', '検索キーコード・' . $clientChargePlanLabel . 'の一覧');
$page->openDefaultWindow();
$I->click('検索キーコード一覧');
$I->switchToWindow('searchkey');
$I->wait(3);
$I->seeInTitle($title);
$I->see($title);

//----------------------
// 検索キーコード一覧の動作・詳細確認
//----------------------
foreach ($pullDown AS $k => $v){
    $I->amGoingTo('検索キーコード一覧の切り替え');
    $I->selectOption('#helperTypeDropDownList', $k);
    $I->wait(3);
    $I->seeInTitle($title);
    $I->see($title);
    //todo 各検索キーページの動作確認

    if ($k == CsvHelperController::DIST) {
        $I->amGoingTo('表示されている都道府県の表示');
        foreach ($prefs as $key => $pref) {
            $i = $key + 1;
            $I->see($pref->pref_name, "//div[@id='pref-accordion']/div[{$i}]/div[1]/h4");
        }
    }
}
