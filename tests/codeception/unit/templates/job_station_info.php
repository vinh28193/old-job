<?php
/**
 * 路線テストテンプレート
 * @var $faker \Faker\Generator
 * @var $index integer
 * 160レコード作る想定
 * ごくごく稀に重複レコードが出来る場合あり
 * 4つ以上の駅と紐づく求人原稿が出来る場合あり
 */
// セッティング
use yii\helpers\ArrayHelper;

$recordPerTenant = \tests\codeception\unit\fixtures\JobStationInfoFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
// テナントの仕事情報idのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\JobMasterFixture::RECORDS_PER_TENANT;
$jobMasterId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
// 存在する駅No.を入れる
$stations = require(__DIR__ . '/../../fixtures/data/station.php');
$stationNos = ArrayHelper::getColumn($stations, 'station_no');
$stationId = $faker->randomElement($stationNos);
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'job_master_id' => $jobMasterId,
    'station_id' => $stationId,
    'transport_type' => $faker->numberBetween(0, 1),
    'transport_time' => $faker->numberBetween(0, 60),
];
