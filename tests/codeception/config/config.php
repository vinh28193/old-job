<?php
/**
 * Application configuration shared by all test types
 */
return [
    'language' => 'ja',
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=jm2_test',
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => false,
        ],
    ],
];
