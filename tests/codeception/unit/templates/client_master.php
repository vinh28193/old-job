<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 30レコード作る想定
 */
use tests\codeception\unit\fixtures\ClientMasterFixture;
use tests\codeception\unit\fixtures\CorpMasterFixture;
// セッティング
$recordPerTenant = ClientMasterFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
// テナントのcorpMasterIdのみを入れる(id=1のものは必ずcorp_master_id=1)
$recordPerTenant = CorpMasterFixture::RECORDS_PER_TENANT;
if ($id == 1) {
    $corpMasterId = 1;
} else {
    $corpMasterId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
}

return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'client_no' => $no,
    'corp_master_id' => $corpMasterId,
    'client_name' => $faker->realText(30),
    'client_name_kana' => $faker->realText(30),
    'tel_no' => $faker->phoneNumber,
    'address' => $faker->address,
    'tanto_name' => $faker->realText(255),
    'created_at' => $faker->unixTime,
    'valid_chk' => $faker->numberBetween(0, 1),
    'client_business_outline' => $faker->realText(50),
    'client_corporate_url' => $faker->url,
    'admin_memo' => $faker->realText(50),
    'option100' => $faker->realText(50),
    'option101' => $faker->realText(50),
    'option102' => $faker->realText(50),
    'option103' => $faker->realText(50),
    'option104' => $faker->realText(50),
    'option105' => $faker->realText(50),
    'option106' => $faker->realText(50),
    'option107' => $faker->realText(50),
    'option108' => $faker->realText(50),
    'option109' => $faker->realText(50),
];