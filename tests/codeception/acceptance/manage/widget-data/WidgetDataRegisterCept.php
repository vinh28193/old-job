<?php

use app\models\manage\ManageMenuMain;
use app\models\manage\Widget;
use app\models\manage\WidgetData;
use app\models\manage\searchkey\Area;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\fixtures\AreaFixture;
use tests\codeception\fixtures\WidgetFixture;
use tests\codeception\fixtures\WidgetDataFixture;
use tests\codeception\fixtures\WidgetDataAreaFixture;
use tests\codeception\_pages\manage\widget_data\WidgetDataRegisterPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// Areaのfixtureを読み込み（有効エリアが複数ある＝ワンエリア状態ではない状態に依存）
(new AreaFixture())->initTable();

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/widget-data/create');

// 画像無し、タイトル・ディスクリプションありのwidgetを用意
/** @var Widget $widget */
$widget = Widget::find()->one();
$widget->widget_name = 'Ceptテスト';
$widget->element1 = Widget::ELEMENT_TITLE;
$widget->element2 = Widget::ELEMENT_DESCRIPTION;
$widget->element3 = Widget::ELEMENT_HIDE;
$widget->update(false);

/** @var app\common\ProseedsFormatter $formatterComp */
$formatterComp = Yii::$app->formatter;

$baseUrl = 'http://test.jobmaker.jp/';

/** @var \app\components\Area $areaComp */
$areaComp = Yii::$app->area;

/** @var app\models\manage\searchkey\Area $otherArea */
$otherArea = Area::find()->where(['valid_chk' => Area::FLAG_VALID])->one();

// テスター準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// 権限別アクセステスト ////////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('ウィジェット登録画面のテスト');
$admins = [
    [
        'managerType' => '掲載企業',
        'loginId' => 'admin03',
        'password' => 'admin03',
    ],
    [
        'managerType' => '代理店権限',
        'loginId' => 'admin02',
        'password' => 'admin02',
    ],
];

foreach ($admins as $admin) {
    $I->amGoingTo("{$admin['managerType']}でアクセス");
    $loginPage->login($admin['loginId'], $admin['password']);
    $I->wait(3);
    if (isset($page)) {
        /* @var $page WidgetDataRegisterPage */
        $I->amOnPage($page->getUrl());
    } else {
        $page = WidgetDataRegisterPage::openBy($I);
        $I->wait(3);
    }
    $I->expect('accessが出来ない');
    $I->seeInTitle('アクセス権限がありません');
    $I->see('誠に申し訳ございません。');
    $I->see('こちらの画面はメニューのアクセス制限により閲覧・操作できません。');
    $I->amOnPage('manage/logout');
}

$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);

$page = WidgetDataRegisterPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->title);

//----------------------
// widget選択
//----------------------
$I->amGoingTo('ドロップダウンでテスト用widgetを選択');
$I->selectOption('#widgetId', $widget->widget_name);
$I->wait(3);

// client side validation検証（画像と動画タグ以外）/////////////////////////////////////////////////////////////////////
//----------------------
// 表示エリアとリンク先URL表示確認
//----------------------
$I->amGoingTo('表示エリアとリンク先URLの表示確認');
foreach ($areaComp->tenantArea as $key => $area) {
    $i = $key + 1;
    $I->see($area->area_name, "//*[@id='widgetdata-areaids']/label[{$i}]");
    $I->see($area->area_name, "//*[@id='form-urls-tr']/td/div/div[{$i}]/label");
}
$I->amGoingTo('余分なものが表示されていないことを確認');
$over = count($areaComp->tenantArea) + 1;
$I->cantSeeElement("//*[@id='widgetdata-areaids']/label[{$over}]");
$I->cantSeeElement("//*[@id='form-urls-tr']/td/div/div[{$over}]");

//----------------------
// 表示エリアとリンク先URLの入力
//----------------------
$I->amGoingTo('チェックボックスにチェックを入れる'); // 「全国エリア以外の任意のエリア」
$I->checkOption($otherArea->area_name);
$I->wait(2);

$I->expect('validationが成功する');
$page->checkValidate('areaids', null);

$I->amGoingTo('チェックを外す');
$I->uncheckOption($otherArea->area_name);
$I->wait(2);

$I->expect('必須validationがかかる');
$page->checkValidate('areaids', '表示エリアは必須項目です。');

