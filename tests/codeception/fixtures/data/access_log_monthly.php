<?php

$yesterday = date('Y-m-d', strtotime('-1 day'));
$twoDaysAgo = date('Y-m-d', strtotime('-2 day'));

return [
    [
        // AccessLogMonthlyのテストで使っています
        // 編集しないでください
        'id' => 1,
        'tenant_id' => 1,
        'access_date' => $twoDaysAgo,
        'detail_count_pc' => 40,
        'application_count_pc' => 21,
        'member_count_pc' => 46,
        'detail_count_smart' => 44,
        'application_count_smart' => 39,
        'member_count_smart' => 25,
    ],
    [
        'id' => 2,
        'tenant_id' => 2,
        'access_date' => $twoDaysAgo,
        'detail_count_pc' => 88,
        'application_count_pc' => 81,
        'member_count_pc' => 33,
        'detail_count_smart' => 88,
        'application_count_smart' => 99,
        'member_count_smart' => 88,
    ],
    [
        // AccessLogMonthlyのtestFindYesterdayRecordで使っています
        // 編集しないでください
        'id' => 3,
        'tenant_id' => 1,
        'access_date' => $yesterday,
        'detail_count_pc' => 54,
        'application_count_pc' => 69,
        'member_count_pc' => 14,
        'detail_count_smart' => 96,
        'application_count_smart' => 37,
        'member_count_smart' => 93,
    ],
    [
        'id' => 4,
        'tenant_id' => 2,
        'access_date' => $yesterday,
        'detail_count_pc' => 37,
        'application_count_pc' => 66,
        'member_count_pc' => 36,
        'detail_count_smart' => 34,
        'application_count_smart' => 86,
        'member_count_smart' => 15,
    ],
];
