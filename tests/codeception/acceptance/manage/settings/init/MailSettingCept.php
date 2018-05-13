<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use app\models\manage\SendMailSet;
use app\models\MailSend;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\init\MailSettingPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/settings/sendmail/list');

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('メール設定のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = MailSettingPage::openBy($I);
$page->go($menu);

//----------------------
// キーワード
// todo next 動作検証
//----------------------
$I->wantTo('キーワードの入力ができる');
// キーワードの入力
$I->fillField('#sendmailsetsearch-searchtext', 'test');
// 入力の値が入っている
$I->seeInField('#sendmailsetsearch-searchtext', 'test');

//----------------------
// 対象者の入力
//----------------------
$I->wantTo('対象者の入力ができる');
$I->selectOption('#sendmailsetsearch-mail_to', '仕事転送');

//----------------------
// メール種別の入力
//----------------------
$I->wantTo('メール種別の入力ができる');
$I->selectOption('#sendmailsetsearch-mail_to', 0);

//----------------------
// 検索する
// todo next 動作検証
//----------------------
$I->click('この条件で表示する');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// クリアする
// todo next 動作検証
////----------------------
$I->click('クリア');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// ソートする
// todo next 動作検証
//----------------------
$I->amGoingTo('ソートする');
$I->click('//thead/tr/th[2]');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// 内容変更
//----------------------
$I->amGoingTo('内容変更');
$page->openModal(1);
// todo next 詳細検証実装
$page->submitModal();

//----------------------
// 入力項目 通知先メール
//----------------------
$just254Email = str_repeat('a', 64) . "@" . str_repeat('b', 100) . "." . str_repeat('c', 254 - 64 - 1 - 100 - 1);
$mailTypeATA = MailSend::TYPE_APPLY_TO_ADMIN;
$mailTypeIN = MailSend::TYPE_INQUILY_NOTIFICATION;
$mailTypeJR = MailSend::TYPE_JOB_REVIEW;
$I->wantTo("通知先メール項目はメールタイプIDが{$mailTypeATA}と{$mailTypeIN}と{$mailTypeJR}のメール設定変更画面のみ表示される");
$I->amGoingTo('表示されいてるメールタイプ数を取得する');
$rowCount = $I->executeJS("return $('tr[data-key]').length");

for ($i = 0; $i < $rowCount; $i++) {
    $rowIndex = $i + 1;
    $I->amGoingTo("{$rowIndex}行目のメールタイプIDを取得する");
    $sendMailSetId = $I->executeJS("return $('tr[data-key]:eq({$i})').data('key')");
    $mailTypeId = SendMailSet::findOne($sendMailSetId)->mail_type_id;

    $I->amGoingTo("メールタイプID={$mailTypeId}のメール設定変更を開く");
    $page->openModal($rowIndex);
    $I->wait(2);

    if ($mailTypeId == $mailTypeATA) {
        $I->amGoingTo("メールタイプIDが{$mailTypeATA}のときは通知先メール項目がある");
        $I->seeElement('#form-notification_address-tr');
        $I->amGoingTo("予め保存されているデータを控える");
        $preStoredMail = $I->grabValueFrom('#sendmailset-notification_address');
        $I->amGoingTo('通知先メール設定は任意項目である');
        $I->fillField('通知先メールアドレス', '');
        $I->wait(1);
        $I->cantSee('通知先メールアドレスは必須項目です。');
        $I->amGoingTo('通知先メール設定が空でも保存できる');
        $page->submitModal();
        $page->openModal($rowIndex);
        $I->wait(1);
        $I->amGoingTo('通知先メールアドレスが空になっている');
        $I->canSeeInField('通知先メールアドレス', '');
        $page->checkRuleEmail('通知先メールアドレス');
        $page->checkRuleOver255Email('通知先メールアドレス');
        $I->amGoingTo('通知先メールアドレスは最大254文字保存できる');
        $I->fillField('通知先メールアドレス', $just254Email);
        $page->submitModal();
        $page->openModal($rowIndex);
        $I->wait(1);
        $I->canSeeInField('通知先メールアドレス', $just254Email);
        $I->amGoingTo("予め保存されていたデータを戻す");
        $I->fillField('通知先メールアドレス', $preStoredMail);
        $page->submitModal();
        $page->openModal($rowIndex);
        $I->wait(1);
        $I->canSeeInField('通知先メールアドレス', $preStoredMail);
    } elseif ($mailTypeId == $mailTypeIN) {
        $I->amGoingTo("メールタイプIDが{$mailTypeIN}のときは通知先メール項目がある");
        $I->seeElement('#form-notification_address-tr');
        $I->amGoingTo("予め保存されているデータを控える");
        $preStoredMail = $I->grabValueFrom('#sendmailset-notification_address');
        $I->amGoingTo('通知先メール設定は必須項目である');
        $I->fillField('通知先メールアドレス', '');
        $I->wait(1);
        $I->see('通知先メールアドレスは必須項目です。');
        $page->checkRuleEmail('通知先メールアドレス');
        $page->checkRuleOver255Email('通知先メールアドレス');
        $I->amGoingTo('通知先メールアドレスは最大254文字保存できる');
        $I->fillField('通知先メールアドレス', $just254Email);
        $page->submitModal();
        $page->openModal($rowIndex);
        $I->wait(1);
        $I->canSeeInField('通知先メールアドレス', $just254Email);
        $I->amGoingTo("予め保存されていたデータを戻す");
        $I->fillField('通知先メールアドレス', $preStoredMail);
        $page->submitModal();
        $page->openModal($rowIndex);
        $I->wait(1);
        $I->canSeeInField('通知先メールアドレス', $preStoredMail);
    } elseif ($mailTypeId == $mailTypeJR) {
        $I->amGoingTo("メールタイプIDが{$mailTypeJR}のときは通知先メール項目がある");
        $I->seeElement('#form-notification_address-tr');
        $I->amGoingTo("予め保存されているデータを控える");
        $preStoredMail = $I->grabValueFrom('#sendmailset-notification_address');
        $I->amGoingTo('通知先メール設定は任意項目である');
        $I->fillField('通知先メールアドレス', '');
        $I->wait(1);
        $I->see('入力が無い場合、運営元に審査メールが送信されません。');
        $I->cantSee('通知先メールアドレスは必須項目です。');
        $I->amGoingTo('通知先メール設定が空でも保存できる');
        $page->submitModal();
        $page->openModal($rowIndex);
        $I->wait(1);
        $I->amGoingTo('通知先メールアドレスが空になっている');
        $I->canSeeInField('通知先メールアドレス', '');
        $page->checkRuleEmail('通知先メールアドレス');
        $page->checkRuleOver255Email('通知先メールアドレス');
        $I->amGoingTo('通知先メールアドレスは最大254文字保存できる');
        $I->fillField('通知先メールアドレス', $just254Email);
        $page->submitModal();
        $page->openModal($rowIndex);
        $I->wait(1);
        $I->canSeeInField('通知先メールアドレス', $just254Email);
        $I->amGoingTo("予め保存されていたデータを戻す");
        $I->fillField('通知先メールアドレス', $preStoredMail);
        $page->submitModal();
        $page->openModal($rowIndex);
        $I->wait(1);
    } else {
        $I->amGoingTo("通知先メール項目はない");
        $I->dontSeeElement('#form-notification_address-tr');
        $I->amGoingTo("通知先メールアドレスなしで保存ができる");
        $page->submitModal();
        $page->openModal($rowIndex);
        $I->wait(1);
    }

    $page->closeModal();
    $I->wait(2);
}