<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$tenantId = $index + 1;
$limit = 500000;
$no = $index + 1;
if($tenantId <= $limit){
    $tenantId = 1;
}elseif ($tenantId > $limit && $tenantId <= $limit*2 ){
    $tenantId = 2;
    $no = $no - $limit;
}elseif ($tenantId > $limit*2 && $tenantId <= $limit*3 ){
    $tenantId = 3;
    $no = $no - $limit*2;
}elseif ($tenantId > $limit*3 && $tenantId <= $limit*4 ){
    $tenantId = 4;
    $no = $no - $limit*3;
}elseif ($tenantId > $limit*4 && $tenantId <= $limit*5 ) {
    $tenantId = 5;
    $no = $no - $limit*4;
}elseif ($tenantId > $limit*5 && $tenantId <= $limit*6 ) {
    $tenantId = 6;
    $no = $no - $limit*5;
}elseif ($tenantId > $limit*6 && $tenantId <= $limit*7 ) {
    $tenantId = 7;
    $no = $no - $limit*6;
}elseif ($tenantId > $limit*7 && $tenantId <= $limit*8 ) {
    $tenantId = 8;
    $no = $no - $limit*7;
}elseif ($tenantId > $limit*8 && $tenantId <= $limit*9 ) {
    $tenantId = 9;
    $no = $no - $limit*8;
}elseif ($tenantId > $limit*9 && $tenantId <= $limit*10 ) {
    $tenantId = 10;
    $no = $no - $limit*9;
}elseif ($tenantId > $limit*10 && $tenantId <= $limit*11 ) {
    $tenantId = 11;
    $no = $no - $limit*10;
}elseif ($tenantId > $limit*11 && $tenantId <= $limit*12 ) {
    $tenantId = 12;
    $no = $no - $limit*11;
}elseif ($tenantId > $limit*12 && $tenantId <= $limit*13 ) {
    $tenantId = 13;
    $no = $no - $limit*12;
}elseif ($tenantId > $limit*13 && $tenantId <= $limit*14 ) {
    $tenantId = 14;
    $no = $no - $limit*13;
}elseif ($tenantId > $limit*14 && $tenantId <= $limit*15 ) {
    $tenantId = 15;
    $no = $no - $limit*14;
}elseif ($tenantId > $limit*15 && $tenantId <= $limit*16 ) {
    $tenantId = 16;
    $no = $no - $limit*15;
}elseif ($tenantId > $limit*16 && $tenantId <= $limit*17 ) {
    $tenantId = 17;
    $no = $no - $limit*16;
}elseif ($tenantId > $limit*17 && $tenantId <= $limit*18 ) {
    $tenantId = 18;
    $no = $no - $limit*17;
}elseif ($tenantId > $limit*18 && $tenantId <= $limit*19 ) {
    $tenantId = 19;
    $no = $no - $limit*18;
}elseif ($tenantId > $limit*19 && $tenantId <= $limit*20 ) {
    $tenantId = 20;
    $no = $no - $limit*19;
}elseif ($tenantId > $limit*20 && $tenantId <= $limit*21 ) {
    $tenantId = 21;
    $no = $no - $limit*20;
}elseif ($tenantId > $limit*21 && $tenantId <= $limit*22 ) {
    $tenantId = 22;
    $no = $no - $limit*21;
}elseif ($tenantId > $limit*22 && $tenantId <= $limit*23 ) {
    $tenantId = 23;
    $no = $no - $limit*22;
}elseif ($tenantId > $limit*23 && $tenantId <= $limit*24 ) {
    $tenantId = 24;
    $no = $no - $limit*23;
}elseif ($tenantId > $limit*24 && $tenantId <= $limit*25 ) {
    $tenantId = 25;
    $no = $no - $limit*24;
}elseif ($tenantId > $limit*25 && $tenantId <= $limit*26 ) {
    $tenantId = 26;
    $no = $no - $limit*25;
}elseif ($tenantId > $limit*26 && $tenantId <= $limit*27 ) {
    $tenantId = 27;
    $no = $no - $limit*26;
}elseif ($tenantId > $limit*27 && $tenantId <= $limit*28 ) {
    $tenantId = 28;
    $no = $no - $limit*27;
}elseif ($tenantId > $limit*28 && $tenantId <= $limit*29 ) {
    $tenantId = 29;
    $no = $no - $limit*28;
}elseif ($tenantId > $limit*29 && $tenantId <= $limit*30 ) {
    $tenantId = 30;
    $no = $no - $limit*29;
}elseif ($tenantId > $limit*30 && $tenantId <= $limit*31 ) {
    $tenantId = 31;
    $no = $no - $limit*30;
}elseif ($tenantId > $limit*31 && $tenantId <= $limit*32 ) {
    $tenantId = 32;
    $no = $no - $limit*31;
}elseif ($tenantId > $limit*32 && $tenantId <= $limit*33 ) {
    $tenantId = 33;
    $no = $no - $limit*32;
}elseif ($tenantId > $limit*33 && $tenantId <= $limit*34 ) {
    $tenantId = 34;
    $no = $no - $limit*33;
}elseif ($tenantId > $limit*34 ) {
    $tenantId = 35;
    $no = $no - $limit*34;
}
$limit = 30;
switch ($tenantId) {
    case 1:
        $dispTypeId = $faker->numberBetween(1, $limit);
        break;
    case 2:
        $dispTypeId = $faker->numberBetween($limit*1+1, $limit*2);
        break;
    case 3:
        $dispTypeId = $faker->numberBetween($limit*2+1, $limit*3);
        break;
    case 4:
        $dispTypeId = $faker->numberBetween($limit*3+1, $limit*4);
        break;
    case 5:
        $dispTypeId = $faker->numberBetween($limit*4+1, $limit*5);
        break;
    case 6:
        $dispTypeId = $faker->numberBetween($limit*5+1, $limit*6);
        break;
    case 7:
        $dispTypeId = $faker->numberBetween($limit*6+1, $limit*7);
        break;
    case 8:
        $dispTypeId = $faker->numberBetween($limit*7+1, $limit*8);
        break;
    case 9:
        $dispTypeId = $faker->numberBetween($limit*8+1, $limit*9);
        break;
    case 10:
        $dispTypeId = $faker->numberBetween($limit*9+1, $limit*10);
        break;
    case 11:
        $dispTypeId = $faker->numberBetween($limit*10+1, $limit*11);
        break;
    case 12:
        $dispTypeId = $faker->numberBetween($limit*11+1, $limit*12);
        break;
    case 13:
        $dispTypeId = $faker->numberBetween($limit*12+1, $limit*13);
        break;
    case 14:
        $dispTypeId = $faker->numberBetween($limit*13+1, $limit*14);
        break;
    case 15:
        $dispTypeId = $faker->numberBetween($limit*14+1, $limit*15);
        break;
    case 16:
        $dispTypeId = $faker->numberBetween($limit*15+1, $limit*16);
        break;
    case 17:
        $dispTypeId = $faker->numberBetween($limit*16+1, $limit*17);
        break;
    case 18:
        $dispTypeId = $faker->numberBetween($limit*17+1, $limit*18);
        break;
    case 19:
        $dispTypeId = $faker->numberBetween($limit*18+1, $limit*19);
        break;
    case 20:
        $dispTypeId = $faker->numberBetween($limit*19+1, $limit*20);
        break;
    case 21:
        $dispTypeId = $faker->numberBetween($limit*20+1, $limit*21);
        break;
    case 22:
        $dispTypeId = $faker->numberBetween($limit*21+1, $limit*22);
        break;
    case 23:
        $dispTypeId = $faker->numberBetween($limit*22+1, $limit*23);
        break;
    case 24:
        $dispTypeId = $faker->numberBetween($limit*23+1, $limit*24);
        break;
    case 25:
        $dispTypeId = $faker->numberBetween($limit*24+1, $limit*25);
        break;
    case 26:
        $dispTypeId = $faker->numberBetween($limit*25+1, $limit*26);
        break;
    case 27:
        $dispTypeId = $faker->numberBetween($limit*26+1, $limit*27);
        break;
    case 28:
        $dispTypeId = $faker->numberBetween($limit*27+1, $limit*28);
        break;
    case 29:
        $dispTypeId = $faker->numberBetween($limit*28+1, $limit*29);
        break;
    case 30:
        $dispTypeId = $faker->numberBetween($limit*29+1, $limit*30);
        break;
    case 31:
        $dispTypeId = $faker->numberBetween($limit*30+1, $limit*31);
        break;
    case 32:
        $dispTypeId = $faker->numberBetween($limit*31+1, $limit*32);
        break;
    case 33:
        $dispTypeId = $faker->numberBetween($limit*32+1, $limit*33);
        break;
    case 34:
        $dispTypeId = $faker->numberBetween($limit*33+1, $limit*34);
        break;
    case 35:
        $dispTypeId = $faker->numberBetween($limit*34+1, $limit*35);
        break;
    default:
        $dispTypeId = null;
}
$limit = 10000;
switch ($tenantId) {
    case 1:
        $clientId = $faker->numberBetween(1, $limit);
        break;
    case 2:
        $clientId = $faker->numberBetween($limit*1+1, $limit*2);
        break;
    case 3:
        $clientId = $faker->numberBetween($limit*2+1, $limit*3);
        break;
    case 4:
        $clientId = $faker->numberBetween($limit*3+1, $limit*4);
        break;
    case 5:
        $clientId = $faker->numberBetween($limit*4+1, $limit*5);
        break;
    case 6:
        $clientId = $faker->numberBetween($limit*5+1, $limit*6);
        break;
    case 7:
        $clientId = $faker->numberBetween($limit*6+1, $limit*7);
        break;
    case 8:
        $clientId = $faker->numberBetween($limit*7+1, $limit*8);
        break;
    case 9:
        $clientId = $faker->numberBetween($limit*8+1, $limit*9);
        break;
    case 10:
        $clientId = $faker->numberBetween($limit*9+1, $limit*10);
        break;
    case 11:
        $clientId = $faker->numberBetween($limit*10+1, $limit*11);
        break;
    case 12:
        $clientId = $faker->numberBetween($limit*11+1, $limit*12);
        break;
    case 13:
        $clientId = $faker->numberBetween($limit*12+1, $limit*13);
        break;
    case 14:
        $clientId = $faker->numberBetween($limit*13+1, $limit*14);
        break;
    case 15:
        $clientId = $faker->numberBetween($limit*14+1, $limit*15);
        break;
    case 16:
        $clientId = $faker->numberBetween($limit*15+1, $limit*16);
        break;
    case 17:
        $clientId = $faker->numberBetween($limit*16+1, $limit*17);
        break;
    case 18:
        $clientId = $faker->numberBetween($limit*17+1, $limit*18);
        break;
    case 19:
        $clientId = $faker->numberBetween($limit*18+1, $limit*19);
        break;
    case 20:
        $clientId = $faker->numberBetween($limit*19+1, $limit*20);
        break;
    case 21:
        $clientId = $faker->numberBetween($limit*20+1, $limit*21);
        break;
    case 22:
        $clientId = $faker->numberBetween($limit*21+1, $limit*22);
        break;
    case 23:
        $clientId = $faker->numberBetween($limit*22+1, $limit*23);
        break;
    case 24:
        $clientId = $faker->numberBetween($limit*23+1, $limit*24);
        break;
    case 25:
        $clientId = $faker->numberBetween($limit*24+1, $limit*25);
        break;
    case 26:
        $clientId = $faker->numberBetween($limit*25+1, $limit*26);
        break;
    case 27:
        $clientId = $faker->numberBetween($limit*26+1, $limit*27);
        break;
    case 28:
        $clientId = $faker->numberBetween($limit*27+1, $limit*28);
        break;
    case 29:
        $clientId = $faker->numberBetween($limit*28+1, $limit*29);
        break;
    case 30:
        $clientId = $faker->numberBetween($limit*29+1, $limit*30);
        break;
    case 31:
        $clientId = $faker->numberBetween($limit*30+1, $limit*31);
        break;
    case 32:
        $clientId = $faker->numberBetween($limit*31+1, $limit*32);
        break;
    case 33:
        $clientId = $faker->numberBetween($limit*32+1, $limit*33);
        break;
    case 34:
        $clientId = $faker->numberBetween($limit*33+1, $limit*34);
        break;
    case 35:
        $clientId = $faker->numberBetween($limit*34+1, $limit*35);
        break;
    default:
        $clientId = null;
}
$limit = 30;
switch ($tenantId) {
    case 1:
        $clientChargePlanId = $faker->numberBetween(1, $limit);
        break;
    case 2:
        $clientChargePlanId = $faker->numberBetween($limit*1+1, $limit*2);
        break;
    case 3:
        $clientChargePlanId = $faker->numberBetween($limit*2+1, $limit*3);
        break;
    case 4:
        $clientChargePlanId = $faker->numberBetween($limit*3+1, $limit*4);
        break;
    case 5:
        $clientChargePlanId = $faker->numberBetween($limit*4+1, $limit*5);
        break;
    case 6:
        $clientChargePlanId = $faker->numberBetween($limit*5+1, $limit*6);
        break;
    case 7:
        $clientChargePlanId = $faker->numberBetween($limit*6+1, $limit*7);
        break;
    case 8:
        $clientChargePlanId = $faker->numberBetween($limit*7+1, $limit*8);
        break;
    case 9:
        $clientChargePlanId = $faker->numberBetween($limit*8+1, $limit*9);
        break;
    case 10:
        $clientChargePlanId = $faker->numberBetween($limit*9+1, $limit*10);
        break;
    case 11:
        $clientChargePlanId = $faker->numberBetween($limit*10+1, $limit*11);
        break;
    case 12:
        $clientChargePlanId = $faker->numberBetween($limit*11+1, $limit*12);
        break;
    case 13:
        $clientChargePlanId = $faker->numberBetween($limit*12+1, $limit*13);
        break;
    case 14:
        $clientChargePlanId = $faker->numberBetween($limit*13+1, $limit*14);
        break;
    case 15:
        $clientChargePlanId = $faker->numberBetween($limit*14+1, $limit*15);
        break;
    case 16:
        $clientChargePlanId = $faker->numberBetween($limit*15+1, $limit*16);
        break;
    case 17:
        $clientChargePlanId = $faker->numberBetween($limit*16+1, $limit*17);
        break;
    case 18:
        $clientChargePlanId = $faker->numberBetween($limit*17+1, $limit*18);
        break;
    case 19:
        $clientChargePlanId = $faker->numberBetween($limit*18+1, $limit*19);
        break;
    case 20:
        $clientChargePlanId = $faker->numberBetween($limit*19+1, $limit*20);
        break;
    case 21:
        $clientChargePlanId = $faker->numberBetween($limit*20+1, $limit*21);
        break;
    case 22:
        $clientChargePlanId = $faker->numberBetween($limit*21+1, $limit*22);
        break;
    case 23:
        $clientChargePlanId = $faker->numberBetween($limit*22+1, $limit*23);
        break;
    case 24:
        $clientChargePlanId = $faker->numberBetween($limit*23+1, $limit*24);
        break;
    case 25:
        $clientChargePlanId = $faker->numberBetween($limit*24+1, $limit*25);
        break;
    case 26:
        $clientChargePlanId = $faker->numberBetween($limit*25+1, $limit*26);
        break;
    case 27:
        $clientChargePlanId = $faker->numberBetween($limit*26+1, $limit*27);
        break;
    case 28:
        $clientChargePlanId = $faker->numberBetween($limit*27+1, $limit*28);
        break;
    case 29:
        $clientChargePlanId = $faker->numberBetween($limit*28+1, $limit*29);
        break;
    case 30:
        $clientChargePlanId = $faker->numberBetween($limit*29+1, $limit*30);
        break;
    case 31:
        $clientChargePlanId = $faker->numberBetween($limit*30+1, $limit*31);
        break;
    case 32:
        $clientChargePlanId = $faker->numberBetween($limit*31+1, $limit*32);
        break;
    case 33:
        $clientChargePlanId = $faker->numberBetween($limit*32+1, $limit*33);
        break;
    case 34:
        $clientChargePlanId = $faker->numberBetween($limit*33+1, $limit*34);
        break;
    case 35:
        $clientChargePlanId = $faker->numberBetween($limit*34+1, $limit*35);
        break;
    default:
        $clientChargePlanId = null;
}
$validChk = $no <= 10000 ? 0 : 1 ;
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'job_no' => $no,
    'disp_type_id' => $dispTypeId,
    'client_master_id' => $clientId,
    'corp_name_disp' => $faker->realText(255),
    'job_pr' => $faker->realText(),
    'main_copy' => $faker->realText(),
    'job_comment' => $faker->realText(),
    'job_pict_1' =>  $faker->realText(255),
    'job_pict_2' => $faker->realText(255),
    'job_pict_3' => $faker->realText(255),
    'job_type_text' => $faker->realText(),
    'work_place' => $faker->realText(),
    'station' => $faker->realText(),
    'transport' => $faker->realText(),
    'wage_text' => $faker->realText(),
    'requirement' => $faker->realText(),
    'conditions' => $faker->realText(),
    'holidays' => $faker->realText(),
    'work_period' => $faker->realText(),
    'work_time_text' => $faker->realText(),
    'application' => $faker->realText(),
    'application_tel_1' => $faker->realText(255),
    'application_tel_2' => $faker->realText(255),
    'application_place' => $faker->realText(),
    'application_staff_name' => $faker->realText(255),
    'agent_name' => $faker->realText(255),
    'disp_start_date' => $faker->date('Y-m-d'),
    'disp_end_date' => $faker->date('Y-m-d'),
    'created_at' => $faker ->unixTime,
    'valid_chk' => $validChk,
    'job_search_number' => $faker->realText(255),
    'job_pict_text_2' => $faker->realText(),
    'job_pict_text_3' => $faker->realText(),
    'map_url' => $faker->realText(),
    'mail_body' => $faker->realText(),
    'updated_at' => $faker ->unixTime,
    'job_pict_text_4' => $faker->realText(),
    'job_pict_4' => $faker->realText(255),
    'main_copy2' => $faker->realText(),
    'job_pr2' => $faker->realText(),
    'option100' => $faker->realText(),
    'option101' => $faker->realText(),
    'option102' => $faker->realText(),
    'option103' => $faker->realText(),
    'option104' => $faker->realText(),
    'option105' => $faker->realText(),
    'option106' => $faker->realText(),
    'option107' => $faker->realText(),
    'option108' => $faker->realText(),
    'option109' => $faker->realText(),
    'client_charge_plan_id' => $clientChargePlanId,
    'job_review_status_id' => $faker->boolean(6),
    'sample_pict_flg_1' => $faker->boolean(1),
    'sample_pict_flg_2' => $faker->boolean(1),
    'sample_pict_flg_3' => $faker->boolean(1),
    'sample_pict_flg_4' => $faker->boolean(1),
    'sample_pict_flg_5' => $faker->boolean(1),
  ];
