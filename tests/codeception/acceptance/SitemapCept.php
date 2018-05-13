<?php

/* @var $scenario Codeception\Scenario */

use app\commands\SitemapController;
use proseeds\models\Tenant;
use \app\commands\SitemapGenerator;

/**
 * このテストはサイトマップ生成後に実施してください
 * URLの有効チェックはテスト環境で表示できるTenantのサイトマップのみチェック対象としています
 **/
// テスト準備(定数値として使いたいもの)/////////////////////////////////////////////////////////////////////////////////
$sampleNumUrl = 3;
$sampleNumFile = 10;
$xmlLineNumHeaderFooter = 3;
$xmlLineNumOneItem = 6;
$sitemapDir = Yii::$app->basePath . "/web/systemdata/" . Yii::$app->tenant->id . "/data/sitemap/";
$targetData = [
    ['title' => '原稿詳細', 'path' => "{$sitemapDir}sitemap_detai*.xml"],
    ['title' => '検索結果', 'path' => "{$sitemapDir}sitemap_resul*.xml"],
];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$I = new AcceptanceTester($scenario);
$I->wantTo('サイトマップテスト');
$xmlReader = new XMLReader();
//----------------------
// URLの有効チェック
//----------------------
$I->amGoingTo("全国TOP、エリアTOP");
$I->comment($sitemapDir . "sitemap.xml");
// sitemap.xml
$xmlReader->open($sitemapDir . "sitemap.xml");
while($xmlReader->read()) {
    if ($xmlReader->name === 'loc') {
        $path = explode(Yii::$app->tenant->getTenantCode(), $xmlReader->readString());
        $I->amOnPage($path[1]);
        $I->dontSee('404 File not found.');
        if (!$xmlReader->next()) {
            $xmlReader->close();
            break;
        };
    }
}

foreach($targetData as $target) {
    $I->amGoingTo($target['title']);
    // 対象ファイルを取得
    $files = glob($target['path'], GLOB_NOSORT);
    // ランダムに並び替え
    shuffle($files);
    $count = 0;

    foreach ((array)$files as $filename) {
        $I->comment($filename);
        $ret = explode(' ', exec( 'wc -l '.$filename ));
        $kensu = ($ret[0] - $xmlLineNumHeaderFooter) / $xmlLineNumOneItem;
        $targetKeys = range(0, ($kensu - 1));
        if($sampleNumUrl < $kensu) {
            $targetKeys = array_rand($targetKeys, $sampleNumUrl);
            sort($targetKeys);
        }
        $xmlReader->open($filename);
        $item = -1;
        while($xmlReader->read()) {
            $line = $xmlReader->readString();
            if ($xmlReader->name === 'loc') {
                if(!$line) {
                    continue;
                }
                $item++;
                if(!in_array($item, $targetKeys)) {
                    continue;
                }
                $path = explode(Yii::$app->tenant->getTenantCode(), $xmlReader->readString());
                $I->amOnPage($path[1]);
                $I->dontSee('404 File not found.');
                if (!$xmlReader->next()) {
                    $xmlReader->close();
                    break;
                };
            }
        }
        $count++;
        if($count == 10) {
            break;
        }
    }
}
//----------------------
// 10MB以下または5万URL以下チェック
//----------------------
$files = glob(Yii::$app->basePath . "/web/systemdata/*/data/sitemap/sitema*.xml");
//$files = glob(Yii::$app->basePath . "/web/systemdata/*/data/sitemap/sitema*.xml", GLOB_NOSORT);
foreach($files as $file) {
    $I->comment($file);
    $fileSize = filesize($file);
    $I->comment("fileSize = {$fileSize}");
    $ret = explode(' ', exec( 'wc -l '.$file ));
    $kensu = $ret[0] ? ($ret[0] - $xmlLineNumHeaderFooter) / $xmlLineNumOneItem : 0;
    $I->comment("item = {$kensu}");
}