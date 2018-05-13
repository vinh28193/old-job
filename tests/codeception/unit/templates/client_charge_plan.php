<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 12レコード作る想定
 */

use tests\codeception\fixtures\DispTypeFixture;
use tests\codeception\unit\fixtures\ClientChargePlanFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;

// tenantあたりのclient_charge_planのレコード数
$recordPerTenant = ClientChargePlanFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
// テナントの掲載タイプのみを入れる
$recordPerTenant = DispTypeFixture::RECORDS_PER_TENANT;
$dispTypeId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);

return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'client_charge_plan_no' => $no,
    'client_charge_type' => $faker->numberBetween(1, 3),
    'disp_type_id' => $dispTypeId,
    'plan_name' => $faker->realText(50),
    'price' => $faker->numberBetween(10000, 1000000),
    'valid_chk' => $faker->numberBetween(0, 1),
    'period' => $faker->numberBetween(1, 31),
];