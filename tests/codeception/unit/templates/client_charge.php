<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 100レコード作る想定（1テナントあたり50レコード）
 * 1掲載企業があるプランを複数持ってしまうような状態が発生する可能性があります。
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\ClientChargeFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
// テナントのプランのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\ClientChargePlanFixture::RECORDS_PER_TENANT;
$clientChargePlanId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
// テナントの掲載企業のみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\ClientMasterFixture::RECORDS_PER_TENANT;
$clientId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);

return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'client_charge_plan_id' => $clientChargePlanId,
    'client_master_id' => $clientId,
    'limit_num' => $faker->numberBetween(0, 255),
];