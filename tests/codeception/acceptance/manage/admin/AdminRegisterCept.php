<?php
use app\models\manage\ManageMenuMain;
use app\models\manage\AdminColumnSet;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\fixtures\AdminMasterFixture;
use tests\codeception\fixtures\AdminColumnSetFixture;
use app\models\manage\CorpMaster;
use app\models\manage\ClientMaster;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

(new AdminMasterFixture())->initTable();
(new AdminColumnSetFixture())->initTable();

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/admin/list');

// 登録するログインID
$now = date('Ymdhis');
$loginId = $now . 'test';

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

//テストデータ取得
/** @var CorpMaster $corp */
$corp = CorpMaster::find()->where(['valid_chk' => 1/**todo 定数クラスができ次第、直す */])->one();
/** @var ClientMaster $client */
$client = ClientMaster::find()->where(['valid_chk' => 1/**todo 定数クラスができ次第、直す */,'corp_master_id'=>$corp->id])->one();
// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('管理者登録変更のテスト');

//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(3);


$I->amGoingTo('管理者一覧ページへ移動');
$I->click($menu->category->name);
$I->wait(3);
$I->click($menu->name);
$I->wait(3);
$I->seeInTitle($menu->name);
$I->see($menu->name, 'h1');


$I->amGoingTo('管理者を登録する');
$I->click('管理者を登録する');
$I->wait(3);
$I->see('管理者の登録', 'h1');

// 種別
$I->selectOption('//*[@id="adminmaster-role"]//input', 'corp_admin');

// 代理店名
$I->click('//*[@id="select2-adminmaster-corp_master_id-container"]');
$I->wait(3);
$I->fillField('//span[@class="select2-search select2-search--dropdown"]/input', $corp->corp_name);
$I->wait(3);
$I->click('//*[@id="select2-adminmaster-corp_master_id-results"]/li[1]');
$I->wait(3);


// 部署名
$I->fillField('//*[@id="adminmaster-name_sei"]', 'テスト部署');

// 担当者名
$I->fillField('//*[@id="adminmaster-name_mei"]', 'テスト担当者');

// ログインID
$I->fillField('//*[@id="adminmaster-login_id"]', $loginId);

// パスワード
$I->fillField('//*[@id="adminmaster-password"]', 'Passw0rd');

// メールアドレス
$I->fillField('//*[@id="adminmaster-mail_address"]', $now . '@example.com');

// オプション1
$I->fillField('//*[@id="adminmaster-option100"]', 'オプション1');

// 状態
$I->selectOption('//*[@id="adminmaster-valid_chk"]//input', 1);

// submit
$I->click('complete');
$I->wait(3);
$I->click('//div[@class="modal-footer"]/button[@class="btn btn-primary"]');
$I->wait(3);
$I->see('管理者情報-完了', '//h1[@class="heading"]');
$I->see('登録完了', 'h1');


$I->amGoingTo('管理者情報一覧ページへ移動');
$I->click('管理者情報一覧へ');
$I->wait(3);
$I->see($menu->title, 'h1');
$I->dontSee($loginId);


$I->amGoingTo('キーワードで検索');
$I->fillField('//*[@id="adminmastersearch-searchtext"]', $loginId);
$I->click('この条件で表示する');
$I->wait(3);
$I->see($menu->title, 'h1');
$I->see($loginId);


$I->amGoingTo('種別を運営元管理者に変更する');
$I->click('//*[@id="grid_id"]/div/table/tbody/tr/td/a[@title="変更"]');
$I->wait(3);
$I->see('管理者の編集', 'h1');
$I->seeInField('//*[@id="adminmaster-login_id"]', $loginId);

// 種別
$I->selectOption('//*[@id="adminmaster-role"]//input', 'owner_admin');

// submit
$I->click('complete');
$I->wait(3);
$I->click('//div[@class="modal-footer"]/button[@class="btn btn-primary"]');
$I->wait(3);
$I->see('管理者情報-完了', '//h1[@class="heading"]');
$I->see('変更完了', 'h1');


$I->amGoingTo('管理者情報一覧ページへ移動');
$I->click('管理者情報一覧へ');
$I->wait(3);
$I->see($menu->title, 'h1');
$I->dontSee($loginId);


$I->amGoingTo('キーワードで検索');
$I->fillField('//*[@id="adminmastersearch-searchtext"]', $loginId);
$I->click('この条件で表示する');
$I->wait(3);
$I->see($menu->title, 'h1');
$I->see($loginId);


$I->amGoingTo('種別を代理店管理者に変更する');
$I->click('//*[@id="grid_id"]/div/table/tbody/tr/td/a[@title="変更"]');
$I->wait(3);
$I->see('管理者の編集', 'h1');
$I->seeInField('//*[@id="adminmaster-login_id"]', $loginId);

// 種別
$I->selectOption('//*[@id="adminmaster-role"]//input', 'corp_admin');

// 代理店名
$I->click('//*[@id="select2-adminmaster-corp_master_id-container"]');
$I->wait(3);
$I->fillField('//span[@class="select2-search select2-search--dropdown"]/input', $corp->corp_name);
$I->wait(3);
$I->click('//*[@id="select2-adminmaster-corp_master_id-results"]/li[1]');
$I->wait(3);

// submit
$I->click('complete');
$I->wait(3);
$I->click('//div[@class="modal-footer"]/button[@class="btn btn-primary"]');
$I->wait(3);
$I->see('管理者情報-完了', '//h1[@class="heading"]');
$I->see('変更完了', 'h1');


$I->amGoingTo('管理者情報一覧ページへ移動');
$I->click('管理者情報一覧へ');
$I->wait(3);
$I->see($menu->title, 'h1');
$I->dontSee($loginId);


$I->amGoingTo('キーワードで検索');
$I->fillField('//*[@id="adminmastersearch-searchtext"]', $loginId);
$I->click('この条件で表示する');
$I->wait(3);
$I->see($menu->title, 'h1');
$I->see($loginId);


$I->amGoingTo('種別を掲載企業管理者に変更する');
$I->click('//*[@id="grid_id"]/div/table/tbody/tr/td/a[@title="変更"]');
$I->wait(3);
$I->see('管理者の編集', 'h1');
$I->seeInField('//*[@id="adminmaster-login_id"]', $loginId);

// 種別
$I->selectOption('//*[@id="adminmaster-role"]//input', 'client_admin');

// 代理店名
$I->click('//*[@id="select2-adminmaster-corp_master_id-container"]');
$I->wait(3);
$I->fillField('//span[@class="select2-search select2-search--dropdown"]/input', '運営');
$I->wait(3);
$I->click('//*[@id="select2-adminmaster-corp_master_id-results"]/li[1]');
$I->wait(3);

// submit
$I->click('complete');
$I->wait(3);
$I->click('//div[@class="modal-footer"]/button[@class="btn btn-primary"]');
$I->wait(3);
$I->see('管理者情報-完了', '//h1[@class="heading"]');
$I->see('変更完了', 'h1');

// 非表示項目のチェック
$I->amGoingTo('管理者情報一覧ページへ移動');
$I->click('管理者情報一覧へ');
$I->wait(3);
$I->wantTo('非表示項目のチェック');
$columnSet = AdminColumnSet::findOne(['column_name' => 'tel_no']);
$columnSet->valid_chk = 0;
$columnSet->save();
$I->click('管理者を登録する');
$I->wait(3);
$I->dontSeeElement('#adminmaster-tel_no');
