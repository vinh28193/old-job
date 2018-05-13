<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 400レコード作る想定
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\JobTypeFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
// テナントの仕事情報idのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\JobMasterFixture::RECORDS_PER_TENANT;
$jobMasterId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
// テナントの職種小idのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\JobTypeSmallFixture::RECORDS_PER_TENANT;
$jobTypeSmallId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'job_master_id' => $jobMasterId,
    'job_type_small_id' => $jobTypeSmallId,
];
