<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$tenantId = $index + 1;
$limit = 1000;
$no = $index + 1;
$managerAuthority = 1000;
$corpAuthority = 1000;
$clientAuthority = 8000;
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
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index +1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 2:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 3:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 4:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 5:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 6:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 7:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 8:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 9:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 10:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 11:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 12:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 13:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 14:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 15:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 16:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 17:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 18:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 19:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 20:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 21:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 22:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 23:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 24:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 25:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 26:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 27:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 28:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 29:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 30:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 31:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 32:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 33:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 34:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    case 35:
        if($index + 1 <= $limit * ($tenantId - 1) + $managerAuthority){
            $corpMasterId = null;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority && $index + 1 <= $limit * ($tenantId - 1) + $managerAuthority + $corpAuthority ){
            $corpMasterId = $index + 1;
            $clientMasterId = null;
        }elseif ($index + 1 > $managerAuthority + $corpAuthority && $index + 1 <= $limit * ($tenantId - 1 ) + $managerAuthority + $corpAuthority + $clientAuthority){
            $corpMasterId = $index + 1;
            $clientMasterId = $index + 1;
        }
        break;
    default:
        $jobMasterId = null;
}
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'admin_no' => $no,
    'corp_master_id' => $corpMasterId,
    'login_id' => $faker->realText(255),
    'password' => $faker->realText(255),
    'created_at' => $faker->unixTime,
    'valid_chk' => $faker->boolean(1),
    'name_sei' => $faker->realText(255),
    'name_mei' => $faker->realText(255),
    'tel_no' => $faker->realText(30),
    'client_master_id' => $clientMasterId,
    'mail_address' => $faker->realText(30),
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