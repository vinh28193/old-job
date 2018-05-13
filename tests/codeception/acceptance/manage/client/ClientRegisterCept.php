<?php

use app\models\manage\ClientChargePlan;
use app\models\manage\CorpMaster;
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/client/list');

// 登録する会社名
$clientName = date('Ymdhis') . '株式会社';

// モデル準備
$corp = CorpMaster::findOne(['valid_chk' => 1]);
$plan = ClientChargePlan::findOne(['valid_chk' => 1]);

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('掲載企業登録変更のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);


$I->amGoingTo('企業掲載一覧ページへ移動');
$I->click('//*[@id="menu-cate-3"]/a');
$I->wait(3);
$I->click('//*[@id="navi-item04"]');
$I->wait(3);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');


$I->amGoingTo('掲載企業を登録する');
$I->click('掲載企業を登録する');
$I->wait(2);
$I->see('載企業の登録', 'h1');

// 代理店
$I->click('//*[@id="select2-clientmaster-corp_master_id-container"]');
$I->wait(3);
$I->fillField('//span[@class="select2-search select2-search--dropdown"]/input', $corp->corp_name);
$I->wait(3);
$I->click('//*[@id="select2-clientmaster-corp_master_id-results"]/li[1]');

// 会社名
$I->fillField('//*[@id="clientmaster-client_name"]', $clientName);

// 掲載タイプ
$I->checkOption('//*[@id="clientcharge-' . $plan->id . '-client_charge_plan_id"]');
$I->wait(3);
$I->selectOption('//*[@id="clientcharge-' . $plan->id . '-limittype"]', 1);
$I->wait(3);
$I->fillField('//*[@id="clientcharge-' . $plan->id . '-limit_num"]', 100);

// オプション7
$I->fillField('//*[@id="clientmaster-option106"]', 'オプション7');

// 取引状態
$I->selectOption('//*[@id="clientmaster-valid_chk"]/label/input', 1);

// submit
$I->click('complete');
$I->wait(3);
$I->click('//div[@class="modal-footer"]/button[@class="btn btn-primary"]');
$I->wait(3);
$I->see('掲載企業情報-完了', '//h1[@class="heading"]');
$I->see('登録完了', 'h1');


$I->amGoingTo('企業掲載一覧ページへ移動');
$I->click('掲載企業情報一覧へ');
$I->wait(3);
$I->see($menu->title, 'h1');
$I->dontSee($clientName);


$I->amGoingTo('キーワードで検索');
$I->fillField('//*[@id="clientmastersearch-searchtext"]', $clientName);
$I->click('この条件で表示する');
$I->wait(3);
$I->see($menu->title, 'h1');
$I->see($clientName);


$I->amGoingTo('掲載企業を編集する');
$I->click('//*[@id="grid_id"]/div/table/tbody/tr/td/a[@title="変更"]');
$I->wait(3);
$I->see('掲載企業の編集', 'h1');
$I->seeInField('//*[@id="clientmaster-client_name"]', $clientName);

// 代理店
//$I->click('//*[@id="select2-clientmaster-corp_master_id-container"]');
//$I->wait(3);
//$I->fillField('//span[@class="select2-search select2-search--dropdown"]/input', '株式');
//$I->wait(3);
//$I->click('//*[@id="select2-clientmaster-corp_master_id-results"]/li[1]');

// 会社名
$I->fillField('//*[@id="clientmaster-client_name"]', $clientName);

// 掲載タイプ
//$I->checkOption('//*[@id="clientcharge-1-client_charge_plan_id"]');
//$I->checkOption('//*[@id="clientcharge-6-client_charge_plan_id"]');
//$I->checkOption('//*[@id="clientcharge-3-client_charge_plan_id"]');

// オプション7
$I->fillField('//*[@id="clientmaster-option106"]', "オプション\nオプション");

// 取引状態
$I->selectOption('//*[@id="clientmaster-valid_chk"]/label/input', 1);

// submit
$I->click('complete');
$I->wait(3);
$I->click('//div[@class="modal-footer"]/button[@class="btn btn-primary"]');
$I->wait(3);
$I->see('掲載企業情報-完了', '//h1[@class="heading"]');
$I->see('変更完了', 'h1');

