<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 8レコード作る想定
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\WageCategoryFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'wage_category_name' => $faker->realText(30),
    'sort' => $no,
    'wage_category_no' => $no,
    'valid_chk' => $faker->numberBetween(0, 1),
    ];