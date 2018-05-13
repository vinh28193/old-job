<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/04/24
 * Time: 15:18
 */
use app\models\manage\BaseColumnSet;
use app\models\manage\ListDisp;
use app\models\manage\MainDisp;
use app\models\manage\ManageMenuMain;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Pref;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\job\JobRegisterPage;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\fixtures\JobColumnSetFixture;
use tests\codeception\fixtures\JobColumnSubsetFixture;
use tests\codeception\unit\fixtures\AdminMasterFixture;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$createMenu = ManageMenuMain::findFromRoute('/manage/secure/job/create');
$updateMenu = ManageMenuMain::findFromRoute('/manage/secure/job/update');

// todo next corp_column_setのレコードの整備・連携
(new JobColumnSetFixture())->load();
(new JobColumnSubsetFixture())->load();
(new AdminMasterFixture())->load();

// disp_type.disp_type_no=3の代理店、掲載企業、プランの組み合わせを準備
$array = JobRegisterPage::initPlan();
$dispTypeId = $array['dispTypeId'];
$corp = $array['corp'];
$client = $array['client'];

// mainとlistのdisplay項目を取得
$mainItems = MainDisp::items($dispTypeId);
$listItems = ListDisp::items($dispTypeId);

// 表示する都道府県のモデル取得
/** @var Pref[] $prefs */
$prefs = Pref::find()->joinWith('area')->where([Area::tableName() . '.valid_chk' => 1])->all();

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('求人情報登録変更のテスト');

//----------------------
// 運営元でログインして一覧へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);
$page = JobRegisterPage::openBy($I);
$I->wait(1);

//----------------------
// 新規登録画面
//----------------------
$I->amOnPage($createMenu->href);
// 代理店のselect2に値をinput
$I->amGoingTo('代理店、掲載企業、プランを入力');

$I->click('//*[@id="select2-jobmaster-corpmasterid-container"]');
$I->wait(2);
$I->fillField('//span[@class="select2-search select2-search--dropdown"]/input', $corp->corp_name);
$I->wait(2);
$I->click("//*[@id='select2-jobmaster-corpmasterid-results']/li[text()='$corp->corp_name']");
$I->wait(2);

$I->click('#select2-jobmaster-client_master_id-container');
$I->wait(2);
$I->click("//*[@id='select2-jobmaster-client_master_id-results']/li[text()='$client->client_name']");
$I->wait(2);

// 日付を入力
$I->amGoingTo('日付を入力');
$page->fillAndRemember('disp_start_date', date('Y/m/d'));

// 状態を有効に
$I->amGoingTo('状態を有効に');
$I->selectOption('input[name=JobMaster\\[valid_chk\\]]', 1);

// 募集要項入力
$I->amGoingTo('募集要項入力');
$I->switchToIFrame("iframeName"); // 操作対象をiframeにスイッチ

$displayPlace = 'main';
foreach ($mainItems as $mainItem) {
    $id = time();
    // todo next 各clientValidation.fixtureでcolumn_setの値をloadしてからそれに合わせたテストをする。
    if (strpos($mainItem->column_name, 'job_pict_text') !== false) {
        // todo next 画像テキストの入力。現在はphantomJsがslick-sliderに対応していないためテスト不可
    } elseif (strpos($mainItem->column_name, 'media_upload_id') === false) {
        switch ($mainItem->data_type) {
            case BaseColumnSet::DATA_TYPE_TEXT:
                $page->fillEditableTextareaAndRemember($mainItem, $mainItem->label . $id . PHP_EOL . '改行あり', $displayPlace);
                break;
            case BaseColumnSet::DATA_TYPE_NUMBER:
                $page->fillEditableTextAndRemember($mainItem, $id, $displayPlace);
                break;
            case BaseColumnSet::DATA_TYPE_URL:
                $page->fillEditableTextAndRemember($mainItem, "http://jm2.yii/{$id}", $displayPlace);
                break;
            case BaseColumnSet::DATA_TYPE_MAIL:
                $page->fillEditableTextAndRemember($mainItem, "{$displayPlace}-{$id}@pro-seeds.co.jp", $displayPlace);
                break;
            default:
                break;
        }
        $I->wait(1);
        $I->see($page->{$mainItem->column_name});
    }
}

$displayPlace = 'list';
foreach ($listItems as $listItem) {
    $id = time();
    // todo next 各clientValidation.fixtureでcolumn_setの値をloadしてからそれに合わせたテストをする。
    switch ($listItem->data_type) {
        case BaseColumnSet::DATA_TYPE_TEXT:
            $page->fillEditableTextareaAndRemember($listItem, $listItem->label . $id . PHP_EOL . '改行あり', $displayPlace);
            break;
        case BaseColumnSet::DATA_TYPE_NUMBER:
            $page->fillEditableTextAndRemember($listItem, $listItem->max_length, $displayPlace);
            break;
        case BaseColumnSet::DATA_TYPE_URL:
            $page->fillEditableTextAndRemember($listItem, "http://jm2.yii/{$id}", $displayPlace);
            break;
        case BaseColumnSet::DATA_TYPE_MAIL:
            $page->fillEditableTextAndRemember($listItem, "{$displayPlace}-{$id}@pro-seeds.co.jp", $displayPlace);
            break;
        case BaseColumnSet::DATA_TYPE_CHECK:
            $values = [
                $listItem->subsetItems[0]->subset_name,
                $listItem->subsetItems[1]->subset_name,
            ];
            $page->checkEditableAndRemember($listItem, $values, $displayPlace);
            break;
        case BaseColumnSet::DATA_TYPE_RADIO:
            $page->selectEditableAndRemember($listItem, $listItem->subsetItems[0]->subset_name, $displayPlace);
            break;
        default:
            break;
    }
    $I->wait(1);
    $I->see($page->{$listItem->column_name});
}

