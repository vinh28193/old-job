<?php

use app\models\manage\ManageMenuMain;
use app\models\manage\searchkey\Area;
use app\models\manage\SearchkeyMaster;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\settings\searchkey\AreaPage;

/* @var $scenario Codeception\Scenario */
/* @var $this Codeception\TestCase\Cept */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// fixtureをload
(new \tests\codeception\fixtures\AreaFixture())->load();
(new \tests\codeception\fixtures\PrefFixture())->load();

// todo next loadするfixtureの条件をチェック

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/area/list');
$searchKey = SearchkeyMaster::findOne(['table_name' => 'area']);

// エリア名
$areaName = date('Ymdhis') . 'エリア';

// エリアインスタンス取得(pref relationはsort順にorderされています)
/** @var Area[] $areas */
$areas = Area::find()->with('pref')->orderBy(['sort' => SORT_ASC])->all();
$firstArea = $areas[0];
$firstPrefs = $firstArea->pref;
$secondArea = $areas[1];
$secondPrefs = $secondArea->pref;

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('エリア検索キーのテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でログイン');
$loginPage->login('admin01', 'admin01');
$I->amOnPage("/manage/secure/settings/list");
$page = AreaPage::openBy($I);
$I->wait(2);
$page->go($searchKey);

$I->amGoingTo('更新完了メッセージ等が最初から見えてしまっていないか検証');
$I->dontSee('更新が完了しました。');
$I->dontSee('エリアの並び順、都道府県の割当と並び順の更新が完了しました。');

$I->amGoingTo('DBの状態と初期表示が合致しているか検証');
$areaSort = 1;
foreach ($areas as $area) {
    $prefSort = 1;
    $I->see($area->area_name , "//form/ul/li[{$areaSort}]/div/div[1]/a");
    if ($area->valid_chk) {
        $I->see('公開中' , "//form/ul/li[{$areaSort}]/div/div[1]/span");
    } else {
        $I->see('非公開' , "//form/ul/li[{$areaSort}]/div/div[1]/span");
    }
    foreach ($area->pref as $pref) {
        $I->see($pref->pref_name , "//form/ul/li[{$areaSort}]/div/div[2]/ul/li[{$prefSort}]/a");
        $prefSort++;
    }
    $areaSort++;
}

//----------------------
// 割り当て変更及び並び替え
//----------------------
// 1番目のエリアの都道府県を2番目のエリアへ移動
$page->movePref($firstPrefs[0], $secondArea);
// 2番目のエリアの都道府県を1番目のエリアへ移動
$page->movePref($secondPrefs[0], $firstArea);
// 1番目のエリアの1番最初にある（sort=1は移動したのでsort=2の）都道府県を1番最後に移動
$lastPrefKey = count($firstPrefs) - 1;
$page->movePref($firstPrefs[1], $firstPrefs[$lastPrefKey]);
// 2番目のエリアの1番最初にある（sort=1は移動したのでsort=2の）都道府県を欄外に移動
$page->movePref($secondPrefs[1], null);
// 2番目のエリアを7番目に移動する
$page->moveArea($secondArea, 7);

$I->amGoingTo('並びと割り当てを確定');
$I->click('エリアの並び順、都道府県の割当と並び順を確定する');
$I->wait(3);

$I->amGoingTo('割り当て結果の適用を確認する');
$I->see('エリアの並び順、都道府県の割当と並び順の更新が完了しました。');

