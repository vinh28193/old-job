<?php

use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\PrefDistMaster;
use app\models\manage\SearchkeyMaster;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\searchkey\DistGroupPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// 東京の地域グループの並び順を全て2以上にする
$prefDists = PrefDistMaster::findAll(['sort' => 1]);
foreach ((array)$prefDists as $prefDist) {
    /** @var PrefDistMaster $prefDist */
    $prefDist->sort = 2;
    $prefDist->save();
}

// メニュー情報取得
$menu = SearchkeyMaster::findOne(['table_name' => 'pref_dist_master']);

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('地域グループ検索キーのテスト');
//----------------------
// 運営元でログインして検証画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(2);
$page = DistGroupPage::openBy($I);
$page->go($menu);
$I->wait(2);
$I->seeInTitle($menu->searchkey_name);
$I->see($menu->searchkey_name, 'h1');

$I->amGoingTo('全都道府県が選択可能');
$prefNos = Pref::find()->select('id')->column();
foreach ($prefNos as $k => $prefNo) {
    $i = $k + 2;
    $I->seeElementInDOM("//select[@id='prefdistmaster-pref_id']/option[{$i}][@value='{$prefNo}']");
}

$I->amGoingTo('東京都を選択');
$I->selectOption('#prefdistmaster-pref_id', '東京都');
$I->wait(1);
$I->see('板橋区', 'li');
$I->see('新宿区', 'li');

//----------------------
// 地域グループ新規作成
//----------------------
$I->amGoingTo('地域グループ新規作成');
$page->openModal('地域グループを追加する', $page::CREATE);
// todo client side動作検証
$I->expect('地域グループ名重複チェック');
$page->fillAndRemember('pref_dist_name', '板橋区');
$I->wait(2);
$I->see('地域名で "板橋区" は既に使われています。');
$I->expect('他県の地域グループ名は重複チェック外');
$page->fillAndRemember('pref_dist_name', '川崎市');
$I->wait(2);
$I->cantSee('地域名で "川崎市" は既に使われています。');

$I->expect('正常データ入力');
$page->fillAndRemember('pref_dist_name', '板橋区と新宿区' . time());
$page->fillAndRemember('sort', 1);
$I->selectOption('input[name=PrefDistMaster\\[valid_chk\\]]', 1);
$page->submitModal($page::CREATE);
$I->see('板橋区', 'li');
$I->see('新宿区', 'li');
$page->reload();

//----------------------
// 地域グループ更新
//----------------------
$I->amGoingTo('地域グループ更新');
$page->openModal($page->attributes['pref_dist_name'], $page::UPDATE);
$page->submitModal($page::UPDATE);
$I->see('板橋区', 'li');
$I->see('新宿区', 'li');
$page->reload();

//----------------------
// 地域グループ割り当て
//----------------------
$I->amGoingTo('作ったgroupに板橋区、新宿区を割り当てる');
$I->dragAndDrop('//li[text()="板橋区"]', '//tbody/tr[2]/td/ul');
$I->dragAndDrop('//li[text()="新宿区"]', '//tbody/tr[2]/td/ul');
$I->see('板橋区', '//tbody/tr[2]/td/ul/li[1]');
$I->see('新宿区', '//tbody/tr[2]/td/ul/li[2]');
$I->click('市区町村の割当を確定する');
$I->wait(3);
$I->see('新宿区', '//tbody/tr[2]/td/ul/li[1]');
$I->see('板橋区', '//tbody/tr[2]/td/ul/li[2]');
$I->see('更新が完了しました。', 'p');

$I->amGoingTo('リロードしたらメッセージが消える');
$I->reloadPage();
$I->wait(3);
$I->see('新宿区', '//tbody/tr[2]/td/ul/li[1]');
$I->see('板橋区', '//tbody/tr[2]/td/ul/li[2]');
$I->cantSee('更新が完了しました。', 'p');

$I->amGoingTo('新宿区をどこにも所属させないようにする');
$I->dragAndDrop('li[data-key="13104"]', '//div[@id="fixedBox"]/ul');
$I->see('板橋区', '//tbody/tr[2]/td/ul/li');
$I->see('新宿区', '//div[@id="fixedBox"]/ul/li');
$I->click('市区町村の割当を確定する');
$I->wait(3);
$I->see('板橋区', '//tbody/tr[2]/td/ul/li');
$I->see('新宿区', '//div[@id="fixedBox"]/ul/li');
$I->see('更新が完了しました。', 'p');

$I->reloadPage();
$I->wait(3);
$I->see('板橋区', '//tbody/tr[2]/td/ul/li[1]');
$I->see('新宿区', '//div[@id="fixedBox"]/ul/li');
$I->cantSee('更新が完了しました。', 'p');

//----------------------
// 削除
// WSeleniumとPhantomJSの組み合わせだとwindow.alert|window.confirm|window.promptが使えないため保留
//----------------------
//$I->amGoingTo('地域グループ削除');
//$page->openModal($page->attributes['pref_dist_name'], $page::UPDATE);
//$page->delete();
//$I->see('板橋区', 'li');
//$I->see('新宿区', 'li');
//$page->reload();
