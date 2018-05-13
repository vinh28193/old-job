<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/06/22
 * Time: 16:04
 */
use app\models\manage\ManageMenuMain;
use app\models\manage\Widget;
use app\models\manage\WidgetLayout;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\widget\WidgetPage;
use tests\codeception\fixtures\WidgetFixture;
use yii\helpers\ArrayHelper;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// fixture dataが下記の条件を満たすことに依存
// ・どのlayoutにも結びついていないwidgetが存在しないこと
// ・widget_layout_no=2に1つ以上widgetが紐づいていること
// ・widget_layout_no=6に2つ以上widgetが紐づいていること
(new WidgetFixture())->initTable();
// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/widget/index');
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

/** @var WidgetLayout[] $widgetLayouts */
$widgetLayouts = ArrayHelper::index(WidgetLayout::find()->innerJoinWith('widget')->all(), 'widget_layout_no');
// テストで扱うwidgetのlayoutのno
$fromLayoutNo = 2;
// テストで移動する先のlayoutのno
$toLayoutNo = 6;
// テストで移動する前のposition
$fromWidgetPosition = 2;
// テストで移動する先のposition
$toWidgetPosition = 3;
// テストで移動する先のlayoutのwidget数
$numberOfWidget = count($widgetLayouts[$toLayoutNo]->widget);
// テストで扱うwidget
$widget = $widgetLayouts[$fromLayoutNo]->widget[0];

// 権限別アクセステスト ////////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('ウィジェット設定画面のテスト');
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
        /* @var $page WidgetPage */
        $I->amOnPage($page->getUrl());
    } else {
        $page = WidgetPage::openBy($I);
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
$page = WidgetPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->title);

// 設定テスト //////////////////////////////////////////////////////////////////////////////////////////////////////////
$I->amGoingTo("widgetLayoutNo{$fromLayoutNo}の{$widget->widget_name}を「使用しないウィジェット」に移動");
$page->moveWidget($widget, null);
$page->seeWidget($widget, null);

$I->amGoingTo('任意のwidgetの設定をクリック');
$page->clickSettingWidget($widget);
$I->wait(3);

$I->expect('widgetの設定モーダルが表示される');
$I->see('変更', '//*[@id="modal-widget"]/div/div/div[1]');

//----------------------
// ウィジェット名clientValidationの確認
//----------------------
$I->amGoingTo('ウィジェット名を空にする');
$page->fillInputAndRemember('widget_name', '');

$I->expect('必須validationがかかる');
$page->checkValidate('widget_name', 'ウィジェット名は必須項目です。');

$I->amGoingTo('変更ボタンを押す');
$I->click('//*[@id="updateButton"]');
$I->wait(3);

$I->expect('何も起きない');
$I->see('変更', '//*[@id="modal-widget"]/div/div/div[1]');

$I->amGoingTo('ウィジェット名に256文字入力する');
$page->fillInputAndRemember('widget_name', str_repeat('a', 256));

$I->expect('文字数上限validationがかかる');
$page->checkValidate('widget_name', 'ウィジェット名は255文字以下で入力してください。');

$I->amGoingTo('変更ボタンを押す');
$I->click('//*[@id="updateButton"]');
$I->wait(3);

$I->expect('何も起きない');
$I->see('変更', '//*[@id="modal-widget"]/div/div/div[1]');

$I->amGoingTo('ウィジェット名に255文字入力する');
$page->fillInputAndRemember('widget_name', str_repeat('a', 255));

$I->expect('validationが成功する');
$page->checkValidate('widget_name');
//----------------------
// ウィジェット表示パターン毎の表示
//----------------------

$I->amGoingTo('ウィジェット表示パターンでパターン3～6を選択');
$showCommonPatterns = [
    Widget::WIDGET_DATA_PATTERN_3,
    Widget::WIDGET_DATA_PATTERN_4,
    Widget::WIDGET_DATA_PATTERN_5,
    Widget::WIDGET_DATA_PATTERN_6,
];
foreach ($showCommonPatterns as $showCommonPattern) {
    $I->amGoingTo("ウィジェット表示パターンでパターン{$showCommonPattern}を選択");
    $page->selectDropdownAndRemember('widgetDataPattern', $showCommonPattern);
    $I->expect('表示スタイル（SP版）が表示されない');
    $I->dontSee('表示スタイル（SP版）', '//*[@id="widgetForm-style_sp-tr"]/th/div/label');
    $I->expect('ウィジェットデータ数（PC版）（SP版）共に表示される');
    $I->see('ウィジェットデータ数（PC版）', '//*[@id="widgetForm-data_per_line_pc-tr"]/th/div/label');
    $I->see('ウィジェットデータ数（SP版）', '//*[@id="widgetForm-data_per_line_sp-tr"]/th/div/label');
}

