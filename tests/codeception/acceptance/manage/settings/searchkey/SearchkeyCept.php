<?php
use app\models\manage\SearchkeyMaster;
use app\modules\manage\models\Manager;
use app\models\manage\ManageMenuMain;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\init\SearchkeyPage;
use tests\codeception\fixtures\SearchkeyMasterFixture;

/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
$ownerAdmin = Manager::findOne(['login_id' => 'admin01']);
Yii::$app->user->identity = $ownerAdmin;
// fixtureを使って初期化
(new SearchkeyMasterFixture())->load();
// モデル取得
/** @var SearchkeyMaster $principalKey */
/** @var SearchkeyMaster $option1key */
$prefKey = SearchkeyMaster::findOne(['table_name' => 'pref']);
$principalKey = SearchkeyMaster::find()->where(['principal_flg' => 1])->one();
$option1key = SearchkeyMaster::find()->where(['principal_flg' => 0])->one();
$option11key = SearchkeyMaster::findOne(['table_name' => 'searchkey_item11']);
// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/settings/searchkey/list');
// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);
// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('検索キー設定のテスト');

//----------------------
// 運営元でログインして検索キー設定画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = SearchkeyPage::openBy($I);
$I->wait(1);
$page->go($menu);

$I->amGoingTo('Grid表示を確認');
$page->checkGridValues($prefKey, 1, '選択する（固定）', 'モーダル（固定）');
$page->checkGridValues($principalKey, 4, '選択する（固定）', 'モーダル（固定）');
$page->checkGridValues($option1key, 5);
$page->checkGridValues($option11key, 14, '-');

//----------------------
// デフォルト検索キーの検証
//----------------------
$I->amGoingTo('勤務地を詳細検証');
$page->openModal(1);
$I->amGoingTo('勤務地input初期表示を検証');
$page->checkCommonItems($prefKey);
$page->cantFillRadioButSeeText('is_category_label', '選択する');
$page->cantFillRadioButSeeText('is_and_search', 'or');
$page->cantFillRadioButSeeText('search_input_tool', 'モーダル');
$page->cantFillRadioButSeeText('icon_flg', '表示しない');
$page->cantFillRadioButSeeText('valid_chk', '有効');

$I->amGoingTo('ClientValidation検証');
$I->fillField('#searchkeymaster-searchkey_name', '');
$I->fillField('#searchkeymaster-sort', '');
$I->wait(1);
$I->see('検索キー名は必須項目です。', '//tr[@id="form-searchkey_name-tr"]/td/div/div');
$I->see('表示順は必須項目です。', '//tr[@id="form-sort-tr"]/td/div/div');

$I->fillField('#searchkeymaster-searchkey_name', str_repeat('a', 51));
$I->fillField('#searchkeymaster-sort', 100);
$I->wait(1);
$I->see('検索キー名は50文字以下で入力してください。', '//tr[@id="form-searchkey_name-tr"]/td/div/div');
$I->see('表示順は"99"以下の数字で入力してください。', '//tr[@id="form-sort-tr"]/td/div/div');

$I->fillField('#searchkeymaster-sort', 'aaa');
$I->wait(1);
$I->see('表示順は整数にしてください。', '//tr[@id="form-sort-tr"]/td/div/div');

$I->fillField('#searchkeymaster-searchkey_name', '');
$I->fillField('#searchkeymaster-sort', '');

$I->amGoingTo('変更可能項目を変更');
$page->fillInputAndRemember('searchkey_name', $prefKey->searchkey_name . 'changed');
$page->fillInputAndRemember('sort', $prefKey->sort + 10);
$page->fillRadioAndRemember('is_on_top', $prefKey->is_on_top ? 0 : 1);
$page->submitModal();
$page->reload();

$I->amGoingTo('変更が反映されているかを確認');
$page->openModal(1);
$page->checkCommonItems();
$I->click('//button[@class="close"]');

// 路線駅、給与、職種に関しては入力の可否と更新時エラーが無いことのみ確認
$keys = [2 => '路線駅', 3 => '給与'];
foreach ($keys as $i => $name) {
    $I->amGoingTo("{$name}を更新");
    $page->openModal($i);
    $page->checkModalItems(
        ['searchkey_name', 'sort', 'is_on_top', 'valid_chk'],
        ['is_category_label', 'is_and_search', 'search_input_tool', 'icon_flg']
    );
    $page->submitModal();
}

//----------------------
// 優先検索キーの検証
//----------------------
$I->amGoingTo('優先検索キーを1つ詳細検証');
$page->openModal(4);
$I->amGoingTo('優先検索キーinput初期表示を検証');
$page->checkCommonItems($principalKey);
$page->cantFillRadioButSeeText('is_category_label', '選択する（固定）');
$page->cantFillRadioButSeeText('search_input_tool', 'モーダル（固定）');
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[is_and_search]'][@value='{$principalKey->is_and_search}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[valid_chk]'][@value='{$principalKey->valid_chk}']");

$I->amGoingTo('変更可能項目を変更');
$page->fillInputAndRemember('searchkey_name', $principalKey->searchkey_name . 'changed');
$page->fillRadioAndRemember('is_and_search', $principalKey->is_and_search ? 0 : 1);
$page->fillInputAndRemember('sort', $principalKey->sort + 10);
$page->fillRadioAndRemember('is_on_top', $principalKey->is_on_top ? 0 : 1);
$page->fillRadioAndRemember('icon_flg', $principalKey->icon_flg ? 0 : 1);
$page->fillRadioAndRemember('valid_chk', $principalKey->valid_chk ? 0 : 1);
$page->submitModal();

