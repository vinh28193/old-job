<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 400レコード作る想定
 * ごく稀にどの都道府県とも結びついてないレコードが出来てしまう
 * job_distとの関係はハチャメチャ
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\JobPrefFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
// テナントの仕事情報idのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\JobMasterFixture::RECORDS_PER_TENANT;
$jobMasterId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
// テナントの都道府県idのみを入れる
$recordPerTenant = \tests\codeception\fixtures\PrefFixture::RECORDS_PER_TENANT;
$prefId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'job_master_id' => $jobMasterId,
    'pref_id' => $prefId,
];