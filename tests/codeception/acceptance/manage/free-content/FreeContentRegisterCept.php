<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\free_content\FreeContentRegisterPage;

/* @var $scenario Codeception\Scenario */

$menu = ManageMenuMain::findFromRoute('/manage/secure/free-content/list');

//管理者でログイン
$manager = Manager::findIdentity(1);
Yii::$app->user->identity = $manager;

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);
$loginPage = ManageLoginPage::openBy($I);
$loginPage->login('admin01', 'admin01');
$I->wait(2);

// ---------------
// 新規登録の検証
// ---------------

// 画像あり。テキストあり
moveOnCreatePage($I);
fillForm($I);
uploadImage($I, 0, 'test.png');
$I->click('要素追加');
fillTextInput($I, 1);
$I->wait(1);
register($I);

// 画像のみ
moveOnCreatePage($I);
fillForm($I);
uploadImage($I, 0, 'test.png');
register($I);

// 画像あり。テキスト不正
moveOnCreatePage($I);
fillForm($I);
uploadImage($I, 0, 'test.png');
$I->wait(1);
$I->click('要素追加');
$I->wantTo('テキストを入力');
$I->selectOption('#freecontentelementform-1-displayitem', 'テキスト');
$I->fillField('#freecontentelementform-1-text', str_repeat('a', 5001));
$I->wait(1);
$I->selectOption('#freecontentelementform-1-displayitem', '画像');
$I->attachFile('#freecontentelementform-1-imgfile', 'test.png');
register($I);

// 画像なし。テキストあり
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-1-displayitem', 'テキスト');
$I->fillField('#freecontentelementform-1-text', 'aaa');
$I->wait(1);
$I->click('登録する');
$I->expect('画像のvalidation');
$I->see('画像は必須項目です。', '.error-block');

//　画像なし。テキストなし
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-1-displayitem', 'テキスト');
$I->wait(1);
$I->click('登録する');
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像なし。テキスト不正
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-1-displayitem', 'テキスト');
$I->fillField('#freecontentelementform-1-text', str_repeat('a', 5001));
$I->wait(1);
$I->wantTo('ソート');
dragAndDropOn($I, 1, 0);
$I->click('登録する');
$I->wait(1);
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像不正。テキストあり
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像');
// 不正な拡張子のファイルを添付
$I->attachFile('#freecontentelementform-0-imgfile', 'job.csv');
$I->click('要素追加');
fillTextInput($I, 1);
$I->click('登録する');
$I->wait(1);
$I->expect('画像のvalidation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');

