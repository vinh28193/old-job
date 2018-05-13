<?php

use app\models\JobMasterDisp;
use app\models\manage\JobPic;
use app\models\manage\ManageMenuMain;
use app\models\manage\MediaUpload;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\job\JobPicPage;
use tests\codeception\_pages\manage\ManageLoginPage;
// テスト準備(レコード準備等) //////////////////////////////////////////////////////////////////////////////////////////
/* @var $scenario Codeception\Scenario */
Yii::$app->user->identity = Manager::findOne(['login_id' => 'admin01']);

// このjobのcorpに最低2つ以上のclientが紐づいている必要があります
/** @var JobMasterDisp $job */
$job = JobMasterDisp::find()->active()->one();
/** @var MediaUpload[] $mediaUploads */
$mediaUploads = MediaUpload::find()->limit(4)->all();

// 検証に必要な最低限のレコードの存在を担保
$noTagOwnerPic = $mediaUploads[0];
$noTagOwnerPic->client_master_id = null;
$noTagOwnerPic->tag = '';
$noTagOwnerPic->save(false);

$tagOwnerPic = $mediaUploads[1];
$tagOwnerPic->client_master_id = null;
$tagOwnerPic->tag = 'ownerTag';
$tagOwnerPic->save(false);

$noTagClientPic = $mediaUploads[2];
$noTagClientPic->client_master_id = $job->client_master_id;
$noTagClientPic->tag = null;
$noTagClientPic->save(false);

$tagClientPic = $mediaUploads[3];
$tagClientPic->client_master_id = $job->client_master_id;
$tagClientPic->tag = 'clientTag';
$tagClientPic->save(false);

// 掲載企業画像と運営元画像のモデルインスタンスをそれぞれ準備
/** @var MediaUpload[] $ownerPics */
$ownerPics = MediaUpload::find()
    ->where(['client_master_id' => null])
    ->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])
    ->all();
/** @var MediaUpload[] $clientPics */
$clientPics = MediaUpload::find()
    ->where(['client_master_id' => $job->clientMaster->id])
    ->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])
    ->all();

// タグ取得用のモデル
$jobPic = new JobPic();
$jobPic->client_master_id = $job->client_master_id;

// テスター準備
$I = new AcceptanceTester($scenario);

// テスト開始 //////////////////////////////////////////////////////////////////////////////////////////////////////////
$I->wantTo('求人原稿編集画面-画像モーダルテスト');
$loginPage = ManageLoginPage::openBy($I);

//----------------------
// 運営元でログインしてupdate画面へ遷移
//----------------------
$I->amGoingTo('運営元でアクセス');
$loginPage->login('admin01', 'admin01');
$I->wait(2);
$page = JobPicPage::openBy($I, ['id' => $job->id]);
$I->wait(2);

$page->openPicModal();
$I->see('写真-登録・修正', 'h3');

//----------------------
// 初期表示検証
//----------------------
$page->checkModalPictures($clientPics, $ownerPics);

//----------------------
// タグドロップダウン検証
//----------------------
$I->amGoingTo('タグの選択肢が正常');
$i = 1;
foreach (JobPic::makeTagDropDownSelections($jobPic->clientPics) as $value => $tag) {
    $I->seeElementInDOM("//select[@id='clientTag']/option[{$i}][@value='{$value}' and text()='{$tag}']");
    $i++;
}
$i = 1;
foreach (JobPic::makeTagDropDownSelections($jobPic->ownerPics) as $value => $tag) {
    $I->seeElementInDOM("//select[@id='ownerTag']/option[{$i}][@value='{$value}' and text()='{$tag}']");
    $i++;
}

$I->amGoingTo('運営元画像をタグでfilterする');
$page->filterPic(JobPicPage::OWNER, $tagOwnerPic->tag);
$I->seeElement('img', ['data-model_id' => $tagOwnerPic->id]);
$I->cantSeeElement('img', ['data-model_id' => $noTagOwnerPic->id]);


$I->amGoingTo('運営元画像をタグ無しでfilterする');
$page->filterPic(JobPicPage::OWNER, 'タグ無し');
$I->cantSeeElement('img', ['data-model_id' => $tagOwnerPic->id]);
$I->seeElement('img', ['data-model_id' => $noTagOwnerPic->id]);


$I->amGoingTo('運営元画像を全部表示する');
$page->filterPic(JobPicPage::OWNER, 'すべて');
foreach ($ownerPics as $pic) {
    $I->seeElement('img', ['data-model_id' => $pic->id]);
}


$I->amGoingTo('掲載企業画像をタグでfilterする');
$page->filterPic(JobPicPage::CLIENT, $tagClientPic->tag);
$I->seeElement('img', ['data-model_id' => $tagClientPic->id]);
$I->cantSeeElement('img', ['data-model_id' => $noTagClientPic->id]);


$I->amGoingTo('掲載企業画像をタグ無しでfilterする');
$page->filterPic(JobPicPage::CLIENT, 'タグ無し');
$I->cantSeeElement('img', ['data-model_id' => $tagClientPic->id]);
$I->seeElement('img', ['data-model_id' => $noTagClientPic->id]);


$I->amGoingTo('掲載企業画像を全部表示する');
$page->filterPic(JobPicPage::CLIENT, 'すべて');
foreach ($clientPics as $pic) {
    $I->seeElement('img', ['data-model_id' => $pic->id]);
}

//----------------------
// 別の掲載企業に変更して検証
//----------------------
$I->amGoingTo('別の掲載企業に切り替え');
$I->click('//div[@id="picModal"]/div/div/div[1]/button');
$I->wait(1);
$I->click('#select2-jobmaster-client_master_id-container');
$I->wait(2);
$I->click('//ul[@id="select2-jobmaster-client_master_id-results"]/li[@aria-selected="false"][1]');
$I->wait(1);
$clientName = $I->grabTextFrom('//span[@id="select2-jobmaster-client_master_id-container"]');
$clientId = $I->grabAttributeFrom("//select[@id='jobmaster-client_master_id']/option[text()='{$clientName}']" , 'value');
$clientPics2 = MediaUpload::find()
    ->where(['client_master_id' => $clientId])
    ->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])
    ->all();
$I->wait(2);

$page->openPicModal();
$page->checkModalPictures($clientPics2, $ownerPics);

// todo 開発環境にファイルサーバーができ次第アップロードと削除のテスト
