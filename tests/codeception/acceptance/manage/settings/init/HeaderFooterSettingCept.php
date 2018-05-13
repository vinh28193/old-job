<?php
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\init\HeaderFooterSettingPage;
use tests\codeception\fixtures\HeaderFooterFixture;
use tests\codeception\fixtures\SiteHtmlFixture;
use app\common\Helper\Html;
use app\models\manage\HeaderFooterSetting;

/* @var $scenario Codeception\Scenario */
/* @var $this yii\codeception\TestCase */

CONST URL = '/manage/secure/settings/header-footer-html/update';
CONST TEL_NO = '0123456789';
CONST TEL_TEXT = 'お電話でのお問い合わせ';
CONST COPY_LIGHT = 'コピーライト更新';

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute(URL);

// テスター準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

/**
 * ヘッダーフッターのデータとHtmlに関するテーブルの
 * 内容を初期化。下記の更新のテストとは異なる内容になっている。
 */
(new SiteHtmlFixture())->initTable();
(new HeaderFooterFixture())->initTable();

/** @var \app\models\JobMasterDisp $job */
$job = \app\models\JobMasterDisp::find()->active()->one();
$userUrls = [
    '全国トップ' => 'top/zenkoku',
    '地域トップ' => 'kanto',
    /**
     * 掲載のお問い合わせと原稿応募ページはbeforeunloadイベントが設定されている。
     * そのためjsアラートが表示されるので、URL入力による画面遷移が出来ない。
     * そのため、掲載のお問い合わせの方は省略する。
     */
//    '掲載のお問い合わせ' => 'inquiry/index',
    '検索結果' => 'kanto/search-result',
    '原稿詳細' => 'kyujin/' . $job->job_no,
    '転送先情報入力フォーム' => 'kyujin/send-mobile/' . $job->job_no,
    '原稿応募' => 'apply/' . $job->job_no,
];
// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('ヘッダー・フッター設定の編集のテスト');

//----------------------
// 代理店・掲載企業権限でログインしてヘッダー・フッター設定ページへ遷移
//----------------------
$admins = [
    '代理店' => 'admin02',
    '掲載企業' => 'admin03',
];
foreach ($admins as $ro => $id) {
    $I->amGoingTo($ro . '権限でアクセス');
    $loginPage->login($id, $id);
    $I->wait(3);
    /** @var Manager $identity */
    $identity = Manager::findByLoginId($id);
    $adminName = Html::encode($identity->name_sei . ' ' . $identity->name_mei);
    $I->wait(3);
    $I->amOnPage(URL);
    $I->wait(3);
    $I->see('こちらの画面はメニューのアクセス制限により閲覧・操作できません。');
    //ログアウト処理
    $I->click($adminName);
    $I->wait(1);
    $I->click('ログアウト');
    $I->wait(3);
}

//----------------------
// 運営元権限でログインしてヘッダー・フッター設定ページへ遷移
//----------------------
$I->amGoingTo('運営元権限でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);
$page = HeaderFooterSettingPage::openBy($I);
$page->go($menu);
$I->wait(3);

//----------------------
// 更新画面→入力→更新→完了一更新画面へ
//----------------------
$I->amGoingTo('更新画面→バリデータの動作確認→入力→更新→完了一更新画面へ');
$I->wait(3);
$I->see($menu->title);

// クライアント側のバリデータの動作確認
$I->amGoingTo('バリデータの動作確認');
for ($i = 1; $i <= 10; $i++) {
    $page->seeTooManyCharacters("header_text{$i}", 20);
    $page->seeTooManyCharacters("footer_text{$i}", 20);
}
$page->seeCharactersStyle('tel_no', 'aaaa', '電話番号は半角数字で入力してください。');
$page->seeTooManyCharacters('tel_no', 30, '0');
$page->seeTooManyCharacters('tel_text', 50);
$page->seeTooManyCharacters('copyright', 200);

// 入力
$I->amGoingTo('入力');
// 開発環境にはファイルサーバーが無いためコメントアウト
//$I->attachFile('#headerfootersetting-imagefile', 'media-upload.png');
for ($i = 1; $i <= 10; $i++) {
    $page->fillHeaderLink($i);
    $page->fillFooterLink($i);
}
$I->fillField('//*[@id="headerfootersetting-tel_no"]', TEL_NO);
$I->fillField('//*[@id="headerfootersetting-tel_text"]', TEL_TEXT);
$I->fillField('//*[@id="headerfootersetting-copyright"]', COPY_LIGHT);

// tests/codeception/acceptance/manage/job/JobRegisterCept.php 同様、プレビューの確認は行わない

// 更新
$I->amGoingTo('更新');
$I->click('更新');
$I->wait(1);
$I->click('OK');
$I->wait(5);
$I->see('更新完了');
$I->click('ヘッダー・フッター設定へ戻る');
$I->wait(1);
$I->see($menu->title);

// 管理画面での確認
$I->amGoingTo('管理画面での反映確認');
/** @var HeaderFooterSetting $hd */
$hd = HeaderFooterSetting::find()->one();
$src = $I->grabAttributeFrom('.file-preview-thumbnails img', 'src');
$this->assertContains($hd->logo_file_name, $src);
for ($i = 1; $i <= 10; $i++) {
    $page->seeHeaderForm($i);
    $page->seeFooterForm($i);
}
$I->seeInField('//*[@id="headerfootersetting-tel_no"]', TEL_NO);
$I->seeInField('//*[@id="headerfootersetting-tel_text"]', TEL_TEXT);
$I->seeInField('//*[@id="headerfootersetting-copyright"]', COPY_LIGHT);

//----------------------
// 求職者画面ページ確認
//----------------------
$I->amGoingTo('求職者画面ページ確認');

foreach ($userUrls as $title => $url) {
    $I->amGoingTo($title . 'ページでのヘッダーフッター確認');
    $I->amOnPage($url);
    $I->wait(3);
    $src = $I->grabAttributeFrom('.header img', 'src');
    $this->assertContains($hd->logo_file_name, $src);
    for ($i = 1; $i <= 10; $i++) {
        $page->seeHeaderLink($i);
        $page->seeFooterLink($i);
    }
    $I->see(TEL_NO, '.hide-sp .nav__phone');
    $I->see(COPY_LIGHT, '.copyright p');
}
