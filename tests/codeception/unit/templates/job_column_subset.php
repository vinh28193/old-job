<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 40レコード作る想定
 */
// tenantあたりのclient_masterのレコード数
$recordPerTenant = \tests\codeception\unit\fixtures\JobColumnSubsetFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'column_name' => 'option' . $faker->numberBetween(100, 109),
    'subset_name' => $faker->realText(30),
];