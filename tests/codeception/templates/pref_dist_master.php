<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
//テナントID
$tenantId = $index + 1;
//1テナント毎に生成する最大レコード数
$limit = 2048;
//1からインクリメントし、テナントIDが切り替わると1に戻る
$no = $index + 1;

//テナント毎に行う処理、総テナント数は、35までの想定
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

//都道府県
$prefId = 1;
if($no <= 47){
    $prefId = $no;
}else if($no <= 189 + 47) {
    $prefId = 1;
}
else if($no <= 40 + 236) {
    $prefId = 2;
}
else if($no <= 34 + 276) {
    $prefId = 3;
}
else if($no <= 39 + 310) {
    $prefId = 4;
}
else if($no <= 25 + 349) {
    $prefId = 5;
}
else if($no <= 35 + 374) {
    $prefId = 6;
}
else if($no <= 61 + 409) {
    $prefId = 7;
}
else if($no <= 44 + 470) {
    $prefId = 8;
}
else if($no <= 30 + 514) {
    $prefId = 9;
}
else if($no <= 38 + 544) {
    $prefId = 10;
}
else if($no <= 80 + 582) {
    $prefId = 11;
}
else if($no <= 61 + 662) {
    $prefId = 12;
}
else if($no <= 66 + 723) {
    $prefId = 13;
}
else if($no <= 60 + 789) {
    $prefId = 14;
}
else if($no <= 42 + 849) {
    $prefId = 15;
}
else if($no <= 15 + 891) {
    $prefId = 16;
}
else if($no <= 19 + 906) {
    $prefId = 17;
}
else if($no <= 17 + 925) {
    $prefId = 18;
}
else if($no <= 29 + 942) {
    $prefId = 19;
}
else if($no <= 1 + 971) {
    $prefId = 13;
}
else if($no <= 80 + 972) {
    $prefId = 20;
}
else if($no <= 42 + 1052) {
    $prefId = 21;
}
else if($no <= 50 + 1094) {
    $prefId = 22;
}
else if($no <= 81 + 1144) {
    $prefId = 23;
}
else if($no <= 29 + 1225) {
    $prefId = 24;
}
else if($no <= 26 + 1254) {
    $prefId = 25;
}
else if($no <= 37 + 1280) {
    $prefId = 26;
}
else if($no <= 72 + 1317) {
    $prefId = 27;
}
else if($no <= 50 + 1389) {
    $prefId = 28;
}
else if($no <= 39 + 1439) {
    $prefId = 29;
}
else if($no <= 30 + 1478) {
    $prefId = 30;
}
else if($no <= 19 + 1508) {
    $prefId = 31;
}
else if($no <= 21 + 1527) {
    $prefId = 32;
}
else if($no <= 31 + 1548) {
    $prefId = 33;
}
else if($no <= 30 + 1579) {
    $prefId = 34;
}
else if($no <= 22 + 1609) {
    $prefId = 35;
}
else if($no <= 24 + 1631) {
    $prefId = 36;
}
else if($no <= 17 + 1655) {
    $prefId = 37;
}
else if($no <= 20 + 1672) {
    $prefId = 38;
}
else if($no <= 34 + 1692) {
    $prefId = 39;
}
else if($no <= 75 + 1726) {
    $prefId = 40;
}
else if($no <= 20 + 1801) {
    $prefId = 41;
}
else if($no <= 21 + 1821) {
    $prefId = 42;
}
else if($no <= 48 + 1842) {
    $prefId = 43;
}
else if($no <= 18 + 1890) {
    $prefId = 44;
}
else if($no <= 30 + 1908) {
    $prefId = 45;
}
else if($no <= 48 + 1938) {
    $prefId = 46;
}
else if($no <= 61 + 1986) {
    $prefId = 47;
}
else if($no <= 1 + 2047) {
    $prefId = 48;
}


return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'pref_dist_name' => $faker->city,
    'valid_chk' => $faker->boolean(1),
    'sort' => $no,
    'pref_id' => $prefId,
];