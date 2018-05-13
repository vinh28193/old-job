<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
//Yii::setAlias('@webroot', dirname(__DIR__) . '/../web');

$params = require(__DIR__ . '/params.php');
$dsn = require(__DIR__ . '/db.php');
$db = array_merge([
    'class' => 'yii\db\Connection',
], $dsn);

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'fixtureDataPath' => '@tests/codeception/fixtures/data',
            'templatePath' => '@tests/codeception/templates',
            'namespace' => 'tests\codeception\fixtures',
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'tenant' => [
            'class' => 'proseeds\base\console\Tenant',
            'exclude' => [
                'tables' => [
                    'tenant',
                    'user_session',
                    'manager_session',
                    'migration',
                    'station',
                    'auth_assignment',
                    'auth_item',
                    'auth_item_child',
                    'auth_rule',
                    'complete_mail_domain',
                    'dist',
                    '{{%auth_assignment}}',
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => '\Swift_SmtpTransport',
                'host' => 'mlg011.pro-seeds.com',
                'port' => '25',
            ],
            //'useFileTransport' => true, // DEBUG
        ],
        'errorMail' => [
            'class' => 'app\common\ErrorMail',
            'fromAddress' => 'pro-jm@pro-seeds.com',
            'toAddress' => 'pro-jm@pro-seeds.com',
        ],
    ],
    'params' => $params,
];

$flySystem = require(__DIR__ . '/env-dependent/fileSystem.php');
$config['components'] = array_merge($config['components'], $flySystem);
return $config;
