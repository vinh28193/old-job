<?php

/* @var $scenario Codeception\Scenario */

use app\common\SearchKey;
use app\controllers\KyujinController;
use app\models\JobMasterDisp;
use app\models\manage\MainDisp;
use app\models\manage\SearchkeyMaster;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\jobPref;
use app\models\manage\searchkey\jobDist;
use app\models\manage\searchkey\PrefDistMaster;
use app\models\manage\searchkey\JobSearchkeyItem;
use tests\codeception\_pages\JobDetailPage;
use tests\codeception\_pages\manage\ManageLoginPage;
use tests\codeception\_pages\manage\job\JobRegisterPage;
use tests\codeception\fixtures\SearchkeyCategory1Fixture;
use tests\codeception\fixtures\SearchkeyItem11Fixture;
use tests\codeception\fixtures\SearchkeyItem1Fixture;
use tests\codeception\fixtures\SearchkeyMasterFixture;

// テスト準備 //////////////////////////////////////////////////////////////////////////////////////////////////////////
// 1,2,11,12が有効、6,7,16,17が無効
// 1,6,11,16がアイコン表示、2,7,12,17がアイコン非表示
// 勤務地・路線・給与が有効、
(new SearchkeyMasterFixture())->load();
(new SearchkeyCategory1Fixture())->load();
(new SearchkeyItem1Fixture())->load();
(new SearchkeyItem11Fixture())->load();
// 勤務地、路線、給与、汎用1,2,6,7,11,12,16,17検索キーが少なくとも一つは紐づけられている掲載状態の原稿が存在する前提
// 勤務地は2都道府県（同エリア 関東以外）を紐付けている前提。
$with = [
    'jobDist',
    'jobPref',
    'jobPref.pref',
    'jobDist',
    'jobDist.dist',
    'jobStation',
    'jobWage',
    'jobSearchkeyItem1',
    'jobSearchkeyItem2',
    'jobSearchkeyItem6',
    'jobSearchkeyItem7',
    'jobSearchkeyItem11',
    'jobSearchkeyItem12',
    'jobSearchkeyItem16',
    'jobSearchkeyItem17',
];

/** @var JobMasterDisp $job */
$job = JobMasterDisp::find()
    ->innerJoinWith($with)
    ->active()->one();

$searchCds = SearchkeyMaster::find()->where(['table_name' => 'pref'])->one();
$pc = $searchCds->first_hierarchy_cd;
$pm = $searchCds->second_hierarchy_cd;
$dc = $searchCds->third_hierarchy_cd;

/** @var SearchKey $searchKey */
$searchKey = Yii::$app->searchKey;

$mainItems = MainDisp::items($job->clientChargePlan->disp_type_id);
//$listItems = MainDisp::items($job->clientChargePlan->disp_type_id);

$I = new AcceptanceTester($scenario);
$I->wantTo('原稿詳細ページ、携帯に送るページのテスト');

$I->amGoingTo('原稿詳細ページを表示');
$page = JobDetailPage::openBy($I, ['job_no' => $job->job_no]);
$I->wait(3);
$I->see('募集要項', 'h2');