$I->amGoingTo('ウィジェット表示パターンでパターン7を選択');
$page->selectDropdownAndRemember('widgetDataPattern', Widget::WIDGET_DATA_PATTERN_7);
$I->expect('表示スタイル（SP版）が表示されない');
$I->dontSee('表示スタイル（SP版）', '//*[@id="widgetForm-style_sp-tr"]/th/div/label');
$I->expect('ウィジェットデータ数（PC版）（SP版）共に表示されない');
$I->dontSee('ウィジェットデータ数（PC版）', '//*[@id="widgetForm-data_per_line_pc-tr"]/th/div/label');
$I->dontSee('ウィジェットデータ数（SP版）', '//*[@id="widgetForm-data_per_line_sp-tr"]/th/div/label');

$I->amGoingTo('ウィジェット表示パターンでパターン1～2を選択');

$showShowSpPatterns = [
    Widget::WIDGET_DATA_PATTERN_1,
    Widget::WIDGET_DATA_PATTERN_2,
];
foreach ($showShowSpPatterns as $showShowSpPattern) {
    $I->amGoingTo("ウィジェット表示パターンでパターン{$showShowSpPattern}を選択");
    $page->selectDropdownAndRemember('widgetDataPattern', $showShowSpPattern);
    $I->expect('表示スタイル（SP版）が表示される');
    $I->see('表示スタイル（SP版）', '//*[@id="widgetForm-style_sp-tr"]/th/div/label');
    $I->expect('ウィジェットデータ数（PC版）（SP版）共に表示される');
    $I->see('ウィジェットデータ数（PC版）', '//*[@id="widgetForm-data_per_line_pc-tr"]/th/div/label');
    $I->see('ウィジェットデータ数（SP版）', '//*[@id="widgetForm-data_per_line_sp-tr"]/th/div/label');
}

//----------------------
// 残り項目変更
//----------------------
$I->amGoingTo('表示スタイル（SP版）で画面を左に表示を選択');
$page->selectDropdownAndRemember('style_sp', $widget->style_sp == 1 ? 2 : 1);
// to Mgreeny 以下、現在選択されているものと別の項目を選択してください

$I->amGoingTo('ウィジェットの見出し表示を変更');
$page->fillRadioAndRemember('is_disp_widget_name', $widget->is_disp_widget_name ? 0 : 1);

$I->amGoingTo('ウィジェット内カラム数(PC版)を変更');
$page->selectDropdownAndRemember('data_per_line_pc', $widget->data_per_line_pc == 1 ? 4 : 1);

$I->amGoingTo('ウィジェット内カラム数(SP版)を変更');
$page->selectDropdownAndRemember('data_per_line_sp', $widget->data_per_line_sp == 1 ? 2 : 1);

//----------------------
// 更新して表示検証
//----------------------
$I->amGoingTo('変更ボタンを押す');
$I->click('//*[@id="updateButton"]');
$I->wait(3);

$I->expect('モーダルが閉じる');
$I->dontSee('変更', '//*[@id="modal-widget"]/div/div/div[1]');

$I->expect("{$widget->widget_name}が使用しないwidgetに入っている（親画面が更新されていない）");
$page->seeWidget($widget, null);

$I->expect('更新完了のメッセージが出ている');
$I->see('更新が完了しました。');

$I->amGoingTo('もう一度変更モーダルを開く');
$page->clickSettingWidget($widget);
$I->wait(3);

$I->expect('表示スタイル（SP版）が表示されている');
$I->see('表示スタイル（SP版）', '//*[@id="widgetForm-style_sp-tr"]/th/div/label');

