<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 4レコード作る想定
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\JobTypeCategoryFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'job_type_category_cd' => $no,
    'name' => $faker->realText(30),
    'sort' => $no,
    'valid_chk' => 1,
];