// 画像
$I->amGoingTo('画像モーダル');
$I->click('#media_upload_id_1'); // 画像をクリック
$I->wait(1);
$I->switchToIFrame(); // 操作対象を親画面にスイッチ
$I->see('写真-登録・修正', 'h3');
// todo next 画像アップロード関連テスト
$I->click('//*[@id="picModal"]/div/div/div/button');
$I->wait(1);

// 検索キーの入力
$I->amGoingTo('勤務地の入力');
$I->click('選択する');
$I->wait(1);

$I->amGoingTo("{$prefs[0]->dist[0]->dist_name}をチェック");
$I->executeJS("$('#pref{$prefs[0]->id}').collapse('show')"); // アコーディオンを開く
$I->wait(1);
$I->checkOption("div#pref{$prefs[0]->id} input[name=JobDist\\[itemIds\\]\\[\\]]", $prefs[0]->dist[0]->dist_name);
$I->click('変更を保存');
$I->wait(1);

// todo access update page of the job that was created now
$I->amGoingTo('完了画面から一覧へ遷移');
$I->click('この条件で表示する');
$I->wait(5);
$I->amGoingTo('一覧から更新へ遷移');
$page->clickActionColumn(1, 1);
$I->wait(5);
$I->seeInTitle($updateMenu->title);
$I->see($updateMenu->title, 'h1');

// todo change input type
$I->amGoingTo('Change input type');
$I->click('クラシック');
$I->wait(2);
$I->switchToIFrame();
$I->see('入力中の内容が反映されませんが入力モードの切り替えを行いますか？', "//div[@class='bootbox-body']");
$I->click('OK');
$I->wait(5);

foreach ($mainItems as $mainItem) {
    if (strpos($mainItem->column_name, 'media_upload_id') !== false) {
        $I->seeElement("//img[@id='$mainItem->column_name']");
    } else {
        switch ($mainItem->data_type) {
            case BaseColumnSet::DATA_TYPE_TEXT:
                $I->seeElement("//textarea[@id='jobmaster-$mainItem->column_name']");
                break;
            case BaseColumnSet::DATA_TYPE_NUMBER:
            case BaseColumnSet::DATA_TYPE_URL:
            case BaseColumnSet::DATA_TYPE_MAIL:
                $I->seeElement("//input[@id='jobmaster-$mainItem->column_name']");
                break;
            default:
                break;
        }
    }
}

foreach ($listItems as $listItem) {
    switch ($listItem->data_type) {
        case BaseColumnSet::DATA_TYPE_TEXT:
            $I->seeElement("//textarea[@id='jobmaster-$listItem->column_name']");
            break;
        case BaseColumnSet::DATA_TYPE_NUMBER:
        case BaseColumnSet::DATA_TYPE_URL:
        case BaseColumnSet::DATA_TYPE_MAIL:
            $I->seeElement("//input[@id='jobmaster-$listItem->column_name']");
            break;
        case BaseColumnSet::DATA_TYPE_RADIO:
            $I->seeElement("//select[@id='jobmaster-$listItem->column_name']");
            break;
        case BaseColumnSet::DATA_TYPE_CHECK:
            $I->seeElement("//input[@type='checkbox' and @name='JobMaster[$listItem->column_name][]']");
            break;
        default:
            break;
    }
}
// todo test inputs of requirements
$I->fillField("//textarea[@id='jobmaster-corp_name_disp']", "");
$I->wait(3);
$I->see('１会社名は必須項目です。', "//div[@class='form-group field-jobmaster-corp_name_disp required has-error']/div[@class='error-block text-danger']");

$I->fillField("//input[@id='jobmaster-map_url']", "あああああ");
$I->wait(3);
$I->see('１勤務地MAPは有効な URL 書式ではありません。', "//div[@class='form-group field-jobmaster-map_url has-error']/div[@class='error-block text-danger']");

$I->fillField("//input[@id='jobmaster-wage_text']", "あああああ");
$I->wait(3);
$I->see('１初任給は数字にしてください。', "//div[@class='form-group field-jobmaster-wage_text has-error']/div[@class='error-block text-danger']");

$I->fillField("//input[@id='jobmaster-wage_text']", "1111");
$I->wait(3);
$I->see('１初任給は"300"以下の数字で入力してください。', "//div[@class='form-group field-jobmaster-wage_text has-error']/div[@class='error-block text-danger']");

$I->fillField("//input[@id='jobmaster-option101']", "あああああ");
$I->wait(3);
$I->see('１オプション２（URL)は有効な URL 書式ではありません。', "//div[@class='form-group field-jobmaster-option101 has-error']/div[@class='error-block text-danger']");

$I->fillField("//input[@id='jobmaster-option102']", "あああああ");
$I->wait(3);
$I->see('正しいメールアドレス表記で入力してください。', "//div[@class='form-group field-jobmaster-option102 has-error']/div[@class='error-block text-danger']");