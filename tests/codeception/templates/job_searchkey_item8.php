job_searchkey_item1.phpjob_searchkey_item1.php<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$tenantId = $index + 1;
$limit = 50000;
$connecting = 7;
$searchkeyItem = 300;
$jobMasterId = floor($tenantId / $connecting + 1);
$searchkeyItemId = $tenantId % $searchkeyItem ;
if($searchkeyItemId == 0){
    $searchkeyItemId = $searchkeyItem;
    $jobMasterId = $jobMasterId - 1;
}
if($tenantId <= $limit){
    $tenantId = 1;
}elseif ($tenantId > $limit && $tenantId <= $limit*2 ){
    $tenantId = 2;
}elseif ($tenantId > $limit*2 && $tenantId <= $limit*3 ){
    $tenantId = 3;
}elseif ($tenantId > $limit*3 && $tenantId <= $limit*4 ){
    $tenantId = 4;
}elseif ($tenantId > $limit*4 && $tenantId <= $limit*5 ) {
    $tenantId = 5;
}elseif ($tenantId > $limit*5 && $tenantId <= $limit*6 ) {
    $tenantId = 6;
}elseif ($tenantId > $limit*6 && $tenantId <= $limit*7 ) {
    $tenantId = 7;
}elseif ($tenantId > $limit*7 && $tenantId <= $limit*8 ) {
    $tenantId = 8;
}elseif ($tenantId > $limit*8 && $tenantId <= $limit*9 ) {
    $tenantId = 9;
}elseif ($tenantId > $limit*9 && $tenantId <= $limit*10 ) {
    $tenantId = 10;
}elseif ($tenantId > $limit*10 && $tenantId <= $limit*11 ) {
    $tenantId = 11;
}elseif ($tenantId > $limit*11 && $tenantId <= $limit*12 ) {
    $tenantId = 12;
}elseif ($tenantId > $limit*12 && $tenantId <= $limit*13 ) {
    $tenantId = 13;
}elseif ($tenantId > $limit*13 && $tenantId <= $limit*14 ) {
    $tenantId = 14;
}elseif ($tenantId > $limit*14 && $tenantId <= $limit*15 ) {
    $tenantId = 15;
}elseif ($tenantId > $limit*15 && $tenantId <= $limit*16 ) {
    $tenantId = 16;
}elseif ($tenantId > $limit*16 && $tenantId <= $limit*17 ) {
    $tenantId = 17;
}elseif ($tenantId > $limit*17 && $tenantId <= $limit*18 ) {
    $tenantId = 18;
}elseif ($tenantId > $limit*18 && $tenantId <= $limit*19 ) {
    $tenantId = 19;
}elseif ($tenantId > $limit*19 && $tenantId <= $limit*20 ) {
    $tenantId = 20;
}elseif ($tenantId > $limit*20 && $tenantId <= $limit*21 ) {
    $tenantId = 21;
}elseif ($tenantId > $limit*21 && $tenantId <= $limit*22 ) {
    $tenantId = 22;
}elseif ($tenantId > $limit*22 && $tenantId <= $limit*23 ) {
    $tenantId = 23;
}elseif ($tenantId > $limit*23 && $tenantId <= $limit*24 ) {
    $tenantId = 24;
}elseif ($tenantId > $limit*24 && $tenantId <= $limit*25 ) {
    $tenantId = 25;
}elseif ($tenantId > $limit*25 && $tenantId <= $limit*26 ) {
    $tenantId = 26;
}elseif ($tenantId > $limit*26 && $tenantId <= $limit*27 ) {
    $tenantId = 27;
}elseif ($tenantId > $limit*27 && $tenantId <= $limit*28 ) {
    $tenantId = 28;
}elseif ($tenantId > $limit*28 && $tenantId <= $limit*29 ) {
    $tenantId = 29;
}elseif ($tenantId > $limit*29 && $tenantId <= $limit*30 ) {
    $tenantId = 30;
}elseif ($tenantId > $limit*30 && $tenantId <= $limit*31 ) {
    $tenantId = 31;
}elseif ($tenantId > $limit*31 && $tenantId <= $limit*32 ) {
    $tenantId = 32;
}elseif ($tenantId > $limit*32 && $tenantId <= $limit*33 ) {
    $tenantId = 33;
}elseif ($tenantId > $limit*33 && $tenantId <= $limit*34 ) {
    $tenantId = 34;
}elseif ($tenantId > $limit*34 ) {
    $tenantId = 35;
}
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'job_master_id' => $jobMasterId,
    'searchkey_item_id' => $searchkeyItemId,
];