$I->amGoingTo('検索キーアイコンの検証');
$I->expect('表示されるべきアイコンが表示されている');
$icons = $searchKey->searchKeyIconContents($job);
$i = 1;
foreach ($icons as $key => $icon) {
    if ($key == KyujinController::INITIAL_DISPLAY_ICONS) {
        $I->expect('6つ目以降は最初は見えていない');
        $I->cantSee($icon, "//p[@class='mod-iconSearchKey']/span[{$i}]");
        $I->cantSee($icon, "//p[@class='mod-iconSearchKey']/span[7]/span[{$i}]");
        $I->amGoingTo('全て表示する');
        $I->click('...全て表示');
        $i = 1;
        $I->wait(1);
        $I->expect('6つ目以降が見えるようになり、すべて表示は消える');
        $I->cantSee('...全て表示');
    }
    if ($key >= KyujinController::INITIAL_DISPLAY_ICONS) {
        $I->see($icon, "//p[@class='mod-iconSearchKey']/span[7]/span[{$i}]");
    } else {
        $I->see($icon, "//p[@class='mod-iconSearchKey']/span[{$i}]");
    }
    $i++;
}
$I->expect('勤務地と駅はアイコン表示なし');
foreach ($job->jobPref as $jobPref) {
    $I->cantSee($jobPref->pref->pref_name, 'span.icon.icon-merit');
}
foreach ($job->jobStation as $jobStation) {
    $I->cantSee($jobStation->station->station_name, 'span.icon.icon-merit');
}
// 別フェーズで給与は1カテゴリ1項目しか求人原稿に登録できなくするので、
// 最高値以外の給与が表示されていない事は手動で確認する
$I->expect('無効もしくはアイコン非表示の検索キーはアイコン表示なし');
foreach ([2, 6, 7, 12, 16, 17] as $i) {
    foreach ($job->{'jobSearchkeyItem' . $i} as $jobSearchkeyItem) {
        /** @var JobSearchkeyItem $jobSearchkeyItem */
        $I->cantSee($jobSearchkeyItem->searchKeyItem->searchkey_item_name, 'span.icon.icon-merit');
    }
}

// 別セッションで検索キーを削除
$I->amGoingTo('原稿でアイコン表示されている検索キーを一つ削除');
$admin = $I->haveFriend('admin');

$admin->does(function (AcceptanceTester $I) use ($job) {
    $I->resizeWindow(1200, 800);
    $loginPage = ManageLoginPage::openBy($I);
    $I->amGoingTo('運営元でアクセス');
    $loginPage->login('admin01', 'admin01');
    $I->wait(1);

    // 二階層のものはカテゴリを削除
    $I->amOnPage('/manage/secure/searchkey1/list');
    $I->wait(3);
    /** @var JobSearchkeyItem $jobSearchkeyItem */
    $jobSearchkeyItem = $job->jobSearchkeyItem1[0];
    $I->click($jobSearchkeyItem->searchKeyItem->category->searchkey_category_name);
    $I->wait(1);
    $I->click('削除');
    $I->wait(1);
    $I->click('OK');
    $I->wait(1);

    // 一階層のものは項目を削除
    $I->amOnPage('/manage/secure/searchkey11/list');
    $I->wait(3);
    /** @var JobSearchkeyItem $jobSearchkeyItem */
    $jobSearchkeyItem = $job->jobSearchkeyItem11[0];
    $I->click($jobSearchkeyItem->searchKeyItem->searchkey_item_name);
    $I->wait(1);
    $I->click('削除');
    $I->click('OK');
    $I->wait(1);
});

$I->amGoingTo('原稿詳細ページが正常に表示される');
$page::openBy($I, ['job_no' => $job->job_no]);
$I->wait(3);
$I->see('募集要項', 'h2');

$I->amGoingTo('転送先情報入力フォームを表示');
$I->click('メールで転送する');
$I->wait(5);
$I->see('転送先情報入力フォーム', 'h1');

$I->amGoingTo('転送先情報入力をメールで送信');
$I->fillField('//*[@id="jobmasterdisp-mailaddress"]', 'test@example.com');
$I->click('送信する');
$I->wait(5);
$I->see('メール転送完了', 'h1');

// 消した検索キーを元に戻す
(new SearchkeyCategory1Fixture())->load();
(new SearchkeyItem1Fixture())->load();
(new SearchkeyItem11Fixture())->load();


// 都道府県のエリア有効・無効時の動作チェック
// １．紐付く都道府県が全て有効
// 元に戻す用
$areaBoxId = [];
foreach ($job->jobPref as $jobPref) {
    $areaBoxId[] = $jobPref->pref->area_id % 8;
}

$I->wantTo('原稿詳細ページ、都道府県（勤務地）の有効・無効による表示テスト');

$I->amGoingTo('紐付く都道府県が全て有効な状態で原稿詳細ページを表示');
$page = JobDetailPage::openBy($I, ['job_no' => $job->job_no]);
$I->wait(3);
$I->see('募集要項', 'h2');

// 別セッションで都道府県を１つ無効に設定
$I->amGoingTo('原稿に紐付けている都道府県を1つ無効にする');
$admin = $I->haveFriend('admin');

