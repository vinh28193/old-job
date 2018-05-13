<?php
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\jobCsv\JobCsvToolsPage;
use app\models\manage\ClientMaster;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientCharge;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);
// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/job-csv/index');
// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// 登録に使うCSVファイル用にDBの値を変更
/** @var ClientMaster $client */
$client = ClientMaster::find()->one();
$client->client_no = 1;
$client->save(false);
/** @var ClientChargePlan $plan */
$plan = ClientChargePlan::find()->one();
$plan->client_charge_plan_no = 1;
$plan->save(false);
/** @var ClientCharge $charge */
$charge = ClientCharge::find()->one();
$charge->client_master_id = $client->id;
$charge->client_charge_plan_id = $plan->id;
$charge->save(false);
$I->wait(3);
// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('求人情報CSV一括登録変更のテスト');
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
// CSVアップロード→戻る
//----------------------
$I->attachFile('#csv-uploader', "job.csv");
$I->wait(3);
$I->see('以下の内容で登録してもよろしいですか？（まだ登録は完了していません。）');
//todo 表示項目・ページネーションの確認
$I->click('戻る');
$I->wait(3);
$I->see('ファイルをドラッグ&ドロップ…');

//----------------------
// CSVアップロード→ブラウザバック
//----------------------
$I->attachFile('#csv-uploader', "job.csv");
$I->wait(3);
$I->see('以下の内容で登録してもよろしいですか？（まだ登録は完了していません。）');
$I->moveBack();
$I->wait(3);
$I->see('ファイルをドラッグ&ドロップ…');

//todo ドラッグアンドドロップで確認画面へ遷移するかの確認

//----------------------
// CSVアップロード→登録
//----------------------
$I->attachFile('#csv-uploader', 'job.csv');
$I->wait(3);
$I->see('以下の内容で登録してもよろしいですか？（まだ登録は完了していません。）');
$I->click('登録する');
$I->wait(3);
$I->see('登録完了');
//todo 登録完了画面のリンク、登録件数の確認
//todo 登録した原稿が反映されているかの確認