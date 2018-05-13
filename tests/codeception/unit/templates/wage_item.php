<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 40レコード作る想定
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\WageItemFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
// テナントの給与カテゴリidのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\WageCategoryFixture::RECORDS_PER_TENANT;
$wageCategoryId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
// 給与
$wageItemName = $faker->numberBetween(1000, 10000000);
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'wage_category_id' => $wageCategoryId,
    'wage_item_no' => $no,
    'wage_item_name' => $wageItemName,
    'sort' => $no,
    'valid_chk' => $faker->boolean(80),
    'disp_price' => $wageItemName . '円',
];
