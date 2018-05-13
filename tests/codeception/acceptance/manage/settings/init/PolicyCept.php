<?php

use app\models\JobMasterDisp;
use app\models\manage\ManageMenuMain;
use app\models\manage\Policy;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */
/** @var JobMasterDisp $job */
$job = JobMasterDisp::find()->active()->andWhere(['not', ['job_master.application_mail' => null]])->one();
$menu = ManageMenuMain::findFromRoute('/manage/secure/settings/policy/list');
$policies = Policy::find()->all();

$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

$I->amGoingTo('代理店でアクセス');
$loginPage->login('admin02', 'admin02');
$I->wait(1);

$I->wantTo('規約一覧画面へ遷移する');
$I->amOnPage('manage/secure/settings/policy/list');
$I->wait(1);
$I->see('アクセス権限がありません', 'h1');

$I->amOnPage('manage/logout');
$I->wait(1);

$I->amGoingTo('掲載企業でアクセス');
$loginPage->login('admin03', 'admin03');
$I->wait(1);

$I->wantTo('規約一覧画面へ遷移する');
$I->amOnPage('manage/secure/settings/policy/list');
$I->wait(1);
$I->see('アクセス権限がありません', 'h1');

$I->amOnPage('manage/logout');
$I->wait(1);

$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);

$I->wantTo('規約一覧画面へ遷移する');
$I->amOnPage('manage/secure/settings/policy/list');
$I->wait(1);
$I->see($menu->title . '設定', 'h1');

$I->wantTo('規約編集画面へ遷移する');
$policyName = $I->grabTextFrom("tr[data-key=\"{$policies[0]->id}\"] td:nth-of-type(2)");
$validChk = $I->grabTextFrom("tr[data-key=\"{$policies[0]->id}\"] td:nth-of-type(5)");
$I->click("tr[data-key=\"{$policies[0]->id}\"] a[title=\"変更\"]");
$I->wait(1);
$I->see($menu->title . 'の編集', 'h1');
$I->seeInField('#policy-policy_name', $policyName);
$I->see($validChk, '#policy_form-valid_chk-tr > td');

$I->wantTo('規約名の必須チェック');
$I->fillField('#policy-policy_name', '');
$I->click('変更');
$I->wait(1);
$I->see('規約名は必須項目です。');

$I->wantTo('規約名の最大文字数チェック');
$I->fillField('#policy-policy_name', str_repeat('a', 31));
$I->click('変更');
$I->wait(1);
$I->see('規約名は30文字以下で入力してください。');

$I->wantTo('規約名の登録チェック');
$policyName = time() . '応募規約';
$I->fillField('#policy-policy_name', time() . '応募規約');
$I->click('変更');
$I->wait(1);
$I->click('OK');
$I->wait(1);
$I->see('規約の登録が完了しました');
$I->click('規約設定一覧画面へ');
$I->see($policyName, "tr[data-key=\"{$policies[0]->id}\"] td:nth-of-type(2)");

$I->amOnPage('/policy?policy_no=1');
$I->wait(1);
$I->see($policyName, 'h1');

$I->amOnPage('/apply/' . $job->job_no);
$I->wait(1);
$I->see($policyName, '#policy');
$I->see($policyName . 'に同意のうえ応募する', 'button[type="submit"]');