$I->amGoingTo('全国と全国以外の任意のエリアにチェックを入れなおす');
foreach ([$areaComp->nationwideArea, $otherArea] as $model) {
    /** @var Area $model */
    $I->checkOption($model->area_name);
}
$I->expect('validationが成功する');
$page->checkValidate('areaids', null);

// ▼「全国エリア」と、「全国エリア以外の任意のエリア」で検証▼
foreach ([$areaComp->nationwideArea, $otherArea] as $model) {
    /** @var Area $model */
    $areaId = $model->id;
    $I->amGoingTo('リンク先URLに不正な文字列を入力');
    $page->fillUrlsAndRemember($areaId, 'this is not url');

    $I->expect('URL形式validationがかかる');
    $page->checkValidateForUrls($areaId, 'リンク先URLは有効な URL 書式ではありません');

    $I->amGoingTo('リンク先URLに正しいURL形式で2000文字入力');
    $page->fillUrlsAndRemember($areaId, $baseUrl . str_repeat('a', 2000 - strlen($baseUrl)));

    $I->expect('文字数上限validationがかかる');
    $page->checkValidateForUrls($areaId, 'リンク先URLは2000文字未満で入力してください.');

    $I->amGoingTo('リンク先URLに空文字を入力');
    $page->fillUrlsAndRemember($areaId, '');

    $I->expect('validationが成功する');
    $page->checkValidateForUrls($areaId, null);

    $I->amGoingTo('リンク先URLに正しいURL形式で1999文字入力');
    $page->fillUrlsAndRemember($areaId, $baseUrl . str_repeat('a', 1999 - strlen($baseUrl)));

    $I->expect('validationが成功する');
    $page->checkValidateForUrls($areaId, null);
}
// ▲全国エリアと、任意の全国エリア以外のエリアで検証▲

//----------------------
// タイトルの入力
//----------------------
$I->amGoingTo('タイトルに101文字入力');
$page->fillInputAndRemember('title', str_repeat('a', 101));

$I->expect('文字数上限validationがかかる');
$page->checkValidate('title', 'タイトルは100文字以下で入力してください。');

$I->amGoingTo('タイトルに空文字を入力');
$page->fillInputAndRemember('title', '');

$I->expect('必須validationがかかる');
$page->checkValidate('title', 'タイトルは必須項目です。');

$I->amGoingTo('タイトルに100文字入力');
$page->fillInputAndRemember('title', str_repeat('a', 100));

$I->expect('validationが成功する');
$page->checkValidate('title', false);

//----------------------
// ディスクリプションの入力
//----------------------
$I->amGoingTo('ディスクリプションに201文字入力');
$page->fillInputAndRemember('description', str_repeat('a', 201));

$I->expect('文字数上限validationがかかる');
$page->checkValidate('description', 'ディスクリプションは200文字以下で入力してください。');

$I->amGoingTo('ディスクリプションに空文字を入力');
$page->fillInputAndRemember('description', '');

$I->expect('必須validationがかかる');
$page->checkValidate('description', 'ディスクリプションは必須項目です。');

$I->amGoingTo('ディスクリプションに200文字入力');
$page->fillInputAndRemember('description', str_repeat('a', 200));

$I->expect('validationが成功する');
$page->checkValidate('description', null);

//----------------------
// 表示順の入力
//----------------------
$I->amGoingTo('表示順に不正な文字列を入力');
$page->fillInputAndRemember('sort', '文字列');

$I->expect('形式validationがかかる');
$page->checkValidate('sort', '表示順は整数にしてください。');

$I->amGoingTo('表示順に空文字を入力');
$page->fillInputAndRemember('sort', '');

$I->expect('必須validationがかかる');
$page->checkValidate('sort', '表示順は必須項目です。');

$I->amGoingTo('表示順に0を入力');
$page->fillInputAndRemember('sort', 0);

$I->expect('数字が1以上であることのvalidationがかかる');
$page->checkValidate('sort', '表示順は"1"以上の数字で入力してください。');

$I->amGoingTo('表示順に1を入力');
$page->fillInputAndRemember('sort', 1);

$I->expect('validationが成功する');
$page->checkValidate('sort', null);

$I->amGoingTo('表示順に256と入力');
$page->fillInputAndRemember('sort', 256);

$I->expect('数値上限validationがかかる');
$page->checkValidate('sort', '表示順は"255"以下の数字で入力してください。');

$I->amGoingTo('表示順に255と入力');
$page->fillInputAndRemember('sort', 255);

$I->expect('validationが成功する');
$page->checkValidate('sort', null);

