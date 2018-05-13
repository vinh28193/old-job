<?php
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\admin\AdminSearchPage;
use tests\codeception\_pages\manage\ManageLoginPage;
/* @var $scenario Codeception\Scenario */
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);
// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/admin/list');

// 使うmodelを準備
/** @var CorpMaster $corp */
$corp = CorpMaster::find()->one();

/** @var ClientMaster $client */
$client = ClientMaster::find()->one();

// corpが有効であることを担保
$corp->valid_chk = 1;
$corp->save();

// clientが有効かつcorpの管理下であることを担保
$client->valid_chk = 1;
$client->corp_master_id = $corp->id;
$client->save();

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);
// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('管理者検索のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
$page = AdminSearchPage::openBy($I);
$I->wait(1);
$I->seeInTitle($menu->title);
$I->see($menu->title, 'h1');

//----------------------
// キーワード
// todo next 動作検証
//----------------------
$I->wantTo('キーワードの入力ができる');
// キーワードの入力
$I->fillField('#adminmastersearch-searchtext', 'test');
// 入力の値が入っている
$I->seeInField('#adminmastersearch-searchtext', 'test');

//----------------------
// 代理店名
// todo next 動作検証
//----------------------
$I->wantTo('代理店のselect2に値をinput');
$I->click('#adminmastersearch-corp_master_id + span .select2-selection__arrow');
$I->wait(1);
$I->executeJS("$('input.select2-search__field').attr('id', 'groups_input');");
$I->fillField('#groups_input', $corp->corp_name);
$I->wait(1);
// エンターを押す
$I->pressKey('#groups_input', WebDriverKeys::ENTER);
$I->wait(1);

//----------------------
// 掲載企業名
// todo next 動作検証
//----------------------
$I->wantTo('掲載企業のselect2に値をinput');
$I->click('#adminmastersearch-client_master_id + span .select2-selection__arrow');
$I->wait(1);
$I->executeJS("$('input.select2-search__field').attr('id', 'groups_input');");
$I->fillField('#groups_input', $client->client_name);
$I->wait(1);
// エンターを押す
$I->pressKey('#groups_input', WebDriverKeys::ENTER);
$I->wait(1);

//----------------------
// 種別の入力
//----------------------
$I->wantTo('性別の入力ができる');
$I->selectOption('input[name=AdminMasterSearch\\[role\\]]', 'owner_admin');

//----------------------
// 状態の入力
//----------------------
$I->wantTo('状態の入力ができる');
$I->selectOption('input[name=AdminMasterSearch\\[valid_chk\\]]', 0);

//----------------------
// 検索する
// todo next 動作検証
//----------------------
$I->click('この条件で表示する');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// クリアする
// todo next 動作検証
////----------------------
$I->click('クリア');
$I->wait(2);
$I->seeInTitle($menu->title);

//----------------------
// ソートする
// todo 動作検証
//----------------------
$I->amGoingTo('ソートする');
$I->click('//thead/tr/th[2]');
$I->wait(2);
$I->seeInTitle($menu->title);