// 画像不正。テキストなし
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像');
// 不正な拡張子のファイルを添付
$I->attachFile('#freecontentelementform-0-imgfile', 'job.csv');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-1-displayitem', 'テキスト');
$I->wantTo('ソート');
dragAndDropOn($I, 1, 0);
$I->click('登録する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像不正。テキスト不正
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像');
// 不正な拡張子のファイルを添付
$I->attachFile('#freecontentelementform-0-imgfile', 'job.csv');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-1-displayitem', 'テキスト');
$I->fillField('#freecontentelementform-1-text', str_repeat('a', 5001));
dragAndDropOn($I, 1, 0);
$I->click('登録する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

/* 画像とテキスト */

// 画像あり。テキストあり
moveOnCreatePage($I);
fillForm($I);
fillTextAndUploadImage($I, 0, '画像が左', 'test.png');
register($I);

// 画像のみ
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', '画像が左');
$I->attachFile('#freecontentelementform-0-imgfile', 'test.png');
$I->click('登録する');
$I->expect('vali');
$I->see('テキストは必須項目です。', '.error-block');

// 画像あり。テキスト不正
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', '画像が左');
$I->attachFile('#freecontentelementform-0-imgfile', 'test.png');
$I->fillField('#freecontentelementform-0-text', str_repeat('a', 5001));
$I->click('登録する');
$I->wait(1);
$I->expect('vali');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像なし。テキストあり
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', '画像が左');
$I->fillField('#freecontentelementform-0-text', 'aaa');
$I->click('登録する');
$I->wait(1);
$I->expect('vali');
$I->see('画像は必須項目です。', '.error-block');

//　画像なし。テキストなし
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', '画像が左');
$I->click('登録する');
$I->wait(1);
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像なし。テキスト不正
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', '画像が左');
$I->fillField('#freecontentelementform-0-text', str_repeat('a', 5001));
$I->click('登録する');
$I->expect('validation');
$I->wait(1);
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像不正。テキストあり
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', '画像が左');
$I->attachFile('#freecontentelementform-0-imgfile', 'job.csv');
$I->fillField('#freecontentelementform-0-text', 'aaa');
$I->click('登録する');
$I->wait(1);
$I->expect('ファイルのvalidation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');

// 画像不正。テキストなし
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', '画像が左');
$I->attachFile('#freecontentelementform-0-imgfile', 'job.csv');
$I->click('登録する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像不正。テキスト不正
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', '画像が左');
$I->attachFile('#freecontentelementform-0-imgfile', 'job.csv');
$I->fillField('#freecontentelementform-0-text', str_repeat('a', 5001));
$I->click('登録する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// *************************************************************** //

/* テキストが左 */

// 画像あり。テキストあり
moveOnCreatePage($I);
fillForm($I);
fillTextAndUploadImage($I, 0, 'テキストが左', 'picture.jpg');
register($I);

// 画像のみ
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-0-imgfile', 'test.png');
$I->click('登録する');
$I->wait(1);
$I->expect('vali');
$I->see('テキストは必須項目です。', '.error-block');

// 画像あり。テキスト不正
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-0-imgfile', 'test.png');
$I->fillField('#freecontentelementform-0-text', str_repeat('a', 5001));
$I->click('登録する');
$I->wait(1);
$I->expect('vali');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像なし。テキストあり
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', 'テキストが左');
$I->fillField('#freecontentelementform-0-text', 'aaa');
$I->click('登録する');
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');

//　画像なし。テキストなし
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', 'テキストが左');
$I->click('登録する');
$I->wait(1);
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像なし。テキスト不正
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', 'テキストが左');
$I->fillField('#freecontentelementform-0-text', str_repeat('a', 5001));
$I->click('登録する');
$I->wait(1);
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像不正。テキストあり
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-0-imgfile', 'job.csv');
$I->fillField('#freecontentelementform-0-text', 'aaa');
$I->click('登録する');
$I->expect('ファイルのvalidation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');

// 画像不正。テキストなし
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-0-imgfile', 'job.csv');
$I->click('登録する');
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像不正。テキスト不正
moveOnCreatePage($I);
fillForm($I);
$I->selectOption('#freecontentelementform-0-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-0-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-0-imgfile', 'job.csv');
$I->fillField('#freecontentelementform-0-text', str_repeat('a', 5001));
$I->click('登録する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');


// ---------------
// 更新テストのため、一度レコードをクリア
// ---------------

$I->wantTo('更新テストのため、一度レコードをクリア');
cleanRecord($I);

// ---------------
// 更新用のレコードを作成
// ---------------

moveOnCreatePage($I);
fillForm($I);
uploadImage($I, 0, 'test.png');
$I->click('要素追加');
fillTextInput($I, 1);
dragAndDropOn($I, 0, 1);
$I->wait(1);
register($I);

// ---------------
// 更新
// ---------------

// 追加

// 画像あり。テキストあり
moveOnUpdatePage($I);
$I->click('要素追加');
uploadImage($I, 2, 'picture.jpg');
$I->click('要素追加');
fillTextInput($I, 3);
update($I);

// 画像のみ
moveOnUpdatePage($I);
$I->click('要素追加');
uploadImage($I, 4, 'test.png');
update($I);

// 画像あり。テキスト不正
moveOnUpdatePage($I);
$I->click('要素追加');
uploadImage($I, 5, 'test.png');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', 'テキスト');
$I->fillField('#freecontentelementform-6-text', str_repeat('a', 5001));
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像なし。テキストあり
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-5-displayitem', '画像');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', 'テキスト');
$I->fillField('#freecontentelementform-6-text', 'aaa');
$I->click('変更する');
$I->wait(1);
$I->expect('画像のvalidation');
$I->see('画像は必須項目です。', '.error-block');

//　画像なし。テキストなし
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-5-displayitem', '画像');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', 'テキスト');
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像なし。テキスト不正
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-5-displayitem', '画像');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', 'テキスト');
$I->fillField('#freecontentelementform-6-text', str_repeat('a', 5001));
$I->wait(1);
$I->click('変更する');
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像不正。テキストあり
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-5-displayitem', '画像');
// 不正な拡張子のファイルを添付
$I->attachFile('#freecontentelementform-5-imgfile', 'job.csv');
$I->click('要素追加');
fillTextInput($I, 6);
$I->click('変更する');
$I->expect('ファイルのvalidation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');

// 画像不正。テキストなし
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-5-displayitem', '画像');
// 不正な拡張子のファイルを添付
$I->attachFile('#freecontentelementform-5-imgfile', 'job.csv');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', 'テキスト');
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像不正。テキスト不正
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-5-displayitem', '画像');
$I->attachFile('#freecontentelementform-5-imgfile', 'job.csv');
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', 'テキスト');
$I->fillField('#freecontentelementform-6-text', str_repeat('a', 5001));
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');


/* 画像とテキスト */

// 画像が左

// 画像あり。テキストあり
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-5-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-5-layout', '画像が左');
$I->attachFile('#freecontentelementform-5-imgfile', 'picture.jpg');
$I->fillField('#freecontentelementform-5-text', 'aaa');
update($I);

// 画像のみ
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-6-layout', '画像が左');
$I->attachFile('#freecontentelementform-6-imgfile', 'test.png');
$I->click('変更する');
$I->expect('validation');
$I->see('テキストは必須項目です。', '.error-block');

// 画像あり。テキスト不正
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-6-layout', '画像が左');
$I->attachFile('#freecontentelementform-6-imgfile', 'test.png');
$I->fillField('#freecontentelementform-6-text', str_repeat('a', 5001));
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像なし。テキストあり
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-6-layout', '画像が左');
$I->fillField('#freecontentelementform-6-text', 'aaa');
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');

//　画像なし。テキストなし
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-6-layout', '画像が左');
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像なし。テキスト不正
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-6-layout', '画像が左');
$I->fillField('#freecontentelementform-6-text', str_repeat('a', 5001));
$I->click('変更する');
$I->expect('validation');
$I->wait(1);
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像不正。テキストあり
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-6-layout', '画像が左');
$I->attachFile('#freecontentelementform-6-imgfile', 'job.csv');
$I->fillField('#freecontentelementform-6-text', 'aaa');
$I->click('変更する');
$I->wait(1);
$I->expect('ファイルのvalidation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');

// 画像不正。テキストなし
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-6-layout', '画像が左');
$I->attachFile('#freecontentelementform-6-imgfile', 'job.csv');
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像不正。テキスト不正
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-6-layout', '画像が左');
$I->attachFile('#freecontentelementform-6-imgfile', 'job.csv');
$I->fillField('#freecontentelementform-6-text', str_repeat('a', 5001));
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// *************************************************************** //

// テキストが左

// 画像あり。テキストあり
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-6-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-6-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-6-imgfile', 'test.png');
$I->fillField('#freecontentelementform-6-text', 'aaa');
update($I);

// 画像のみ
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-7-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-7-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-7-imgfile', 'test.png');
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('テキストは必須項目です。', '.error-block');

// 画像あり。テキスト不正
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-7-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-7-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-7-imgfile', 'test.png');
$I->fillField('#freecontentelementform-7-text', str_repeat('a', 5001));
$I->click('変更する');
$I->wait(2);
$I->expect('validation');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像なし。テキストあり
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-7-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-7-layout', 'テキストが左');
$I->fillField('#freecontentelementform-7-text', 'aaa');
$I->click('変更する');
$I->wait(2);
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');

//　画像なし。テキストなし
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-7-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-7-layout', 'テキストが左');
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像なし。テキスト不正
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-7-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-7-layout', 'テキストが左');
$I->fillField('#freecontentelementform-7-text', str_repeat('a', 5001));
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('画像は必須項目です。', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 画像不正。テキストあり
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-7-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-7-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-7-imgfile', 'job.csv');
$I->fillField('#freecontentelementform-7-text', 'aaa');
$I->click('変更する');
$I->wait(1);
$I->expect('ファイルのvalidation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');

// 画像不正。テキストなし
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-7-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-7-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-7-imgfile', 'job.csv');
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは必須項目です。', '.error-block');

// 画像不正。テキスト不正
moveOnUpdatePage($I);
$I->click('要素追加');
$I->selectOption('#freecontentelementform-7-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-7-layout', 'テキストが左');
$I->attachFile('#freecontentelementform-7-imgfile', 'job.csv');
$I->fillField('#freecontentelementform-7-text', str_repeat('a', 5001));
$I->click('変更する');
$I->wait(1);
$I->expect('validation');
$I->see('次の拡張子を持つファイルだけが許可されています : jpg, jpeg, gif, png', '.error-block');
$I->see('テキストは5000文字以下で入力してください。', '.error-block');

// 更新。既存。並び変え後更新。

// ---------------
// 更新テストのため、一度レコードをクリア
// ---------------

$I->wantTo('更新テストのため、一度レコードをクリア');
cleanRecord($I);

// ---------------
// 更新用のレコードを作成
// ---------------
moveOnCreatePage($I);
fillForm($I);
uploadImage($I, 0, 'test.png');
$I->click('要素追加');
fillTextInput($I, 1);
$I->click('要素追加');
uploadImage($I, 2, 'picture.jpg');
$I->click('要素追加');
fillTextInput($I, 3);
$I->wait(1);
register($I);

// 画像、テキスト変更あり。
moveOnUpdatePage($I);
uploadImage($I, 3, 'test.png');
$I->wantTo('ソート');
dragAndDropOn($I, 3, 2);
$I->wait(1);
update($I);

// 画像変更あり。テキスト変更なし
moveOnUpdatePage($I);
uploadImage($I, 3, 'test.png');
$I->wantTo('ソート');
dragAndDropOn($I, 3, 2);
$I->wait(1);
update($I);

// 画像変更あり。テキスト不正
moveOnUpdatePage($I);
uploadImage($I, 3, 'picture.jpg');
fillTextInput($I, 2);
$I->selectOption('#freecontentelementform-2-displayitem', '画像');
$I->attachFile('#freecontentelementform-2-imgfile', 'picture.jpg');
$I->wantTo('ソート');
dragAndDropOn($I, 2, 3);
$I->wait(1);
update($I);

// 画像とテキスト。画像が左
moveOnUpdatePage($I);
$I->selectOption('#freecontentelementform-3-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-3-layout', '画像が左');
uploadImage($I, 3, 'test.png');
fillTextInput($I, 3);
$I->wantTo('ソート');
dragAndDropOn($I, 2, 3);
$I->wait(1);
update($I);

// 画像とテキスト。テキストが左
moveOnUpdatePage($I);
$I->selectOption('#freecontentelementform-3-displayitem', '画像とテキスト');
$I->selectOption('#freecontentelementform-3-layout', 'テキストが左');
uploadImage($I, 3, 'test.png');
fillTextInput($I, 3);
$I->wantTo('ソート');
dragAndDropOn($I, 3, 1);
dragAndDropOn($I, 0, 2);
$I->wait(1);
update($I);

// ---------------
// コピーの検証
// ---------------

/* 既存データの変更 */

// 画像とテキスト
moveOnCopyPage($I);
fillForm($I);
uploadImage($I, 0, 'picture.jpg');
fillTextInput($I, 1);
$I->wantTo('sort');
dragAndDropOn($I, 3, 0);
$I->wait(1);
register($I);

// 画像のみ
moveOnCopyPage($I);
fillForm($I);
uploadImage($I, 0, 'picture.jpg');
$I->wantTo('sort');
dragAndDropOn($I, 1, 3);
$I->wait(1);
register($I);

// テキストのみ
moveOnCopyPage($I);
fillForm($I);
fillTextInput($I, 3);
$I->wantTo('sort');
dragAndDropOn($I, 1, 3);
$I->wait(1);
register($I);

// 画像変更。テキスト不正
moveOnCopyPage($I);
fillForm($I);
uploadImage($I, 0, 'test.png');
fillTextInput($I, 1);
uploadImage($I, 1, 'test.png');
$I->wantTo('sort');
dragAndDropOn($I, 1, 3);
$I->wait(1);
register($I);

// テキスト変更。画像不正。
moveOnCopyPage($I);
fillForm($I);
fillTextInput($I, 1);
uploadImage($I, 2, 'job.csv');
fillTextInput($I, 2);
$I->wantTo('sort');
dragAndDropOn($I, 1, 3);
$I->wait(1);
register($I);

// テキストと画像。画像が左
moveOnCopyPage($I);
fillForm($I);
fillTextAndUploadImage($I, 2, '画像が左', 'test.png');
$I->wantTo('sort');
dragAndDropOn($I, 2, 0);
$I->wait(1);
register($I);

// テキストと画像。テキストが左
moveOnCopyPage($I);
fillForm($I);
fillTextAndUploadImage($I, 1, 'テキストが左', 'picture.jpg');
$I->wantTo('sort');
dragAndDropOn($I, 1, 3);
$I->wait(1);
register($I);

/* 新規データの追加 */

// 画像とテキスト

moveOnCopyPage($I);
fillForm($I);
$I->click('要素追加');
uploadImage($I, 4, 'test.png');
$I->click('要素追加');
fillTextInput($I, 5);
$I->wantTo('sort');
dragAndDropOn($I, 4, 1);
$I->wait(1);
register($I);

// 画像のみ
moveOnCopyPage($I);
fillForm($I);
$I->click('要素追加');
uploadImage($I, 4, 'test.png');
$I->wantTo('sort');
dragAndDropOn($I, 4, 1);
$I->wait(1);
register($I);

// テキストのみ
moveOnCopyPage($I);
fillForm($I);
$I->click('要素追加');
fillTextInput($I, 4);
$I->wantTo('sort');
dragAndDropOn($I, 4, 1);
$I->wait(1);
register($I);

// 画像とテキスト。画像が左
moveOnCopyPage($I);
fillForm($I);
$I->click('要素追加');
fillTextAndUploadImage($I, 4, '画像が左', 'test.png');
$I->wantTo('sort');
dragAndDropOn($I, 4, 2);
$I->wait(1);
register($I);

// 画像とテキスト。テキストが左
moveOnCopyPage($I);
fillForm($I);
$I->click('要素追加');
fillTextAndUploadImage($I, 4, 'テキストが左', 'test.png');
$I->wantTo('sort');
dragAndDropOn($I, 4, 1);
$I->wait(1);
register($I);


// ---------------
// utility methods
// ---------------

// レコードのクリア
function cleanRecord(AcceptanceTester $I)
{
    $I->wantTo('一覧が画面へ遷移');
    FreeContentRegisterPage::openBy($I);
    $I->wait(1);
    $I->click('この条件で表示する');
    $I->wait(2);
    $I->click('まとめて削除する');
    $I->wait(2);
    $I->click('OK');
}

// 必須項目を入力する
function fillForm(AcceptanceTester $I)
{
    $I->wantTo('必須項目を入力');
    $I->wait(1);
    // ページタイトル
    $I->fillField('#freecontentform-title', 'aaaa');
    // コンテンツURL:一意のためランダムな文字列
    $I->fillField('#freecontentform-url_directory', substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 5));
    // 公開状況
    $I->selectOption('#freecontentform-valid_chk', 1);
}

// 指定箇所に画像をアップロードする
function uploadImage(AcceptanceTester $I, $rowNumber, $fileName)
{
    $I->wantTo('画像をアップロード');
    $I->wait(1);
    $I->selectOption('#freecontentelementform-'.$rowNumber.'-displayitem', '画像');
    $I->attachFile('#freecontentelementform-'.$rowNumber.'-imgfile', $fileName);
}

// 指定箇所のテキストフォームに値を入力する
function fillTextInput(AcceptanceTester $I, $rowNumber)
{
    $I->wantTo('テキストを入力');
    $I->wait(1);
    $I->selectOption('#freecontentelementform-'.$rowNumber.'-displayitem', 'テキスト');
    $I->fillField('#freecontentelementform-'.$rowNumber.'-text', 'aaaa');
}

// 画像とテキストのアップロード
function fillTextAndUploadImage(AcceptanceTester $I, $rowNumber, $placedLeft, $fileName)
{
    $I->selectOption('#freecontentelementform-'.$rowNumber.'-displayitem', '画像とテキスト');
    $I->selectOption('#freecontentelementform-'.$rowNumber.'-layout', $placedLeft);
    $I->attachFile('#freecontentelementform-'.$rowNumber.'-imgfile', $fileName);
    $I->fillField('#freecontentelementform-'.$rowNumber.'-text', 'aaaa');
}

//
function register(AcceptanceTester $I)
{
    $I->click('登録する');
    $I->wait(1);
    $I->click('OK');
    $I->wait(2);
    $I->see('登録完了', 'h1');
}

//
function update(AcceptanceTester $I)
{
    $I->click('変更する');
    $I->wait(2);
    $I->click('OK');
    $I->wait(2);
    $I->see('変更完了', 'h1');
}

//
function submit(AcceptanceTester $I, $type)
{
    $I->click($type);
    $I->wait(1);
    $I->click('OK');
    $I->wait(2);
    $I->see($type.'完了', 'h1');
}

// ソート処理
function dragAndDropOn(AcceptanceTester $I, $from, $to)
{
    $I->dragAndDrop('.field-freecontentelementform-'.$from.'-id','.field-freecontentelementform-'.$to.'-id');
}

//
function moveOnListPage(AcceptanceTester $I)
{
    $I->wantTo('一覧が画面へ遷移');
    FreeContentRegisterPage::openBy($I);
    $I->wait(1);
}

//
function moveOnCreatePage(AcceptanceTester $I)
{
    moveOnListPage($I);
    $I->click('フリーコンテンツ設定・編集を登録する');
    $I->wait(1);
}

//
function moveOnUpdatePage(AcceptanceTester $I)
{
    moveOnListPage($I);
    $I->click('この条件で表示する');
    $I->wait(2);
    $I->click('a[title="変更"]');
    $I->wait(2);
}

//
function moveOnCopyPage(AcceptanceTester $I)
{
    moveOnListPage($I);
    $I->click('この条件で表示する');
    $I->wait(2);
    $I->click('a[title="コピー"]');
    $I->wait(2);
}