//----------------------
// 日付の入力
//----------------------
$I->amGoingTo('公開開始日、終了日に不正な文字列を入力');
$page->fillInputAndRemember('disp_start_date', '文字列');
$page->fillInputAndRemember('disp_end_date', '文字列');

$I->expect('形式validationがかかる');
$page->checkValidate('disp_start_date', '公開開始日の書式が正しくありません。');
$page->checkValidate('disp_end_date', '公開終了日の書式が正しくありません。');

$I->amGoingTo('公開開始日、終了日に空文字を入力する');
$page->fillInputAndRemember('disp_start_date', '');
$page->fillInputAndRemember('disp_end_date', '');

$I->expect('開始日に必須validationがかかり、終了日のvalidationは成功する');
$page->checkValidate('disp_start_date', '公開開始日は必須項目です。');
$page->checkValidate('disp_end_date', null);

$I->amGoingTo('公開終了日に公開開始日より１日前の内容を入力する');
$page->fillInputAndRemember('disp_start_date', '2017/06/15');
$page->fillInputAndRemember('disp_end_date', '2017/06/14');

$I->expect('比較validationがかかる');
$page->checkValidate('disp_end_date', '公開終了日は公開開始日より後の日付にしてください.');

$I->amGoingTo('公開開始日に公開終了日より１日前の内容を入力する');
$page->fillInputAndRemember('disp_start_date', '2017/06/15');
$page->fillInputAndRemember('disp_end_date', '2017/06/16');

$I->expect('validationが成功する');
$page->checkValidate('disp_start_date', null);
$page->checkValidate('disp_end_date', null);
//----------------------
// 状態の入力
//----------------------
$I->amGoingTo('入力をせず「登録する」をクリックする');
$I->click('登録する');
$I->wait(2);

$I->expect('必須validationがかかる');
$page->checkValidate('valid_chk', '状態は必須項目です。');

$I->amGoingTo('状態にチェックを入れる');
$page->fillRadioAndRemember('valid_chk', WidgetData::VALID);

$I->expect('validationが成功する');
$page->checkValidate('valid_chk', null);

// 登録検証（画像と動画タグ以外）///////////////////////////////////////////////////////////////////////////////////////
//----------------------
// 登録の成否
//----------------------
$I->amGoingTo('登録する');
$I->click('登録する');
$I->wait(3);
$I->see('ウィジェットデータを登録してよろしいですか？');
$I->click('OK');
$I->wait(2);

$I->expect('登録完了画面に遷移する');
$I->seeInTitle('ウィジェットデータ - 完了');
$I->see('作成・編集完了', 'h1');
$I->see('ウィジェットデータの登録が完了しました。', 'p');

//----------------------
// 登録内容確認
//----------------------
$I->amGoingTo('完了画面のボタンで一覧画面へ遷移');
$I->click('ウィジェットデータ一覧画面へ');
$I->wait(5);

$I->expect('一覧画面が表示される');
$I->see('ウィジェットデータ一覧', 'h1');

$I->amGoingTo('一覧を表示');
$I->selectOption('#widgetdatasearch-searchitem', 'タイトル');
$I->fillField('//input[@name="WidgetDataSearch[searchText]"]', $page->attributes['title']);
$I->click('この条件で表示する');
$I->wait(5);

$I->expect('ウィジェットデータが一覧で表示される');
$page->seeInGrid(1, 2, $widget->widget_no);
$page->seeInGrid(1, 3, $widget->widget_name);
$page->seeInGrid(1, 4, $page->attributes['title']);
$page->seeInGrid(1, 5, $page->attributes['description']);
$page->seeInGrid(1, 6, $formatterComp->asDate($page->attributes['disp_start_date']));
$page->seeInGrid(1, 7, $formatterComp->asDate($page->attributes['disp_end_date']));
$page->seeInGrid(1, 8, $formatterComp->asValidChk($page->attributes['valid_chk']));

//----------------------
// 編集画面初期化確認
//----------------------
$I->amGoingTo('先ほど登録したウィジェットデータの編集ボタンをクリック');
$page->clickActionColumn(1, 1);
$I->wait(5);

