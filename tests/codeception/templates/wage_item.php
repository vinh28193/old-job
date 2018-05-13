<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$tenantId = $index + 1;
$limit = 300;
$no = $index + 1;
$connecting = 10;
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
if ($no <= $connecting ){
    $wageCategoryId = 1;
}elseif ($no > $connecting && $no <= $connecting*2 ) {
    $wageCategoryId = 2;
}elseif ($no > $connecting*2 && $no <= $connecting*3 ) {
    $wageCategoryId = 3;
}elseif ($no > $connecting*3 && $no <= $connecting*4 ) {
    $wageCategoryId = 4;
}elseif ($no > $connecting*4 && $no <= $connecting*5 ) {
    $wageCategoryId = 5;
}elseif ($no > $connecting*5 && $no <= $connecting*6 ) {
    $wageCategoryId = 6;
}elseif ($no > $connecting*6 && $no <= $connecting*7 ) {
    $wageCategoryId = 7;
}elseif ($no > $connecting*7 && $no <= $connecting*8 ) {
    $wageCategoryId = 8;
}elseif ($no > $connecting*8 && $no <= $connecting*9 ) {
    $wageCategoryId = 9;
}elseif ($no > $connecting*9 && $no <= $connecting*10 ) {
    $wageCategoryId = 10;
}elseif ($no > $connecting*10 && $no <= $connecting*11 ) {
    $wageCategoryId = 11;
}elseif ($no > $connecting*11 && $no <= $connecting*12 ) {
    $wageCategoryId = 12;
}elseif ($no > $connecting*12 && $no <= $connecting*13 ) {
    $wageCategoryId = 13;
}elseif ($no > $connecting*13 && $no <= $connecting*14 ) {
    $wageCategoryId = 14;
}elseif ($no > $connecting*14 && $no <= $connecting*15 ) {
    $wageCategoryId = 15;
}elseif ($no > $connecting*15 && $no <= $connecting*16 ) {
    $wageCategoryId = 16;
}elseif ($no > $connecting*16 && $no <= $connecting*17 ) {
    $wageCategoryId = 17;
}elseif ($no > $connecting*17 && $no <= $connecting*18 ) {
    $wageCategoryId = 18;
}elseif ($no > $connecting*18 && $no <= $connecting*19 ) {
    $wageCategoryId = 19;
}elseif ($no > $connecting*19 && $no <= $connecting*20 ) {
    $wageCategoryId = 20;
}elseif ($no > $connecting*20 && $no <= $connecting*21 ) {
    $wageCategoryId = 21;
}elseif ($no > $connecting*21 && $no <= $connecting*22 ) {
    $wageCategoryId = 22;
}elseif ($no > $connecting*22 && $no <= $connecting*23 ) {
    $wageCategoryId = 23;
}elseif ($no > $connecting*23 && $no <= $connecting*24 ) {
    $wageCategoryId = 24;
}elseif ($no > $connecting*24 && $no <= $connecting*25 ) {
    $wageCategoryId = 25;
}elseif ($no > $connecting*25 && $no <= $connecting*26 ) {
    $wageCategoryId = 26;
}elseif ($no > $connecting*26 && $no <= $connecting*27 ) {
    $wageCategoryId = 27;
}elseif ($no > $connecting*27 && $no <= $connecting*28 ) {
    $wageCategoryId = 28;
}elseif ($no > $connecting*28 && $no <= $connecting*29 ) {
    $wageCategoryId = 29;
}elseif ($no > $connecting*29 && $no <= $connecting*30 ) {
    $wageCategoryId = 30;
}
switch ($tenantId) {
    case 1:
        $wageCategoryId = $wageCategoryId;
        break;
    case 2:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 1;
        break;
    case 3:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 2;
        break;
    case 4:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 3;
        break;
    case 5:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 4;
        break;
    case 6:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 5;
        break;
    case 7:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 6;
        break;
    case 8:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 7;
        break;
    case 9:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 8;
        break;
    case 10:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 9;
        break;
    case 11:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 10;
        break;
    case 12:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 11;
        break;
    case 13:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 12;
        break;
    case 14:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 13;
        break;
    case 15:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 14;
        break;
    case 16:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 15;
        break;
    case 17:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 16;
        break;
    case 18:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 17;
        break;
    case 19:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 18;
        break;
    case 20:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 19;
        break;
    case 21:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 20;
        break;
    case 22:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 21;
        break;
    case 23:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 22;
        break;
    case 24:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 23;
        break;
    case 25:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 24;
        break;
    case 26:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 25;
        break;
    case 27:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 26;
        break;
    case 28:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 27;
        break;
    case 29:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 28;
        break;
    case 30:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 29;
        break;
    case 31:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 30;
        break;
    case 32:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 31;
        break;
    case 33:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 32;
        break;
    case 34:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 33;
        break;
    case 35:
        $wageCategoryId = $wageCategoryId + ($limit / $connecting) * 34;
        break;
    default:
        $corpMasterId = null;
}
return [
    'id' => $index +1,
    'tenant_id' => $tenantId,
    'wage_category_id' => $wageCategoryId,
    'wage_item_no' => $no,
    'wage_item_name' => $faker->numberBetween(1000000000,2147483647),
    'sort' => $no,
    'valid_chk' => $faker->boolean(1),
    'disp_price' => $faker->realText(20),
];
