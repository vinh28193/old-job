<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 100レコード作る想定
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\JobTypeSmallFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
// テナントの職種大idのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\JobTypeBigFixture::RECORDS_PER_TENANT;
$jobTypeBigId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'job_type_small_name' => $faker->realText(30),
    'valid_chk' => $faker->numberBetween(0, 1),
    'sort' => $no,
    'job_type_big_id' => $jobTypeBigId,
    'job_type_small_no' => $no,
];