$I->expect('先ほど登録した内容が全て正しく表示されている');
$I->seeInTitle('ウィジェットデータの編集', 'h1');
$I->see($widget->widget_name);
foreach ([$areaComp->nationwideArea, $otherArea] as $model) {
    /* @var Area $model */
    $I->seeCheckboxIsChecked($model->area_name);
    $I->seeInField('//input[@name="WidgetData[urls][' . $model->id . ']"]', $page->attributes['urls'][$model->id]);
}
$I->seeInField('//input[@name="WidgetData[title]"]', $page->attributes['title']);
$I->seeInField('//input[@name="WidgetData[description]"]', $page->attributes['description']);
$I->seeInField('//input[@name="WidgetData[disp_start_date]"]', $formatterComp->asDate($page->attributes['disp_start_date']));
$I->seeInField('//input[@name="WidgetData[disp_end_date]"]', $formatterComp->asDate($page->attributes['disp_end_date']));
$I->seeInField('//input[@name="WidgetData[sort]"]', $page->attributes['sort']);

//----------------------
// 表示エリアとリンク先URLの組み合わせ登録確認
//----------------------
$updateUrl = $I->grabFromCurrentUrl();
//表示エリア：全国エリアのみチェック
//リンク先URL：全国任意両方入力
$I->amGoingTo('任意エリアのチェックを外す');
$I->uncheckOption($otherArea->area_name);
$I->wait(2);

$I->amGoingTo('登録する');
$I->click('変更する');
$I->wait(3);
$I->see('ウィジェットデータを変更してもよろしいですか？');
$I->click('OK');
$I->wait(2);

$I->amGoingTo('もう一度編集画面へ遷移');
$I->amOnPage($updateUrl);
$I->wantTo(5);

$I->expect('表示エリア：全国エリアのチェックが入っている');
$I->seeCheckboxIsChecked($areaComp->nationwideArea->area_name);

$I->expect('表示エリア：任意エリアのチェックが入っていない');
$I->cantSeeCheckboxIsChecked($otherArea->area_name);

$I->expect('リンク先URL：全国エリアにURLが入っている');
$I->seeInField('//input[@name="WidgetData[urls][' . $areaComp->nationwideArea->id . ']"]', $page->attributes['urls'][$areaComp->nationwideArea->id]);

$I->expect('リンク先URL：任意エリアにURLが入っていない');
$I->cantSeeInField('//input[@name="WidgetData[urls][' . $otherArea->id . ']"]', $page->attributes['urls'][$otherArea->id]);

//表示エリア：任意エリアのみチェック
//リンク先URL：全国エリア任意エリア両方入力
$I->amGoingTo('全国エリアのチェックを外す');
$I->uncheckOption($areaComp->nationwideArea->area_name);

$I->amGoingTo('任意エリアのチェックを入れる');
$I->checkOption($otherArea->area_name);

$I->amGoingTo('任意エリアのリンク先URLにURLを入力');
$page->fillUrlsAndRemember($otherArea->id, $baseUrl . str_repeat('n', 1999 - strlen($baseUrl)));

$I->amGoingTo('登録する');
$I->click('変更する');
$I->wait(3);
$I->see('ウィジェットデータを変更してもよろしいですか？');
$I->click('OK');
$I->wait(2);

$I->amGoingTo('もう一度編集画面へ遷移');
$I->amOnPage($updateUrl);
$I->wantTo(5);

$I->expect('表示エリア：全国エリアのチェックが入っていない');
$I->cantSeeCheckboxIsChecked($areaComp->nationwideArea->area_name);

$I->expect('表示エリア：任意エリアのチェックが入っている');
$I->seeCheckboxIsChecked($otherArea->area_name);

$I->expect('リンク先URL：全国エリアにURLが入っていない');
$I->cantSeeInField('//input[@name="WidgetData[urls][' . $areaComp->nationwideArea->id . ']"]', $page->attributes['urls'][$areaComp->nationwideArea->id]);

$I->expect('リンク先URL：任意エリアにURLが入っている');
$I->seeInField('//input[@name="WidgetData[urls][' . $otherArea->id . ']"]', $page->attributes['urls'][$otherArea->id]);

//表示エリア：全国エリア任意エリア両方チェック
//リンク先URL：入力なし

$I->amGoingTo('全国エリアのチェックを入れる');
$I->checkOption($areaComp->nationwideArea->area_name);

$I->amGoingTo('任意エリアのリンク先URLを空に');
$page->fillUrlsAndRemember($otherArea->id, '');

$I->amGoingTo('登録する');
$I->click('変更する');
$I->wait(3);
$I->see('ウィジェットデータを変更してもよろしいですか？');
$I->click('OK');
$I->wait(2);

$I->amGoingTo('もう一度編集画面へ遷移');
$I->amOnPage($updateUrl);
$I->wantTo(5);

