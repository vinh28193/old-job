<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 6レコード作る想定
 */
// tenantあたりのclient_masterのレコード数
$limit = \tests\codeception\unit\fixtures\CorpMasterFixture::RECORDS_PER_TENANT;
$no = $index + 1;
// テナント設定
if($no <= $limit){
    $tenantId = 1;
}elseif ($no > $limit && $no <= $limit*2 ){
    $tenantId = 2;
    $no = $no - $limit;
}
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'corp_no' => $no,
    'corp_name' => $faker->realText(30),
    'created_at' => $faker->unixTime,
    'tel_no' => $faker->realText(30),
    'tanto_name' => $faker->realText(30),
    'corp_review_flg' => $faker->numberBetween(0, 1),
    'valid_chk' => $faker->numberBetween(0, 1),
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