<?php

/* @var $scenario Codeception\Scenario */

use app\models\JobMasterDisp;
use tests\codeception\_pages\InquiryPage;

$I = new AcceptanceTester($scenario);
$I->wantTo('掲載問い合わせページのテスト');


$I->amGoingTo('掲載問い合わせページを表示');
$page = InquiryPage::openBy($I);
$I->wait(3);
$I->see('お問い合わせ情報をご入力ください', 'h2');


$I->amGoingTo('掲載問い合わせ内容を入力');

// 企業名
$I->fillField('//*[@id="inquirymaster-company_name"]', '企業名');

// 担当者名
$I->fillField('//*[@id="inquirymaster-tanto_name"]', '担当者名');

// 職種
$I->fillField('//*[@id="inquirymaster-job_type"]', '職種');

// 郵便番号
$I->fillField('//*[@id="inquirymaster-postal_code"]', '108-0073');

// 住所
$I->fillField('//*[@id="inquirymaster-address"]', '東京都港区三田');

// 電話番号
$I->fillField('//*[@id="inquirymaster-tel_no"]', '03-6400-0507');

// メールアドレス
$I->fillField('//*[@id="inquirymaster-mail_address"]', 'test@example.com');

// 問い合わせ内容
$I->fillField('//*[@id="inquirymaster-option100"]', '問い合わせ内容');


$I->amGoingTo('掲載問い合わせ確認ページを表示');
$I->click('上記保護方針に同意のうえお問い合わせする');
$I->wait(3);
$I->see('以下の内容でお間違えなければ「お問い合わせする」ボタンを押してください。', 'h2');


$I->amGoingTo('掲載問い合わせ完了ページを表示');
$I->click('お問い合わせする');
$I->wait(4);
$I->see('お問い合わせが完了しました。', 'h1');
$I->see('お問い合わせありがとうございました。', 'h1');
