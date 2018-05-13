<?php
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\init\TagPage;
use app\models\manage\SiteHtml;
use tests\codeception\fixtures\SiteHtmlFixture;
use app\common\Helper\Html;

/* @var $scenario Codeception\Scenario */
/* @var $this yii\codeception\TestCase */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
const URL = '/manage/secure/settings/tag/list';

// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute(URL);

// テスター準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// タグのデータに関連するSiteHtmlデータを初期化。
$fix = new SiteHtmlFixture();
$fix->load();

/** @var \app\models\JobMasterDisp $job */
$job = \app\models\JobMasterDisp::find()->active()->one();
$userUrls = [
    'TOPページ' => '/',
    /**
     * 掲載のお問い合わせと原稿応募ページはbeforeunloadイベントが設定されている。
     * そのためjsアラートが表示されるので、URL入力による画面遷移が出来ない。
     * そのため、掲載のお問い合わせの方は省略する。
     */
//    '掲載のお問い合わせ' => 'inquiry/index',
    '検索結果' => 'kanto/search-result',
    '原稿詳細' => 'kyujin/' . $job->job_no,
    '転送先情報入力フォーム' => 'kyujin/send-mobile/' . $job->job_no,
    '404ページ' => 'aaaaaaa',
    'キープ機能' => 'keep/index',
    'パスワード再設定' => '/pass/apply',
];
$appUrl = 'apply/' . $job->job_no;  //'原稿応募'

(new \tests\codeception\fixtures\ApplicationColumnSetFixture())->load();
(new \tests\codeception\fixtures\ApplicationColumnSubsetFixture())->load();
// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('タグ設定の編集のテスト');

//----------------------
// 代理店・掲載企業権限でログインした場合、タグ設定ページへ遷移できないことを検証
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
// 運営元権限でログインしてタグ設定ページへ遷移
//----------------------
$I->amGoingTo('運営元権限でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);
$page = TagPage::openBy($I);
$page->go($menu);
$I->wait(3);
$I->see($menu->title);

//----------------------
// 一覧表示→モーダル表示→入力→更新→完了→更新確認
//----------------------
$labels = [
    'analytics_html' => 'Google Analyticsタグ(</head>の直前）',
    'conversion_html' => '応募コンバージョンタグ(</body> の直前）',
    'remarketing_html' => 'リマーケティングタグ(</body> の直前）',
];
$vals = [];
$I->amGoingTo('一覧表示確認');
foreach (SiteHtml::TAG_MANAGES AS $k => $tag) {
    $vals[$tag] = time();
    $modelId = '#modal-' . $tag;
    $field = $modelId . ' textarea[name="SiteHtml[' . $tag . ']"]';
    $page->clickActionColumn($k + 1, 1);
    $I->wait(2);
    $I->see($labels[$tag]);
    $I->fillField($field, str_repeat('a', 10001));
    $I->wait(1);
    $I->see('タグは10000文字以下で入力してください。');
    $I->fillField($field, $vals[$tag]);
    $I->click($modelId . '  button[type="submit"]');
    $I->wait(5);
    $I->see('更新が完了しました。');
    $page->clickActionColumn($k + 1, 1);
    $I->wait(2);
    $I->seeInField($field, $vals[$tag]);
    $I->click($modelId . ' button[type="button"]');
    $I->wait(2);
}

//----------------------
// 求職者画面ページ確認（応募コンバージョンタグを除く）
//----------------------
$I->amGoingTo('求職者画面ページ確認');

foreach ($userUrls as $title => $url) {
    $I->amGoingTo($title . 'ページでのタグ確認');
    $I->amOnPage($url);
    $I->wait(3);
    $I->see($vals['analytics_html']);
    $I->see($vals['remarketing_html']);
}

//----------------------
// 求職者画面ページ確認（応募コンバージョンタグを含む）
//----------------------
$I->amGoingTo('原稿応募ページでのタグ確認');
$I->amOnPage($appUrl);
$I->wait(3);
$I->see($vals['analytics_html']);
$I->see($vals['remarketing_html']);

$I->amGoingTo('原稿応募確認ページでのタグ確認');
$I->selectOption('#apply-birthdateday', '28');
$I->fillField('#apply-name_sei', str_repeat('a', 10));
$I->fillField('#apply-name_mei', str_repeat('b', 10));
$I->fillField('#apply-kana_sei', str_repeat('c', 10));
$I->fillField('#apply-kana_mei', str_repeat('d', 10));
$I->click('#apply-birthdateyear');
$I->selectOption('#apply-birthdateyear', '1999');
$I->click('#apply-birthdatemonth');
$I->selectOption('#apply-birthdatemonth', '09');
$I->click('#apply-birthdateday');
$I->selectOption('#apply-birthdateday', '28');
$I->click('label[for=apply-sex-0]'); // ラジオボタンは実は隠れているので
$I->selectOption('#apply-pref_id', '茨城県');
$I->fillField('#apply-tel_no', '111-2222-3333');
$I->fillField('#apply-mail_address', 'sonzaishinai@pro-seeds.co.jp');
$I->fillField('#apply-self_pr', '猛烈な自己PR');
$I->click('label[for=apply-option101-0]');
$I->fillField('#apply-option108', '1');
$I->click('同意のうえ応募する');
$I->wait(3);
$I->see('以下の内容でお間違えなければ「応募する」ボタンを押してください。');
$I->see($vals['analytics_html']);
$I->see($vals['remarketing_html']);

$I->amGoingTo('原稿応募完了ページでのタグ確認');
$I->click('応募する');
$I->wait(6);
$I->see($vals['analytics_html']);
$I->see($vals['conversion_html']);
$I->see($vals['remarketing_html']);