<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$tenantId = $faker->numberBetween(1, 2);

$corpId = $tenantId == 1 ? $faker->numberBetween(1, 2) : $faker->numberBetween(3, 4);

switch ($corpId) {
    case 1:
        $clientId = $faker->numberBetween(1, 2);
        break;
    case 2:
        $clientId = $faker->numberBetween(3, 4);
        break;
    case 3:
        $clientId = $faker->numberBetween(5, 6);
        break;
    case 4:
        $clientId = $faker->numberBetween(7, 8);
        break;
    default:
        $clientId = null;
}

return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'created_at' => $faker->unixTime,
    'job_master_id' => $faker->numberBetween(2, 100), // tenant_idによるデータ不整合が起こる可能性あり
    'client_master_id' => $clientId,
    'corp_master_id' => $corpId,
    'job_type_big_cds' => $faker->numberBetween(2, 30), // tenant_idによるデータ不整合が起こる可能性あり
    'job_type_small_cds' => $faker->numberBetween(2, 100), // tenant_idによるデータ不整合が起こる可能性あり
    'pref_cds' => $faker->numberBetween(1, 94), // tenant_idによるデータ不整合が起こる可能性あり
    'carrier_type' => $faker->numberBetween(0, 1),
    'access_type' => $faker->numberBetween(0, 1),
    'access_user_agent' => $faker->userAgent,
];