$I->expect('表示エリア：全国エリアのチェックが入っている');
$I->seeCheckboxIsChecked($areaComp->nationwideArea->area_name);
$I->expect('表示エリア：任意エリアのチェックが入っている');
$I->seeCheckboxIsChecked($otherArea->area_name);
$I->expect('リンク先URL：両方空');
foreach ([$areaComp->nationwideArea, $otherArea] as $model) {
    /* @var Area $model */
    $I->seeInField('//input[@name="WidgetData[urls][' . $model->id . ']"]', '');
}

//表示エリア：全国エリアのみチェック
//リンク先URL：入力なし
$I->amGoingTo('任意エリアのチェックを外す');
$I->uncheckOption($otherArea->area_name);

$I->amGoingTo('登録する');
$I->click('変更する');
$I->wait(3);
$I->see('ウィジェットデータを変更してもよろしいですか？');
$I->click('OK');
$I->wait(2);

$I->amGoingTo('もう一度編集画面へ遷移');
$I->amOnPage($updateUrl);
$I->wait(5);

$I->expect('表示エリア：全国エリアのチェックが入っている');
$I->seeCheckboxIsChecked($areaComp->nationwideArea->area_name);

$I->expect('表示エリア：任意エリアのチェックが入っていない');
$I->cantSeeCheckboxIsChecked($otherArea->area_name);

//表示エリア：任意エリアのみチェック
//リンク先URL：入力なし
$I->amGoingTo('全国エリアのチェックを外す');
$I->uncheckOption($areaComp->nationwideArea->area_name);

$I->amGoingTo('任意エリアのチェックを入れる');
$I->checkOption($otherArea->area_name);

$I->amGoingTo('登録する');
$I->click('変更する');
$I->wait(3);
$I->see('ウィジェットデータを変更してもよろしいですか？');
$I->click('OK');
$I->wait(2);

$I->amGoingTo('もう一度編集画面へ遷移');
$I->amOnPage($updateUrl);
$I->wantTo(5);

$I->expect('表示エリア：全国エリアのチェックが入っていない');
$I->cantSeeCheckboxIsChecked($areaComp->nationwideArea->area_name);

$I->expect('表示エリア：任意エリアのチェックが入っている');
$I->seeCheckboxIsChecked($otherArea->area_name);

// 動画タグ検証 ////////////////////////////////////////////////////////////////////////////////////////////////////////
$widget->element1 = Widget::ELEMENT_MOVIE;
$widget->element2 = Widget::ELEMENT_HIDE;
$widget->element3 = Widget::ELEMENT_HIDE;
$widget->update(false);

$I->amGoingTo('動画タグを持つwidgetのcreate画面を表示');
$page = WidgetDataRegisterPage::openBy($I); //turn back to create page
$I->selectOption('#widgetId', $widget->widget_name);
$I->wait(3);

$I->amGoingTo('動画タグのclientValidation検証');
$page->fillInputAndRemember('movieTag', str_repeat('a', 256));
$page->checkValidate('movietag', '動画タグは255文字以下で入力してください。');

$page->fillInputAndRemember('movieTag', '');
$page->checkValidate('movietag', '動画タグは必須項目です。');

$I->amGoingTo('動画タグに限界文字数を登録');
$page->fillInputAndRemember('movieTag', str_repeat('a', 255));
$page->checkValidate('movietag', null);

$I->amGoingTo('他のinputを適当に埋める');
$I->checkOption($areaComp->nationwideArea->area_name);
$page->fillInputAndRemember('disp_start_date', '2017/06/23');
$page->fillInputAndRemember('disp_end_date', '2017/06/24');
$page->fillInputAndRemember('sort', 99);
$page->fillRadioAndRemember('valid_chk', WidgetData::VALID);

$I->amGoingTo('登録する');
$I->click('登録する');
$I->wait(3);
$I->see('ウィジェットデータを登録してよろしいですか？');
$I->click('OK');
$I->wait(2);

$I->amGoingTo('update画面へアクセス');
$I->click('ウィジェットデータ一覧画面へ');
$I->wait(5);
$I->click('この条件で表示する');
$I->wait(5);
$page->clickActionColumn(1, 1);
$I->wait(5);

$I->amGoingTo('動画タグに入力したものが表示されている');
$I->seeInField('//input[@name="WidgetData[movieTag]"]', $page->attributes['movieTag']);

(new WidgetFixture())->initTable();
(new WidgetDataFixture())->initTable();
(new WidgetDataAreaFixture())->initTable();
