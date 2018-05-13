<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 20レコード作る想定
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\JobTypeBigFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
// テナントの職種カテゴリidのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\JobTypeCategoryFixture::RECORDS_PER_TENANT;
$jobTypeCategoryId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'job_type_big_name' => $faker->realText(30),
    'valid_chk' => $faker->numberBetween(0, 1),
    'sort' => $no,
    'job_type_category_id' => $jobTypeCategoryId,
    'job_type_big_no' => $no,
];