$admin->does(function (AcceptanceTester $I) use ($job) {
    $I->resizeWindow(1200, 800);
    $loginPage = ManageLoginPage::openBy($I);
    $I->amGoingTo('運営元でアクセス');
    $I->wait(1);

    // 原稿に紐付けている都道府県を1つ無効にする
    $I->amOnPage('/manage/secure/area/list');
    $I->wait(3);
    $prefId = $job->jobPref[0]->pref->id;
    $I->dragAndDrop('//li[@data-key="' . $prefId . '"]', '//ul[@id="w10-sortable"]');
    $I->click('エリアの並び順、都道府県の割当と並び順を確定する');
    $I->wait(2);
    $I->see('エリアの並び順、都道府県の割当と並び順の更新が完了しました。', 'p');
});

// ２．紐付く都道府県が一部無効

$I->amGoingTo('紐付く都道府県が一部無効な状態で原稿詳細ページを表示');
$page = JobDetailPage::openBy($I, ['job_no' => $job->job_no]);
$I->wait(3);
$I->see('募集要項', 'h2');

// 別セッションで残りの都道府県を無効
$I->amGoingTo('原稿に紐付けている都道府県を全て無効にする');

$admin->does(function (AcceptanceTester $I) use ($job, &$prefBoxId) {
    $I->resizeWindow(1200, 800);
    $loginPage = ManageLoginPage::openBy($I);
    $I->amGoingTo('運営元でアクセス');
    $I->wait(1);

    // 原稿に紐付けている都道府県を1つ無効にする
    $I->amOnPage('/manage/secure/area/list');
    $I->wait(3);
    $prefId = $job->jobPref[1]->pref->id;
    $I->dragAndDrop('//li[@data-key="' . $prefId . '"]', '//ul[@id="w10-sortable"]');
    $I->click('エリアの並び順、都道府県の割当と並び順を確定する');
    $I->wait(2);
    $I->see('エリアの並び順、都道府県の割当と並び順の更新が完了しました。', 'p');
});

// ３．紐付く都道府県が全て無効
// 404に遷移すること

$I->amGoingTo('紐付く都道府県が全て無効な状態で原稿詳細ページを表示');
$page = JobDetailPage::openBy($I, ['job_no' => $job->job_no]);
$I->wait(3);
$I->see('残念ですが、お探しのページは見つかりませんでした', 'h1');


$I->amGoingTo('都道府県の設定を元に戻す');

$admin->does(function (AcceptanceTester $I) use ($job) {
    $I->resizeWindow(1200, 800);
    $loginPage = ManageLoginPage::openBy($I);
    $I->amGoingTo('運営元でアクセス');
    $I->wait(1);

    // 原稿に紐付けている都道府県を元に戻す
    $I->amOnPage('/manage/secure/area/list');
    $I->wait(3);
    foreach ($job->jobPref as $jobPref) {
        $I->dragAndDrop('//li[@data-key="' . $jobPref->pref->id . '"]', '//ul[@id="w' . $jobPref->pref->area_id % 8 . '-sortable"]');
    }

    $I->click('エリアの並び順、都道府県の割当と並び順を確定する');
    $I->wait(2);
    $I->see('エリアの並び順、都道府県の割当と並び順の更新が完了しました。', 'p');
});

// パンくずの表示テスト
// １．直接詳細ページにアクセスする
$I->wantTo('パンくず表示テスト - 直接アクセスの場合');
$I->amGoingTo('直接原稿詳細ページにアクセスする');
$page = JobDetailPage::openBy($I, ['job_no' => $job->job_no]);
$I->wait(3);
$I->expect('取得できる一番先頭の勤務地がパンくずに表示されること');
$I->see($job->jobPref[0]->pref->pref_name, '//div[@class="breadcrumb"]/ul/li[2]/a');
$I->see($job->jobDist[0]->dist->dist_name, '//div[@class="breadcrumb"]/ul/li[3]/a');

