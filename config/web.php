<?php

$params = require(__DIR__ . '/params.php');
$dsn = require(__DIR__ . '/db.php');
$db = array_merge([
    'class' => 'proseeds\db\Connection',
], $dsn);
$googleAnalytics = require(__DIR__ . '/googleAnalytics.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'tenant',
    ],
    'language' => 'ja',
    'components' => [
        'formatter' => [
            'class' => 'app\common\ProseedsFormatter',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require(__DIR__ . '/routes.php'),
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'VxlMkrXLD2pIHKlNJ8saJV-OoUUN7v4b',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => '\Swift_SmtpTransport',
                'host' => 'mlg011.pro-seeds.com',
                'port' => '25',
            ],
//            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'tenant' => [
            'class' => 'proseeds\base\Tenant',
            'exclude' => [
                'tables' => [
                    'tenant',
                    'user_session',
                    'manager_session',
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
        'functionItemSet' => [
            'class' => 'app\common\ManageMenuConfigs',
            'menus' => [
                'corp' => ['columnSetModel' => 'app\models\manage\CorpColumnSet'],
                'admin' => ['columnSetModel' => 'app\models\manage\AdminColumnSet'],
                'client' => ['columnSetModel' => 'app\models\manage\ClientColumnSet'],
                'application' => ['columnSetModel' => 'app\models\manage\ApplicationColumnSet'],
                'member' => ['columnSetModel' => 'app\models\manage\MemberColumnSet'],
                'job' => ['columnSetModel' => 'app\models\manage\JobColumnSet'],
                'inquiry' => ['columnSetModel' => 'app\models\manage\InquiryColumnSet'],
            ],
        ],
        'searchKey' => [
            'class' => 'app\common\SearchKey',
        ],
        'site' => [
            'class' => 'app\common\Site',
        ],
        'area' => [
            'class' => 'app\components\Area',
        ],
        'nameMaster' => [
            'class' => 'app\components\NameMaster',
        ],
        'googleAnalytics' => $googleAnalytics,
        'session' => [
            'class' => 'yii\web\DbSession',
            'sessionTable' => 'user_session',
            'name' => 'JMSSID',
            'timeout' => 2592000,   // 30日間
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
//                    'class' => 'yii\i18n\PhpMessageSource',
                    'class' => 'app\components\ProseedsMessageSource',
                    'basePath' => '@app/messages', // if advanced application, set @frontend/messages
                    'sourceLanguage' => 'ja',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
                // Yii デフォルトの翻訳を残す
                'yii' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages', // if advanced application, set @frontend/messages
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'yii' => 'yii.php',
                    ],
                ],
            ],
        ],
    ],
    'modules' => [
        'manage' => [
            'class' => 'app\modules\manage\Module',
        ],
    ],
    'params' => $params,
    'defaultRoute' => 'top',
];

$flySystem = require(__DIR__ . '/env-dependent/fileSystem.php');
$config['components'] = array_merge($config['components'], $flySystem);


if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
//        'allowedIPs' => ['122.221.168.194', '122.220.243.98', '127.0.0.1', '::1', '192.168.33.*'],
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => [ '192.168.33.*', '127.0.0.1', '::1'],
    ];

    Yii::setAlias('@yii/debug', dirname(__DIR__) . '/vendor/yiisoft/yii2-debug');
}


\Yii::$container->set('yii\widgets\Pjax', ['timeout' => 0, 'clientOptions' => ['cache' => false]]);
\Yii::$container->set('app\common\PostablePjax', ['timeout' => 0, 'clientOptions' => ['cache' => false]]);
\Yii::$container->set('proseeds\widgets\grid\ProseedsGridView', ['renderLimit' => false]);
\Yii::$container->set('app\common\widget\FormattedDatePicker', [
    'type' => kartik\widgets\DatePicker::TYPE_INPUT,
    'options' => ['class' => 'form-control mgr10'],
    'convertFormat' => true,
    'pluginOptions' => [
        'autoclose' => true,
        'startDate' => '1920/1/1',
        'endDate' => '2037/12/31',
    ],
]);

return $config;
