<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 500レコード作る想定（1テナントあたり100レコード）
 */

use tests\codeception\unit\fixtures\FreeContentElementFixture;
use tests\codeception\unit\fixtures\FreeContentFixture;

$id = $index + 1;

$recordPerTenant = FreeContentElementFixture::RECORDS_PER_TENANT;
$tenantId = (int)($index / $recordPerTenant) + 1;

$parentRecordPerTenant = FreeContentFixture::RECORDS_PER_TENANT;
$parentId = $faker->numberBetween(($tenantId - 1) * $parentRecordPerTenant + 1, $tenantId * $parentRecordPerTenant);

return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'free_content_id' => $parentId,
    'type' => $faker->numberBetween(1, 4),
    'image_file_name' => (new \DateTime('NOW'))->format('Y-m-d') . '_' . md5(uniqid()) . '.' . '.jpg',
    'text' => $faker->text(),
    'sort' => $id % $recordPerTenant,
    'created_at' => strtotime($faker->dateTimeBetween('now', '+2months')->format('Y-m-d H:i:s')),
    'updated_at' => strtotime($faker->dateTimeBetween('now', '+2months')->format('Y-m-d H:i:s')),
];