$I->amGoingTo('エリア跨ぎの勤務地を設定する');
// 東京都の情報を取得
$pref = Pref::find()->where(['pref_name' => '東京都'])->one();
$dist = Dist::find()->where(['pref_no' => $pref->pref_no])->one();
$admin->does(function (AcceptanceTester $I) use ($job, $pref, $dist) {
    $I->resizeWindow(1200, 800);
    $page = JobRegisterPage::openBy($I);
    $I->amGoingTo('運営元でアクセス');
    $I->wait(1);

    // エリア跨ぎの勤務地を設定する
    $I->amOnPage('manage/secure/job/update?id='. $job->id);
    $I->wait(3);
    $I->click('選択する');
    $I->wait(1);
    $I->executeJS("$('#pref{$pref->id}').collapse('show')");
    $I->wait(1);
    $I->checkOption("div#pref{$pref->id} input[name=JobDist\\[itemIds\\]\\[\\]]", $dist->dist_name);

    $I->click('変更を保存');
    $I->wait(1);
    $page->submit('変更');
});

// 仕事情報を再取得
$job = JobMasterDisp::find()
->innerJoinWith($with)
->active()->one();


// ２．エリアディレクトリのみURLからアクセスする
$I->wantTo('パンくず表示テスト - エリアディレクトリのみURLからのアクセスの場合');
$I->amGoingTo('勤務地検索結果一覧ページにアクセスする');
$I->amOnPage("/{$pref->area->area_dir}/search-result/");
$I->wait(3);
$I->click('詳細をみる');
$I->wait(3);

$I->expect('エリアディレクトリに沿ったパンくずが表示されること');
$I->see($pref->pref_name, '//div[@class="breadcrumb"]/ul/li[2]/a');
$I->see($dist->dist_name, '//div[@class="breadcrumb"]/ul/li[3]/a');

// ３-１．エリアディレクトリ、都道府県があるURLからアクセスする
$pref = $job->jobPref[1]->pref;
$I->wantTo('パンくず表示テスト - エリアディレクトリ、都道府県があるURLからのアクセスの場合');
$I->amGoingTo('勤務地検索結果一覧ページにアクセスする');
$I->amOnPage("/{$pref->area->area_dir}/{$pc}{$pref->pref_no}/");
$I->wait(3);
$I->click('詳細をみる');
$I->wait(3);

$dist_name = '';
foreach ($job->jobDist as $jobDist) {
    /* @var JobDist $jobDist */
    if ($jobDist->dist->pref_no === $pref->pref_no) {
        $dist_name = $jobDist->dist->dist_name;
        break;
    }
}
$I->expect('都道府県に沿ったパンくずが表示されること');
$I->see($pref->pref_name, '//div[@class="breadcrumb"]/ul/li[2]/a');
$I->see($dist_name, '//div[@class="breadcrumb"]/ul/li[3]/a');

// ３-２．エリアディレクトリ、都道府県があるURLからアクセスする　　※エリア跨ぎ
$pref = $job->jobPref[2]->pref;
$I->wantTo('パンくず表示テスト - エリア跨ぎのあるURLからのアクセスの場合');
$I->amGoingTo('勤務地検索結果一覧ページにアクセスする');
$I->amOnPage("/{$job->jobPref[0]->pref->area->area_dir}/{$pc}{$pref->pref_no}/");
$I->wait(3);
$I->click('詳細をみる');
$I->wait(3);

$dist_name = '';
foreach ($job->jobDist as $jobDist) {
    /* @var JobDist $jobDist */
    if ($jobDist->dist->pref_no === $pref->pref_no) {
        $dist_name = $jobDist->dist->dist_name;
        break;
    }
}
$I->expect('都道府県に沿ったパンくずが表示されること');
$I->see($pref->pref_name, '//div[@class="breadcrumb"]/ul/li[2]/a');
$I->see($dist_name, '//div[@class="breadcrumb"]/ul/li[3]/a');

// ４．エリアディレクトリ、都道府県、市町村があるURLからアクセスする
$pref = $job->jobPref[1]->pref;
$dist = null;
foreach ($job->jobDist as $jobDist) {
    /* @var JobDist $jobDist */
    if ($jobDist->dist->pref_no === $pref->pref_no) {
        $dist = $jobDist->dist;
        break;
    }
}

