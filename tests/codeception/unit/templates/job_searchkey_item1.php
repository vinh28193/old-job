<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 400レコード作る想定
 */
// セッティング
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\fixtures\JobSearchkeyItem1Fixture;

$recordPerTenant = JobSearchkeyItem1Fixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
// テナントの仕事情報idのみを入れる
$recordPerTenant = JobMasterFixture::RECORDS_PER_TENANT;
$jobMasterId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
// テナントのitem_idのみを入れる
$recordPerTenant = 33;
$itemId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'job_master_id' => $jobMasterId,
    'searchkey_item_id' => $itemId,
];
