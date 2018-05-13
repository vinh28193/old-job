<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 400レコード作る想定
 */
// tenantあたりのapplication_masterのレコード数
$limit = \tests\codeception\unit\fixtures\ApplicationMasterFixture::RECORDS_PER_TENANT;
$no = $index + 1;
// テナント設定
if ($no <= $limit) {
    $tenantId = 1;
} elseif ($no > $limit && $no <= $limit * 2) {
    $tenantId = 2;
    $no = $no - $limit;
} else {
    $tenantId = null;
}
// tenantあたりのjob_masterのレコード数
$limit = \tests\codeception\unit\fixtures\JobMasterFixture::RECORDS_PER_TENANT;
switch ($tenantId) {
    case 1:
        $jobMasterId = $faker->numberBetween(1, $limit);
        break;
    case 2:
        $jobMasterId = $faker->numberBetween($limit * 1 + 1, $limit * 2);
        break;
    default:
        $jobMasterId = null;
}
// tenantあたりのoccupationのレコード数
$limit = 5;
switch ($tenantId) {
    case 1:
        $occupationId = $faker->numberBetween(1, $limit);
        break;
    case 2:
        $occupationId = $faker->numberBetween($limit * 1 + 1, $limit * 2);
        break;
    default:
        $occupationId = null;
}
// tenantあたりのprefのレコード数
$limit = 48;
switch ($tenantId) {
    case 1:
        $prefId = $faker->numberBetween(1, $limit);
        break;
    case 2:
        $prefId = $faker->numberBetween($limit * 1 + 1, $limit * 2);
        break;
    default:
        $prefId = null;
}
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'application_no' => $no,
    'job_master_id' => $jobMasterId,
    'name_sei' => $faker->realText(30),
    'name_mei' => $faker->realText(30),
    'kana_sei' => $faker->realText(30),
    'kana_mei' => $faker->realText(30),
    'sex' => $faker->numberBetween(0, 1),
    'birth_date' => $faker->date("Y-m-d"),
    'pref_id' => $prefId,
    'address' => $faker->address,
    'tel_no' => $faker->phoneNumber,
    'mail_address' => $faker->email,
    'occupation_id' => $occupationId,
    'self_pr' => $faker->realText(50),
    'created_at' => $faker->unixTime,
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
    'application_status_id' => $faker->numberBetween(0, 3),
    'carrier_type' => $faker->numberBetween(0, 1),
    'application_memo' => $faker->realText(50),
];