$I->wantTo('パンくず表示テスト - エリアディレクトリ、都道府県、市町村があるURLからのアクセスの場合');
$I->amGoingTo('勤務地検索結果一覧ページにアクセスする');
$I->amOnPage("/{$pref->area->area_dir}/{$pc}{$pref->pref_no}/{$dc}{$dist->dist_cd}");
$I->wait(3);
$I->click('詳細をみる');
$I->wait(3);

$I->expect('都道府県に沿った都道府県のパンくずが表示されること');
$I->see($pref->pref_name, '//div[@class="breadcrumb"]/ul/li[2]/a');
$I->expect('市町村に沿った市町村のパンくずが表示されること');
$I->see($dist->dist_name, '//div[@class="breadcrumb"]/ul/li[3]/a');


// ５．エリアディレクトリ、市町村があるURLからアクセスする
$pref = $job->jobPref[1]->pref;
$dist = null;
foreach ($job->jobDist as $jobDist) {
    /* @var JobDist $jobDist */
    if ($jobDist->dist->pref_no === $pref->pref_no) {
        $dist = $jobDist->dist;
        break;
    }
}

$I->wantTo('パンくず表示テスト - エリアディレクトリ、市町村があるURLからのアクセスの場合');
$I->amGoingTo('勤務地検索結果一覧ページにアクセスする');
$I->amOnPage("/{$pref->area->area_dir}/{$dc}{$dist->dist_cd}");
$I->wait(3);
$I->click('詳細をみる');
$I->wait(3);

$I->expect('エリアディレクトリに沿った都道府県のパンくずが表示されること');
$I->see($job->jobPref[0]->pref->pref_name, '//div[@class="breadcrumb"]/ul/li[2]/a');
$I->expect('市町村に沿った市町村のパンくずが表示されること');
$I->see($dist->dist_name, '//div[@class="breadcrumb"]/ul/li[3]/a');


// ６．エリアディレクトリ、地域グループがあるURLからアクセスする
$pref = $job->jobPref[0]->pref;
$dist = $job->jobDist[0]->dist;
$prefDistMaster = PrefDistMaster::find()
    ->join('INNER JOIN', 'pref_dist', 'pref_dist_master.id = pref_dist.pref_dist_master_id')
    ->where(['pref_dist.dist_id' => $dist->dist_cd ])->one();

$I->wantTo('パンくず表示テスト - エリアディレクトリ、市町村があるURLからのアクセスの場合');
$I->amGoingTo('勤務地検索結果一覧ページにアクセスする');
$I->amOnPage("/{$pref->area->area_dir}/{$pm}{$prefDistMaster->pref_dist_master_no}");
$I->wait(3);
$I->click('詳細をみる');
$I->wait(3);

$I->expect('地域グループの都道府県に沿った都道府県のパンくずが表示されること');
$I->see($pref->pref_name, '//div[@class="breadcrumb"]/ul/li[2]/a');
$I->expect('地域グループの市町村に沿った市町村のパンくずが表示されること');
$I->see($dist->dist_name, '//div[@class="breadcrumb"]/ul/li[3]/a');

// 元に戻す
$pref = Pref::find()->where(['pref_name' => '東京都'])->one();
$dist = Dist::find()->where(['pref_no' => $pref->pref_no])->one();
$admin->does(function (AcceptanceTester $I) use ($job, $pref, $dist) {
    $I->resizeWindow(1200, 800);
    $page = JobRegisterPage::openBy($I);
    $I->amGoingTo('運営元でアクセス');
    $I->wait(1);

    // エリア跨ぎの勤務地を設定する
    $I->amOnPage('manage/secure/job/update?id='. $job->id);
    $I->wait(3);
    $I->click('選択する');
    $I->wait(1);
    $I->executeJS("$('#pref{$pref->id}').collapse('show')");
    $I->wait(1);
    $I->uncheckOption("div#pref{$pref->id} input[name=JobDist\\[itemIds\\]\\[\\]]", $dist->dist_name);

    $I->click('変更を保存');
    $I->wait(1);
    $page->submit('変更');
});

