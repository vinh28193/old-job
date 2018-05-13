<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$tenantId = $index + 1;
$limit = 200000;
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
$limit = 50000;
switch ($tenantId) {
        case 1:
                $jobMasterId = $faker->numberBetween(1, $limit);
                break;
        case 2:
                $jobMasterId = $faker->numberBetween($limit*1+1, $limit*2);
                break;
        case 3:
                $jobMasterId = $faker->numberBetween($limit*2+1, $limit*3);
                break;
        case 4:
                $jobMasterId = $faker->numberBetween($limit*3+1, $limit*4);
                break;
        case 5:
                $jobMasterId = $faker->numberBetween($limit*4+1, $limit*5);
                break;
        case 6:
                $jobMasterId = $faker->numberBetween($limit*5+1, $limit*6);
                break;
        case 7:
                $jobMasterId = $faker->numberBetween($limit*6+1, $limit*7);
                break;
        case 8:
                $jobMasterId = $faker->numberBetween($limit*7+1, $limit*8);
                break;
        case 9:
                $jobMasterId = $faker->numberBetween($limit*8+1, $limit*9);
                break;
        case 10:
                $jobMasterId = $faker->numberBetween($limit*9+1, $limit*10);
                break;
        case 11:
                $jobMasterId = $faker->numberBetween($limit*10+1, $limit*11);
                break;
        case 12:
                $jobMasterId = $faker->numberBetween($limit*11+1, $limit*12);
                break;
        case 13:
                $jobMasterId = $faker->numberBetween($limit*12+1, $limit*13);
                break;
        case 14:
                $jobMasterId = $faker->numberBetween($limit*13+1, $limit*14);
                break;
        case 15:
                $jobMasterId = $faker->numberBetween($limit*14+1, $limit*15);
                break;
        case 16:
                $jobMasterId = $faker->numberBetween($limit*15+1, $limit*16);
                break;
        case 17:
                $jobMasterId = $faker->numberBetween($limit*16+1, $limit*17);
                break;
        case 18:
                $jobMasterId = $faker->numberBetween($limit*17+1, $limit*18);
                break;
        case 19:
                $jobMasterId = $faker->numberBetween($limit*18+1, $limit*19);
                break;
        case 20:
                $jobMasterId = $faker->numberBetween($limit*19+1, $limit*20);
                break;
        case 21:
                $jobMasterId = $faker->numberBetween($limit*20+1, $limit*21);
                break;
        case 22:
                $jobMasterId = $faker->numberBetween($limit*21+1, $limit*22);
                break;
        case 23:
                $jobMasterId = $faker->numberBetween($limit*22+1, $limit*23);
                break;
        case 24:
                $jobMasterId = $faker->numberBetween($limit*23+1, $limit*24);
                break;
        case 25:
                $jobMasterId = $faker->numberBetween($limit*24+1, $limit*25);
                break;
        case 26:
                $jobMasterId = $faker->numberBetween($limit*25+1, $limit*26);
                break;
        case 27:
                $jobMasterId = $faker->numberBetween($limit*26+1, $limit*27);
                break;
        case 28:
                $jobMasterId = $faker->numberBetween($limit*27+1, $limit*28);
                break;
        case 29:
                $jobMasterId = $faker->numberBetween($limit*28+1, $limit*29);
                break;
        case 30:
                $jobMasterId = $faker->numberBetween($limit*29+1, $limit*30);
                break;
        case 31:
                $jobMasterId = $faker->numberBetween($limit*30+1, $limit*31);
                break;
        case 32:
                $jobMasterId = $faker->numberBetween($limit*31+1, $limit*32);
                break;
        case 33:
                $jobMasterId = $faker->numberBetween($limit*32+1, $limit*33);
                break;
        case 34:
                $jobMasterId = $faker->numberBetween($limit*33+1, $limit*34);
                break;
        case 35:
                $jobMasterId = $faker->numberBetween($limit*34+1, $limit*35);
                break;
        default:
                $jobMasterId = null;
}
$limit = 30;
switch ($tenantId) {
        case 1:
                $occupationId = $faker->numberBetween(1, $limit);
                break;
        case 2:
                $occupationId = $faker->numberBetween($limit*1+1, $limit*2);
                break;
        case 3:
                $occupationId = $faker->numberBetween($limit*2+1, $limit*3);
                break;
        case 4:
                $occupationId = $faker->numberBetween($limit*3+1, $limit*4);
                break;
        case 5:
                $occupationId = $faker->numberBetween($limit*4+1, $limit*5);
                break;
        case 6:
                $occupationId = $faker->numberBetween($limit*5+1, $limit*6);
                break;
        case 7:
                $occupationId = $faker->numberBetween($limit*6+1, $limit*7);
                break;
        case 8:
                $occupationId = $faker->numberBetween($limit*7+1, $limit*8);
                break;
        case 9:
                $occupationId = $faker->numberBetween($limit*8+1, $limit*9);
                break;
        case 10:
                $occupationId = $faker->numberBetween($limit*9+1, $limit*10);
                break;
        case 11:
                $occupationId = $faker->numberBetween($limit*10+1, $limit*11);
                break;
        case 12:
                $occupationId = $faker->numberBetween($limit*11+1, $limit*12);
                break;
        case 13:
                $occupationId = $faker->numberBetween($limit*12+1, $limit*13);
                break;
        case 14:
                $occupationId = $faker->numberBetween($limit*13+1, $limit*14);
                break;
        case 15:
                $occupationId = $faker->numberBetween($limit*14+1, $limit*15);
                break;
        case 16:
                $occupationId = $faker->numberBetween($limit*15+1, $limit*16);
                break;
        case 17:
                $occupationId = $faker->numberBetween($limit*16+1, $limit*17);
                break;
        case 18:
                $occupationId = $faker->numberBetween($limit*17+1, $limit*18);
                break;
        case 19:
                $occupationId = $faker->numberBetween($limit*18+1, $limit*19);
                break;
        case 20:
                $occupationId = $faker->numberBetween($limit*19+1, $limit*20);
                break;
        case 21:
                $occupationId = $faker->numberBetween($limit*20+1, $limit*21);
                break;
        case 22:
                $occupationId = $faker->numberBetween($limit*21+1, $limit*22);
                break;
        case 23:
                $occupationId = $faker->numberBetween($limit*22+1, $limit*23);
                break;
        case 24:
                $occupationId = $faker->numberBetween($limit*23+1, $limit*24);
                break;
        case 25:
                $occupationId = $faker->numberBetween($limit*24+1, $limit*25);
                break;
        case 26:
                $occupationId = $faker->numberBetween($limit*25+1, $limit*26);
                break;
        case 27:
                $occupationId = $faker->numberBetween($limit*26+1, $limit*27);
                break;
        case 28:
                $occupationId = $faker->numberBetween($limit*27+1, $limit*28);
                break;
        case 29:
                $occupationId = $faker->numberBetween($limit*28+1, $limit*29);
                break;
        case 30:
                $occupationId = $faker->numberBetween($limit*29+1, $limit*30);
                break;
        case 31:
                $occupationId = $faker->numberBetween($limit*30+1, $limit*31);
                break;
        case 32:
                $occupationId = $faker->numberBetween($limit*31+1, $limit*32);
                break;
        case 33:
                $occupationId = $faker->numberBetween($limit*32+1, $limit*33);
                break;
        case 34:
                $occupationId = $faker->numberBetween($limit*33+1, $limit*34);
                break;
        case 35:
                $occupationId = $faker->numberBetween($limit*34+1, $limit*35);
                break;
        default:
                $occupationId = null;
}
$limit = 30;
switch ($tenantId) {
        case 1:
                $applicationStatusId = $faker->numberBetween(1, $limit);
                break;
        case 2:
                $applicationStatusId = $faker->numberBetween($limit*1+1, $limit*2);
                break;
        case 3:
                $applicationStatusId = $faker->numberBetween($limit*2+1, $limit*3);
                break;
        case 4:
                $applicationStatusId = $faker->numberBetween($limit*3+1, $limit*4);
                break;
        case 5:
                $applicationStatusId = $faker->numberBetween($limit*4+1, $limit*5);
                break;
        case 6:
                $applicationStatusId = $faker->numberBetween($limit*5+1, $limit*6);
                break;
        case 7:
                $applicationStatusId = $faker->numberBetween($limit*6+1, $limit*7);
                break;
        case 8:
                $applicationStatusId = $faker->numberBetween($limit*7+1, $limit*8);
                break;
        case 9:
                $applicationStatusId = $faker->numberBetween($limit*8+1, $limit*9);
                break;
        case 10:
                $applicationStatusId = $faker->numberBetween($limit*9+1, $limit*10);
                break;
        case 11:
                $applicationStatusId = $faker->numberBetween($limit*10+1, $limit*11);
                break;
        case 12:
                $applicationStatusId = $faker->numberBetween($limit*11+1, $limit*12);
                break;
        case 13:
                $applicationStatusId = $faker->numberBetween($limit*12+1, $limit*13);
                break;
        case 14:
                $applicationStatusId = $faker->numberBetween($limit*13+1, $limit*14);
                break;
        case 15:
                $applicationStatusId = $faker->numberBetween($limit*14+1, $limit*15);
                break;
        case 16:
                $applicationStatusId = $faker->numberBetween($limit*15+1, $limit*16);
                break;
        case 17:
                $applicationStatusId = $faker->numberBetween($limit*16+1, $limit*17);
                break;
        case 18:
                $applicationStatusId = $faker->numberBetween($limit*17+1, $limit*18);
                break;
        case 19:
                $applicationStatusId = $faker->numberBetween($limit*18+1, $limit*19);
                break;
        case 20:
                $applicationStatusId = $faker->numberBetween($limit*19+1, $limit*20);
                break;
        case 21:
                $applicationStatusId = $faker->numberBetween($limit*20+1, $limit*21);
                break;
        case 22:
                $applicationStatusId = $faker->numberBetween($limit*21+1, $limit*22);
                break;
        case 23:
                $applicationStatusId = $faker->numberBetween($limit*22+1, $limit*23);
                break;
        case 24:
                $applicationStatusId = $faker->numberBetween($limit*23+1, $limit*24);
                break;
        case 25:
                $applicationStatusId = $faker->numberBetween($limit*24+1, $limit*25);
                break;
        case 26:
                $applicationStatusId = $faker->numberBetween($limit*25+1, $limit*26);
                break;
        case 27:
                $applicationStatusId = $faker->numberBetween($limit*26+1, $limit*27);
                break;
        case 28:
                $applicationStatusId = $faker->numberBetween($limit*27+1, $limit*28);
                break;
        case 29:
                $applicationStatusId = $faker->numberBetween($limit*28+1, $limit*29);
                break;
        case 30:
                $applicationStatusId = $faker->numberBetween($limit*29+1, $limit*30);
                break;
        case 31:
                $applicationStatusId = $faker->numberBetween($limit*30+1, $limit*31);
                break;
        case 32:
                $applicationStatusId = $faker->numberBetween($limit*31+1, $limit*32);
                break;
        case 33:
                $applicationStatusId = $faker->numberBetween($limit*32+1, $limit*33);
                break;
        case 34:
                $applicationStatusId = $faker->numberBetween($limit*33+1, $limit*34);
                break;
        case 35:
                $applicationStatusId = $faker->numberBetween($limit*34+1, $limit*35);
                break;
        default:
                $applicationStatusId = null;
}
return [
            'id' => $index + 1,
            'tenant_id' => $tenantId,
            'application_no' => $no,
            'job_master_id' => $jobMasterId,
            'name_sei' => $faker->realText(255),
            'name_mei' => $faker->realText(255),
            'kana_sei' => $faker->realText(255),
            'kana_mei' => $faker->realText(255),
            'sex' => $faker->boolean(4),
            'birth_date' => $faker->date("Y-m-d"),
            'pref_id' => $faker->numberBetween(1, 96),
            'address' => $faker->realText(),
            'tel_no' => $faker->realText(30),
            'mail_address_flg' => $faker->boolean(4),
            'mail_address' => $faker->realText(32),
            'occupation_id' => $occupationId,
            'self_pr' => $faker->realText(),
            'created_at' => $faker->unixTime,
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
            'application_status_id' => $applicationStatusId,
            'carrier_type' => $faker->boolean(4),
            'application_memo' => $faker->realText(),
        ];