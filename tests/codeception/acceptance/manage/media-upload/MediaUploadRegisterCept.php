<?php
use app\models\manage\ManageMenuMain;
use app\models\manage\MediaUpload;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\media_upload\MediaUploadPage;

/* @var $scenario Codeception\Scenario */

// @TODO ファイルアップロードのテストがないため、あらかじめ各権限（運営元、掲載企業）でログインし、画像をアップロードしておく必要があります

define('_DATA', dirname(dirname(dirname(__DIR__))) . '/_data/');
define('_SRC', _DATA . 'media-upload.png');
define('_PREFIX', 'mu-');

// ログインする管理者の情報取得
$ownerAdmin = Manager::findOne(['login_id' => 'admin01']);
$clientAdmin = Manager::findOne(['login_id' => 'admin03']);

// 変更する画像データ取得
/** @var MediaUpload $clientPic */
$clientPic = MediaUpload::find()->where(['client_master_id' => $clientAdmin->client_master_id])->orderBy(['updated_at' => SORT_DESC])->one();
// admin_master_idの更新確認ができるように、admin_master_idが被らないようにしておく
$clientPic->admin_master_id = 123456;
$clientPic->save(false);

/**
 * 同じファイル名のファイルをアップロードするとエラーになるのでリネームする
 *
 * @return string リネームしたファイル名
 */
function createUploadFile()
{
    $filename = uniqid(_PREFIX) . '.png';
    $dest = _DATA . $filename;
    copy(_SRC, $dest);

    return $filename;
}

/**
 * リネームしたファイルを削除する
 */
function removeUploadFile()
{
    foreach (glob(_DATA . _PREFIX . '*.png') as $png) {
        @unlink($png);
    }
}


// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
Yii::$app->user->identity = $ownerAdmin;

// メニュー情報取得
$menu = ManageMenuMain::findFromRoute('/manage/secure/media-upload/list');

// テスト失敗時に残ってしまったファイルを削除
removeUploadFile();

$admins = [
    [
        'type' => '掲載企業',
        'loginid' => 'admin03',
        'password' => 'admin03',
    ],
    [
        'type' => '運営元',
        'loginid' => 'admin01',
        'password' => 'admin01',
    ],
];

// テスター・ログインpage準備
$I = new AcceptanceTester($scenario);

// テスト準備(on ブラウザ) /////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('ギャラリー登録変更のテスト');

foreach ($admins as $admin) {
    $I->amGoingTo($admin['type'] . '権限でログイン');
    $loginPage = ManageLoginPage::openBy($I);
    $loginPage->login($admin['loginid'], $admin['password']);
    $I->wait(3);


    $I->amGoingTo('画像一覧ページへ移動');

    if (isset($page)) {
        /* @var $page MediaUploadPage */
        $I->amOnPage($page->route);
    } else {
        $page = MediaUploadPage::openBy($I);
        $page->targetModel = $clientPic;
    }
    $I->wait(3);
    $I->see($menu->title, 'h1');


    // @TODO ドラッグ&ドロップを使用したファイルアップロードのテスト


    $I->amGoingTo('種別で検索');
    $I->selectOption('//*[@id="mediauploadsearch-role"]', 'client_admin');
    $I->click('この条件で表示する');
    $I->wait(3);
    $I->see($menu->title, 'h1');


    $I->amGoingTo('更新モーダルを開く');
    $page->clickActionColumn(1, 1); // 何故かここだけphantomJs上では二回押さないとモーダルが出てくれない
    $page->clickActionColumn(1, 1);
    $I->wait(5);

    $I->amGoingTo('初期化の確認');
    $I->seeInField('#mediaupload-tag', $page->targetModel->tag);

    $I->amGoingTo('"0"という画像検索用タグを設定することはできない');
    $I->fillField('#mediaupload-tag', 0);
    $I->wait(1);
    $I->see('"0"という画像検索用タグを設定することはできません', 'div.error-block.text-danger');

    $I->amGoingTo('タグのみを更新する');
    $tag = time();
    $I->fillField('#mediaupload-tag', $tag);
    $I->click('//div[@class="modal-footer"]/button[text()="変更"]');
    $I->wait(5);
    $I->see('タグの変更が完了しました', 'p');
    $page->isUpdated($tag, false);

    $I->amGoingTo('タグと画像を編集する');
    $page->clickActionColumn(1, 1); // 何故かここだけphantomJs上では二回押さないとモーダルが出てくれない
    $page->clickActionColumn(1, 1);
    $I->wait(5);
    $tag = time();
    $I->fillField('#mediaupload-tag', $tag);
    $I->attachFile('//div[@class="modal-body"]//input[@type="file"]', createUploadFile());
    $I->click('//div[@class="modal-footer"]/button[text()="変更"]');
    $I->wait(5);
    $I->see('画像のアップロードが完了しました', 'p');
    $page->isUpdated($tag, true);


    $I->amGoingTo('ログアウト');
    $I->amOnPage('manage/logout');
}

removeUploadFile();
