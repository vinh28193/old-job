<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 100レコード作る想定
 */
// tenantあたりのjob_masterのレコード数
$limit = \tests\codeception\unit\fixtures\JobMasterFixture::RECORDS_PER_TENANT;
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
// tenantあたりのclient_masterのレコード数
$limit = \tests\codeception\unit\fixtures\ClientMasterFixture::RECORDS_PER_TENANT;
switch ($tenantId) {
    case 1:
        $clientId = $faker->numberBetween(1, $limit);
        break;
    case 2:
        $clientId = $faker->numberBetween($limit * 1 + 1, $limit * 2);
        break;
    default:
        $clientId = null;
}
// tenantあたりのclient_charge_planのレコード数
$limit = \tests\codeception\unit\fixtures\ClientChargePlanFixture::RECORDS_PER_TENANT;
switch ($tenantId) {
    case 1:
        $clientChargePlanId = $faker->numberBetween(1, $limit);
        break;
    case 2:
        $clientChargePlanId = $faker->numberBetween($limit * 1 + 1, $limit * 2);
        break;
    default:
        $clientChargePlanId = null;
}
// 日付生成
$dispStartDate = $faker->numberBetween(1400000000, 1500000000);
$dispEndDate = $faker->numberBetween($dispStartDate, 1600000000);
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'job_no' => $no,
    'client_master_id' => $clientId,
    'corp_name_disp' => $faker->realText(50),
    'job_pr' => $faker->realText(50),
    'main_copy' => $faker->realText(50),
    'job_comment' => $faker->realText(50),
    'job_type_text' => $faker->realText(50),
    'work_place' => $faker->realText(50),
    'station' => $faker->realText(50),
    'transport' => $faker->realText(50),
    'wage_text' => $faker->realText(50),
    'requirement' => $faker->realText(50),
    'conditions' => $faker->realText(50),
    'holidays' => $faker->realText(50),
    'work_period' => $faker->realText(50),
    'work_time_text' => $faker->realText(50),
    'application' => $faker->realText(50),
    'application_tel_1' => $faker->phoneNumber,
    'application_tel_2' => $faker->phoneNumber,
    'application_mail'=>$faker->email,
    'application_place' => $faker->realText(50),
    'application_staff_name' => $faker->realText(30),
    'agent_name' => $faker->realText(30),
    'disp_start_date' => $dispStartDate,
    'disp_end_date' => $dispEndDate,
    'created_at' => $faker->unixTime,
    'valid_chk' => $faker->numberBetween(0, 1),
    'job_search_number' => $faker->realText(30),
    'job_pict_text_3' => $faker->realText(50),
    'job_pict_text_4' => $faker->realText(50),
    'job_pict_text_5' => $faker->realText(50),
    'map_url' => $faker->url,
    'mail_body' => $faker->realText(50),
    'updated_at' => $faker->unixTime,
    'main_copy2' => $faker->realText(50),
    'job_pr2' => $faker->realText(50),
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
    'client_charge_plan_id' => $clientChargePlanId,
    'job_review_status_id' => 6,
    'disp_type_sort' => $faker->numberBetween(1, 3),
    'media_upload_id_1'=>$faker->randomNumber(),
    'media_upload_id_2'=>$faker->randomNumber(),
    'media_upload_id_3'=>$faker->randomNumber(),
    'media_upload_id_4'=>$faker->randomNumber(),
    'media_upload_id_5'=>$faker->randomNumber(),
];