$I->expect('先ほどした変更が反映されている');
$I->seeInField("//input[@name='Widget[widget_name]']", $page->attributes['widget_name']);
$page->checkOptionSelected('select', 'widgetDataPattern');
$page->checkOptionSelected('select', 'style_sp');
$page->checkOptionSelected('input', 'is_disp_widget_name');
$page->checkOptionSelected('select', 'data_per_line_pc');
$page->checkOptionSelected('select', 'data_per_line_sp');

//----------------------
// ウィジェットパターンと表示スタイル
//----------------------
$I->amGoingTo('ウィジェット表示パターンでパターン3を選択');
$page->selectDropdownAndRemember('widgetDataPattern', Widget::WIDGET_DATA_PATTERN_3);

$I->amGoingTo('変更ボタンを押す');
$I->click('//*[@id="updateButton"]');
$I->wait(3);

$I->amGoingTo('もう一度設定モーダルを開く');
$page->clickSettingWidget($widget);
$I->wait(3);

$I->amGoingTo('ウィジェット表示パターンがパターン3になっている');
$page->checkOptionSelected('select', 'widgetDataPattern');

$I->amGoingTo('ウィジェット表示パターンでパターン7を選択');
$page->selectDropdownAndRemember('widgetDataPattern', Widget::WIDGET_DATA_PATTERN_7);

$I->amGoingTo('変更ボタンを押す');
$I->click('//*[@id="updateButton"]');
$I->wait(3);

$I->amGoingTo('もう一度設定モーダルを開く');
$page->clickSettingWidget($widget);
$I->wait(3);

$I->expect('表示スタイル（SP版）が表示されない');
$I->dontSee('表示スタイル（SP版）', '//*[@id="widgetForm-style_sp-tr"]/th/div/label');

$I->expect('ウィジェットデータ数（PC版）（SP版）共に表示されない');
$I->dontSee('ウィジェットデータ数（PC版）', '//*[@id="widgetForm-data_per_line_pc-tr"]/th/div/label');
$I->dontSee('ウィジェットデータ数（SP版）', '//*[@id="widgetForm-data_per_line_sp-tr"]/th/div/label');

$I->amGoingTo('ウィジェット表示パターンでパターン1を選択');
$page->selectDropdownAndRemember('widgetDataPattern', Widget::WIDGET_DATA_PATTERN_1);

$I->expect('表示スタイル（SP版）が表示される');
$I->see('表示スタイル（SP版）', '//*[@id="widgetForm-style_sp-tr"]/th/div/label');

$I->expect('表示スタイル（SP版）は画像を上に表示が選択されている');
$page->selectDropdownAndRemember('style_sp', Widget::STYLE_SP_1);

$I->amGoingTo('モーダルを閉じる');
$I->click('//*[@id="modal-widget"]/div/div/div[1]/button');
// 並び替えテスト //////////////////////////////////////////////////////////////////////////////////////////////////////
$I->amGoingTo('変更ボタンを押す');
$I->click('//*[@id="form"]/p/button[3]');
$I->wait(5);
$I->expect('完了メッセージが表示される');

$I->see('ウィジェットの並び順の更新が完了しました。');

$I->expect("{$widget->widget_name}が使用しないwidgetに入っている");
$page->seeWidget($widget, null);

$I->amGoingTo("{$widget->widget_name}をWidgetLayoutNo{$toLayoutNo}の{$fromWidgetPosition}番目に移動させる");
$page->moveWidget($widget, $toLayoutNo, $fromWidgetPosition - 1);

$I->amGoingTo('変更ボタンを押す');
$I->click('//*[@id="form"]/p/button[3]');
$I->wait(5);

$I->expect('完了メッセージが表示される');
$I->see('ウィジェットの並び順の更新が完了しました。');

$I->expect('変更が反映されている');
$page->seeWidget($widget, $toLayoutNo, $fromWidgetPosition);

$I->amGoingTo('widgetLayout内でウィジェットを移動させる');
$page->moveWidget($widget, $toLayoutNo, $toWidgetPosition);

$I->amGoingTo('変更ボタンを押す');
$I->click('//*[@id="form"]/p/button[3]');
$I->wait(5);

$I->expect('完了メッセージが表示される');
$I->see('ウィジェットの並び順の更新が完了しました。');

$I->expect('変更が反映されている');
$page->seeWidget($widget, $toLayoutNo, $toWidgetPosition);

// todo next プレビューテスト ////////////////////////////////////////////////////////////////////////////////////////////////////
