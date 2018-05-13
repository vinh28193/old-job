<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;

/* @var $scenario Codeception\Scenario */

// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
// 設定のため運営元権限を代入
Yii::$app->user->identity = Manager::findIdentity(1);

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/widget-data/list');

// テスター準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('ウィジェット検索画面のテスト');
//----------------------
// 運営元でログインしてlist画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(1);
// todo PageクラスでopenBy()する（Pageクラスが無い場合は作ってください）
$I->wait(1);
$I->seeInTitle($menu->title);

// todo キーワード、エリア、コンテンツ種類、ウィジェット名、状態の入力

//----------------------
// 日付の入力
//----------------------
$I->wantTo('日付入力が独立していて、正しいフォーマットで入力できる');
// from入力
$I->fillField('#widgetdatasearch-startfrom', '2016/10/19');
// fromにはちゃんと入っており、toには自動では入っていない
$I->seeInField('#widgetdatasearch-startfrom', '2016/10/19');
$I->seeInField('#widgetdatasearch-startto', '');
// to入力
$I->fillField('#widgetdatasearch-startto', '2016/10/18');
// それぞれ入力したままの値が入っている
$I->seeInField('#widgetdatasearch-startfrom', '2016/10/19');
$I->seeInField('#widgetdatasearch-startto', '2016/10/18');

$I->click('この条件で表示する');
$I->wait(2);
$I->seeInTitle($menu->title);

$I->click('クリア');
$I->wait(2);
$I->seeInTitle($menu->title);

// todo 新規と変更画面への遷移テストとかソートテストとかポップアップテストとかチェックボックステストとか削除テストとか
