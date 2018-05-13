<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 200レコード作る想定
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\JobWageFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
// テナントの仕事情報idのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\JobMasterFixture::RECORDS_PER_TENANT;
$jobMasterId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
// テナントの給与項目idのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\WageItemFixture::RECORDS_PER_TENANT;
$wageItemId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'job_master_id' => $jobMasterId,
    'wage_item_id' => $wageItemId,
];