$I->amGoingTo('変更が反映されているかを確認');
$page->openModal(4);
$page->checkCommonItems();
$page->cantFillRadioButSeeText('is_category_label', '選択する（固定）');
$page->cantFillRadioButSeeText('search_input_tool', 'モーダル（固定）');
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[is_and_search]'][@value='{$page->attributes['is_and_search']}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[valid_chk]'][@value='{$page->attributes['valid_chk']}']");

//----------------------
// 2階層汎用検索キーの検証
//----------------------
$I->amGoingTo('2階層汎用検索キーを1つ詳細検証');
$page->openModal(5);
$I->amGoingTo('2階層汎用検索キーinput初期表示を検証');
$page->checkCommonItems($option1key);
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[is_category_label]'][@value='{$option1key->is_category_label}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[is_and_search]'][@value='{$option1key->is_and_search}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[search_input_tool]'][@value='{$option1key->search_input_tool}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[valid_chk]'][@value='{$option1key->valid_chk}']");

$I->amGoingTo('変更可能項目を変更');
$page->fillInputAndRemember('searchkey_name', $option1key->searchkey_name . 'changed');
$page->fillRadioAndRemember('is_category_label', $option1key->is_category_label ? 0 : 1);
$page->fillRadioAndRemember('is_and_search', $option1key->is_and_search ? 0 : 1);
$page->fillInputAndRemember('sort', $option1key->sort + 10);
if ($option1key->search_input_tool == 3) {
    $value = 1;
} else {
    $value = $option1key->search_input_tool + 1;
}
$page->fillRadioAndRemember('search_input_tool', $value);
$page->fillRadioAndRemember('is_on_top', $option1key->is_on_top ? 0 : 1);
$page->fillRadioAndRemember('icon_flg', $option1key->icon_flg ? 0 : 1);
$page->fillRadioAndRemember('valid_chk', $option1key->valid_chk ? 0 : 1);
$page->submitModal();

$I->amGoingTo('変更が反映されているかを確認');
$page->openModal(5);
$page->checkCommonItems();
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[is_category_label]'][@value='{$page->attributes['is_category_label']}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[is_and_search]'][@value='{$page->attributes['is_and_search']}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[search_input_tool]'][@value='{$page->attributes['search_input_tool']}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[valid_chk]'][@value='{$page->attributes['valid_chk']}']");

// 残りは入力の可否と更新時エラーが無いことのみ確認
for ($i = 5; $i <= 13; $i++) {
    $I->amGoingTo('2階層汎用検索キー更新:' . ($i - 4));
    $page->openModal($i);
    $page->checkModalItems(
        ['searchkey_name', 'is_category_label', 'is_and_search', 'sort', 'search_input_tool', 'is_on_top', 'icon_flg', 'valid_chk'],
        []
    );
    $page->submitModal();
}

//----------------------
// 1階層汎用検索キーの検証
//----------------------
$I->amGoingTo('1階層汎用検索キーを1つ詳細検証');
$page->openModal(14);
$I->amGoingTo('1階層汎用検索キーinput初期表示を検証');
$page->checkCommonItems($option11key);
$I->cantSee('カテゴリ選択可否');
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[is_and_search]'][@value='{$option11key->is_and_search}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[search_input_tool]'][@value='{$option11key->search_input_tool}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[valid_chk]'][@value='{$option11key->valid_chk}']");

$I->amGoingTo('変更可能項目を変更');
$page->fillInputAndRemember('searchkey_name', $option11key->searchkey_name . 'changed');
$page->fillRadioAndRemember('is_and_search', $option11key->is_and_search ? 0 : 1);
$page->fillInputAndRemember('sort', $option11key->sort + 10);
if ($option11key->search_input_tool == 3) {
    $value = 1;
} else {
    $value = $option11key->search_input_tool + 1;
}
$page->fillRadioAndRemember('search_input_tool', $value);
$page->fillRadioAndRemember('is_on_top', $option11key->is_on_top ? 0 : 1);
$page->fillRadioAndRemember('icon_flg', $option11key->icon_flg ? 0 : 1);
$page->fillRadioAndRemember('valid_chk', $option11key->valid_chk ? 0 : 1);
$page->submitModal();

$I->amGoingTo('変更が反映されているかを確認');
$page->openModal(14);
$page->checkCommonItems();
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[is_and_search]'][@value='{$page->attributes['is_and_search']}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[search_input_tool]'][@value='{$page->attributes['search_input_tool']}']");
$I->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[valid_chk]'][@value='{$page->attributes['valid_chk']}']");

for ($i = 15; $i <= 23; $i++) {
    $I->amGoingTo('1階層汎用検索キー更新:' . ($i - 14));
    $page->openModal($i);
    $page->checkModalItems(
        ['searchkey_name', 'is_and_search', 'sort', 'search_input_tool', 'is_on_top', 'icon_flg', 'valid_chk'],
        []
    );
    $I->cantSee('カテゴリ選択可否');
    $page->submitModal();
}

//----------------------
// 公開状況の入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('公開状況をinput');
$I->selectOption('#searchkeymastersearch-valid_chk', 1);

//----------------------
// 表示場所の入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('表示場所をinput');
$I->selectOption('#searchkeymastersearch-is_on_top', 1);

//----------------------
// 階層の入力
// todo 入力・動作検証
//----------------------
$I->amGoingTo('階層をinput');
$I->selectOption('#searchkeymastersearch-hierarchytype', 1);

//----------------------
// 検索する
// todo 動作検証
//----------------------
$I->amGoingTo('検索する');
$I->click('この条件で表示する');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// クリアする
// todo 動作検証
////----------------------
$I->amGoingTo('クリアする');
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
