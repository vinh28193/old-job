<?php

return [
    [
        'id' => 1,
        'tenant_id' => 2,
        'content' => json_encode([
            'ja' => [
                [
                    'source' => 'テスト',
                    'dist' => 'てすと',
                    'is_active' => true,
                ],
                [
                    'source' => '求人原稿',
                    'dist' => 'バイト原稿',
                    'is_active' => true,
                ],
            ],
            'en' => [
                [
                    'source' => 'テスト',
                    'dist' => 'TEST',
                    'is_active' => true,
                ],
            ],
        ]),
        'created_at' => time(),
        'updated_at' => time(),
    ],
];