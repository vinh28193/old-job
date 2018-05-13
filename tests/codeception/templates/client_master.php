<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$tenantId = $index + 1;
$limit = 10000;
$no = $index + 1;
$corp =5000;
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
switch ($tenantId) {
    case 1:
        $corpMasterId = $index + 1 <= $corp ? 1 : $faker->numberBetween(2,$limit*1);
        break;
    case 2:
        $corpMasterId = $index + 1 <= $limit*1+$corp ? $limit*1+1 : $faker->numberBetween($limit*1+2,$limit*2);
        break;
    case 3:
        $corpMasterId = $index + 1 <= $limit*2+$corp ? $limit*2+1: $faker->numberBetween($limit*2+2,$limit*3);
        break;
    case 4:
        $corpMasterId = $index + 1 <= $limit*3+$corp ? $limit*3+1 : $faker->numberBetween($limit*3+2,$limit*4);
        break;
    case 5:
        $corpMasterId = $index + 1 <= $limit*4+$corp ? $limit*4+1 : $faker->numberBetween($limit*4+2,$limit*5);
        break;
    case 6:
        $corpMasterId = $index + 1 <= $limit*5+$corp ? $limit*5+1 : $faker->numberBetween($limit*5+2,$limit*6);
        break;
    case 7:
        $corpMasterId = $index + 1 <= $limit*6+$corp ? $limit*6+1 : $faker->numberBetween($limit*6+2,$limit*7);
        break;
    case 8:
        $corpMasterId = $index + 1 <= $limit*7+$corp ? $limit*7+1 : $faker->numberBetween($limit*7+2,$limit*8);
        break;
    case 9:
        $corpMasterId = $index + 1 <= $limit*8+$corp ? $limit*8+1 : $faker->numberBetween($limit*8+2,$limit*9);
        break;
    case 10:
        $corpMasterId = $index + 1 <= $limit*9+$corp ? $limit*9+1 : $faker->numberBetween($limit*9+2,$limit*10);
        break;
    case 11:
        $corpMasterId = $index + 1 <= $limit*10+$corp ? $limit*10+1 : $faker->numberBetween($limit*10+2,$limit*11);
        break;
    case 12:
        $corpMasterId = $index + 1 <= $limit*11+$corp ? $limit*11+1 : $faker->numberBetween($limit*11+2,$limit*12);
        break;
    case 13:
        $corpMasterId = $index + 1 <= $limit*12+$corp ? $limit*12+1 : $faker->numberBetween($limit*12+2,$limit*13);
        break;
    case 14:
        $corpMasterId = $index + 1 <= $limit*13+$corp ? $limit*13+1 : $faker->numberBetween($limit*13+2,$limit*14);
        break;
    case 15:
        $corpMasterId = $index + 1 <= $limit*14+$corp ? $limit*14+1 : $faker->numberBetween($limit*14+2,$limit*15);
        break;
    case 16:
        $corpMasterId = $index + 1 <= $limit*15+$corp ? $limit*15+1 : $faker->numberBetween($limit*15+2,$limit*16);
        break;
    case 17:
        $corpMasterId = $index + 1 <= $limit*16+$corp ? $limit*16+1 : $faker->numberBetween($limit*16+2,$limit*17);
        break;
    case 18:
        $corpMasterId = $index + 1 <= $limit*17+$corp ? $limit*17+1 : $faker->numberBetween($limit*17+2,$limit*18);
        break;
    case 19:
        $corpMasterId = $index + 1 <= $limit*18+$corp ? $limit*18+1 : $faker->numberBetween($limit*18+2,$limit*19);
        break;
    case 20:
        $corpMasterId = $index + 1 <= $limit*19+$corp ? $limit*19+1 : $faker->numberBetween($limit*19+2,$limit*20);
        break;
    case 21:
        $corpMasterId = $index + 1 <= $limit*20+$corp ? $limit*20+1 : $faker->numberBetween($limit*20+2,$limit*21);
        break;
    case 22:
        $corpMasterId = $index + 1 <= $limit*21+$corp ? $limit*21+1 : $faker->numberBetween($limit*21+2,$limit*22);
        break;
    case 23:
        $corpMasterId = $index + 1 <= $limit*22+$corp ? $limit*22+1 : $faker->numberBetween($limit*22+2,$limit*23);
        break;
    case 24:
        $corpMasterId = $index + 1 <= $limit*23+$corp ? $limit*23+1 : $faker->numberBetween($limit*23+2,$limit*24);
        break;
    case 25:
        $corpMasterId = $index + 1 <= $limit*24+$corp ? $limit*24+1 : $faker->numberBetween($limit*24+2,$limit*25);
        break;
    case 26:
        $corpMasterId = $index + 1 <= $limit*25+$corp ? $limit*25+1 : $faker->numberBetween($limit*25+2,$limit*26);
        break;
    case 27:
        $corpMasterId = $index + 1 <= $limit*26+$corp ? $limit*26+1 : $faker->numberBetween($limit*26+2,$limit*27);
        break;
    case 28:
        $corpMasterId = $index + 1 <= $limit*27+$corp ? $limit*27+1 : $faker->numberBetween($limit*27+2,$limit*28);
        break;
    case 29:
        $corpMasterId = $index + 1 <= $limit*28+$corp ? $limit*28+1 : $faker->numberBetween($limit*28+2,$limit*29);
        break;
    case 30:
        $corpMasterId = $index + 1 <= $limit*29+$corp ? $limit*29+1 : $faker->numberBetween($limit*29+2,$limit*30);
        break;
    case 31:
        $corpMasterId = $index + 1 <= $limit*30+$corp ? $limit*30+1 : $faker->numberBetween($limit*30+2,$limit*31);
        break;
    case 32:
        $corpMasterId = $index + 1 <= $limit*31+$corp ? $limit*31+1 : $faker->numberBetween($limit*31+2,$limit*32);
        break;
    case 33:
        $corpMasterId = $index + 1 <= $limit*32+$corp ? $limit*32+1 : $faker->numberBetween($limit*32+2,$limit*33);
        break;
    case 34:
        $corpMasterId = $index + 1 <= $limit*33+$corp ? $limit*33+1 : $faker->numberBetween($limit*33+2,$limit*34);
        break;
    case 35:
        $corpMasterId = $index + 1 <= $limit*34+$corp ? $limit*34+1 : $faker->numberBetween($limit*34+2,$limit*35);
        break;
    default:
        $corpMasterId = null;
}
return [
    'id' => $index +1,
    'tenant_id' => $tenantId,
    'client_no' => $no,
    'corp_master_id' =>$corpMasterId,
    'client_name' => $faker->company,
    'client_name_kana' => $faker->name,
    'tel_no' => $faker->phoneNumber,
    'address' => $faker->address,
    'tanto_name' => $faker->name,
    'created_at' => $faker->unixTime,
    'valid_chk' =>  1,
    'client_business_outline' => $faker->realText(),
    'client_corporate_url' => $faker->url,
    'admin_memo' => $faker->realText(),
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
];