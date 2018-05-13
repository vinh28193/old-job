<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$recordPerTenant = tests\codeception\unit\fixtures\InquiryColumnSubsetFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'column_name' => 'option' . $faker->numberBetween(100, 109),
    'subset_name' => $faker->realText(30),
];