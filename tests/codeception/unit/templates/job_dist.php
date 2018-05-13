<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 2000レコード作る想定
 * ごく稀にどの市区町村とも結びついてないレコードが出来てしまう
 * job_prefとの関係はハチャメチャ
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\JobDistFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
// テナントの仕事情報idのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\JobMasterFixture::RECORDS_PER_TENANT;
$jobMasterId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'job_master_id' => $jobMasterId,
    'dist_id' => $faker->numberBetween(1, 1897),
];
