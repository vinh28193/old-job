<?php

use app\models\JobMasterDisp;
use app\models\manage\ManageMenuMain;
use app\models\manage\CustomField;
use tests\codeception\_pages\manage\ManageLoginPage;
use \tests\codeception\_pages\manage\settings\init\CustomFieldPage;

$customFiledPath = '/manage/secure/settings/custom-field/list';

$menu = ManageMenuMain::findFromRoute($customFiledPath);
$customFields = CustomField::find()->all();

/* @var $scenario Codeception\Scenario */
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

$admins = [
    [
        'type' => '代理店',
        'loginId' => 'admin02',
        'password' => 'admin02',
        'resultSee' => 'アクセス権限がありません',
    ],
    [
        'type' => '掲載企業',
        'loginId' => 'admin03',
        'password' => 'admin03',
        'resultSee' => 'アクセス権限がありません',
    ],
    [
        'type' => '運営元',
        'loginId' => 'admin01',
        'password' => 'admin01',
        'resultSee' => "{$menu->title}",
    ],
];

(new \tests\codeception\fixtures\CustomFieldFixture())->load();

//----------------------
// アクセス権限の確認
//----------------------
foreach((array) $admins as $admin) {
    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage = ManageLoginPage::openBy($I);
    $loginPage->login($admin['loginId'], $admin['password']);
    $I->wait(3);
    $I->see('ホーム', 'h1');

    $I->amGoingTo('カスタムフィールド設定画面へ移動');
    $I->amOnPage($customFiledPath);
    $I->wait(1);
    $I->see($admin['resultSee'], 'h1');
    $I->amOnPage('manage/logout');
    $I->wait(3);
}
//----------------------
// 運営元権限でのメニュー表示確認
//----------------------
$I->amGoingTo('運営元でログインする');
$loginPage->login('admin01', 'admin01');
$I->wait(3);
$I->see('ホーム', 'h1');

$I->amGoingTo("サイト設定メニューに{$menu->title}がある");
$I->click('サイト設定');
$I->see($menu->title, 'h4');
//----------------------
// カスタムフィールド設定画面の確認
//----------------------
$I->amGoingTo('カスタムフィールド設定画面へ遷移する');
$page = CustomFieldPage::openBy($I);
$page->go($menu);
//----------------------
// 新規登録画面の確認
//----------------------
$I->amGoingTo('新規登録');
$page->openCreateModal();
//----------------------
// 制約により拒否される
//----------------------
$I->amGoingTo('表示内容、URL、公開状況は必須項目である');
$I->click('登録');
$I->wait(1);
$I->see('表示内容は必須項目です。', '//div[contains(@class, "error-block")]');
$I->see('URLは必須項目です。', '//div[contains(@class, "error-block")]');
$I->see('公開状況は必須項目です。', '//div[contains(@class, "error-block")]');

$I->amGoingTo('表示内容は500文字以下の制約がある');
$I->fillField('表示内容', str_repeat('a', 501));
$I->wait(1);
$I->see('表示内容は500文字以下で入力してください。', '//div[contains(@class, "error-block")]');

$I->amGoingTo('URLは2000文字以下の制約がある');
$I->fillField('URL', str_repeat('a', 2001));
$I->wait(1);
$I->see('URLは2000文字以下で入力してください。', '//div[contains(@class, "error-block")]');

$I->amGoingTo('URLはパターンマッチの制約がある');
$I->fillField('URL', 'pattern/match/test/');
$I->wait(1);
$I->see('URLは無効です。', '//div[contains(@class, "error-block")]');

$I->amGoingTo('URLは重複制限がある');
/** @var CustomField $model */
$model = (new CustomField())->find()->one();
$I->fillField('URL', $model->url);
$I->wait(1);
$I->see('URLで "' . $model->url . '" は既に使われています。', '//div[contains(@class, "error-block")]');
//----------------------
// 正しい入力が登録できる
//----------------------
$I->amGoingTo('表示内容に500文字入力できる');
$detail = str_repeat('a', 500);
$I->fillField('表示内容', $detail);
$I->wait(1);
$I->dontSee('表示内容は500文字以下で入力してください。', '//div[contains(@class, "error-block")]');

$I->amGoingTo('URLは2000文字入力できる');
$sample = '/this/is/max/?test=2000&id=';
$url = $sample . str_repeat('a', 2000 - strlen($sample));
$I->fillField('URL', $url);
$I->wait(1);
$I->dontSee('URLは2000文字以下で入力してください。', '//div[contains(@class, "error-block")]');
$I->dontSee('URLは無効です。', '//div[contains(@class, "error-block")]');

// todo 画像は？

$I->amGoingTo('公開情報を選択できる');
$valid_chk = CustomField::VALID;
$I->selectOption('//input[@name="CustomField[valid_chk]"]', $valid_chk);
$I->wait(1);
$I->dontSee('公開状況は必須項目です。', '//div[contains(@class, "error-block")]');

$I->amGoingTo('正しい値が登録できる');
$page->submitCreateModal();
$I->seeInDatabase('custom_field', ['detail' => $detail, 'url' => $url, 'valid_chk' => $valid_chk]);
//----------------------
// 登録更新できる
//----------------------
$I->amGoingTo('更新ができる');
$page->openModal(1);
$detail = 'update';
$url = $sample . 'test';
$valid_chk = CustomField::INVALID;
$I->fillField('CustomField[detail]', $detail);
$I->fillField('CustomField[url]', $url);
$I->selectOption('//input[@name="CustomField[valid_chk]"]', $valid_chk);
$page->submitUpdateModal();
$I->seeInDatabase('custom_field', ['detail' => $detail, 'url' => $url, 'valid_chk' => $valid_chk]);
//----------------------
// URLで検索できる
//----------------------
$I->amGoingTo('URLで検索できる');
$page->searchUrl($url);
$I->see($url);
//----------------------
// 削除できる
//----------------------
$I->amGoingTo('削除ができる');
$page->deleteBulk();
$I->dontSeeInDatabase('custom_field', ['url' => $url]);
//----------------------
// 検索条件をクリアする
//----------------------
$page->clearSearchUrl();
//----------------------
// 登録データの表示確認
//----------------------
$detail = '表示テストです';
$url = '/kanto/PC13';
// すでに同じURLの登録があれば削除
$page->deleteExistUrl($url);
$customField = new CustomField();
$customField->load([$customField->formName() => ['detail' => $detail, 'url' => $url, 'valid_chk' => 1]]);
$customField->save();
$I->wait(1);
$I->amOnPage($url);
$I->seeElement('p.resultCustomField__text');
$I->see($detail);
//----------------------
// 登録データがない場合の表示確認
//----------------------
// 先程登録したデータを削除
$I->amOnPage($customFiledPath);
$I->wait(1);
$page->deleteExistUrl($url);
$I->wait(1);
$I->amOnPage($url);
$I->dontSeeElement('p.resultCustomField__text');