$I->expect("{$firstPrefs[0]->pref_name}が{$secondArea->area_name}に移動している");
$I->seeInDatabase('pref', ['id' => $firstPrefs[0]->id, 'area_id' => $secondArea->id]);
$I->expect("{$secondPrefs[0]->pref_name}が{$firstArea->area_name}に移動している");
$I->seeInDatabase('pref', ['id' => $secondPrefs[0]->id, 'area_id' => $firstArea->id]);
$I->expect("{$firstPrefs[1]->pref_name}が一番最後に移動している");
$I->seeInDatabase('pref', ['id' => $firstPrefs[1]->id, 'sort' => $firstPrefs[$lastPrefKey]->sort]);
$I->expect("{$secondPrefs[1]->pref_name}がエリアの割り当てを外されている");
$I->seeInDatabase('pref', ['id' => $secondPrefs[1]->id, 'area_id' => null]);
$I->expect("{$secondArea->area_name}が7番目に移動している");
$I->seeInDatabase('area', ['id' => $secondArea->id, 'sort' => 7]);
$page->reload();

//----------------------
// エリア検索キー設定の変更
//----------------------
$I->amGoingTo("{$firstArea->area_name}の変更モーダルを開く");
$I->click("//form/ul/li[1]/div/div[1]/a");
$I->wait(2);

$I->amGoingTo('初期表示を検証');
$I->canSeeInField('#area-area_name', $firstArea->area_name);
$I->see(Yii::$app->request->userHost, '//tr[@id="searchkeyForm-area_name-tr"]/td/div');
$I->canSeeInField('#area-area_dir', $firstArea->area_dir);
$radio = $firstArea->valid_chk ? 1 : 2;
$I->canSeeCheckboxIsChecked("//div[@id='area-valid_chk']/label[{$radio}]/input");

// 必須チェック
$I->amGoingTo('clientValidationを検証');
$I->fillField('//*[@id="area-area_name"]', '');
$I->fillField('//*[@id="area-area_dir"]', '');
$I->see('エリア名は必須項目です', '//tr[@id="searchkeyForm-area_name-tr"]/td/div/div');
$I->see('エリアURL名は必須項目です。', '//tr[@id="searchkeyForm-area_dir-tr"]/td/div/div');

// 文字数上限
$I->fillField('//*[@id="area-area_name"]', str_repeat('a', 51));
$I->fillField('//*[@id="area-area_dir"]', str_repeat('a', 51));
$I->wait(1);
$I->see('エリア名は50文字以下で入力してください。', '//tr[@id="searchkeyForm-area_name-tr"]/td/div/div');
$I->see('エリアURL名は50文字以下で入力してください。', '//tr[@id="searchkeyForm-area_dir-tr"]/td/div/div');

// URLの半角英数チェック
$I->fillField('//*[@id="area-area_dir"]', 'あああ');
$I->wait(1);
$I->see('エリアURL名は半角文字にしてください。', '//tr[@id="searchkeyForm-area_dir-tr"]/td/div/div');

$I->amGoingTo('正しい値を入れて無効にして登録');
$I->fillField('//*[@id="area-area_name"]', str_repeat('a', 50));
$I->fillField('//*[@id="area-area_dir"]', str_repeat('a', 50));
$I->selectOption('//*[@id="area-valid_chk"]//input', $firstArea->valid_chk ? 0 : 1);
$I->wait(1);

$I->click('//div[@class="modal-footer"]/button[text()="変更"]');
$I->wait(3);
$I->see($searchKey->searchkey_name, 'h1');
$I->see('更新が完了しました。');
$page->reload();

$I->amGoingTo('有効なエリアを0にしようとしたときのエラーチェック');
$I->click("//form/ul/li[7]/div/div[1]/a");
$I->wait(2);
$I->selectOption('//*[@id="area-valid_chk"]//input', 0);
$I->wait(1);
$I->see('エリアは全て無効にできません。', '//tr[@id="searchkeyForm-valid_chk-tr"]/td/div/div');
$I->click('//button[@class="close"]');

$I->amGoingTo('地域グループキー設定画面への遷移');
$I->click($firstPrefs[0]->pref_name);
$I->wait(3);
$I->see(SearchkeyMaster::findOne(['table_name' => 'pref_dist_master'])->searchkey_name, 'h1');
$I->seeOptionIsSelected('#prefdistmaster-pref_id', $firstPrefs[0]->pref_name);