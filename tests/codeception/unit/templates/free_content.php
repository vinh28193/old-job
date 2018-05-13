<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 50レコード作る想定（1テナントあたり10レコード）
 */

use tests\codeception\unit\fixtures\FreeContentFixture;

$id = $index + 1;

$recordPerTenant = FreeContentFixture::RECORDS_PER_TENANT;
$tenantId = (int)($index / $recordPerTenant) + 1;

return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'title' => $faker->title(),
    'keyword' => implode(',', $faker->words(10)),
    'description' => $faker->text(),
    'url_directory' => $faker->word . $id,
    'valid_chk' => $faker->boolean(70),
    'created_at' => strtotime($faker->dateTimeBetween('now', '+2months')->format('Y-m-d H:i:s')),
    'updated_at' => strtotime($faker->dateTimeBetween('now', '+2months')->format('Y-m-d H:i:s')),
];
