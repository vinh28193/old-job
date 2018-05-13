<?php
/**
 * Application configuration for acceptance tests
 */
// 特殊なルーティングをしているためにこのように直で値を代入しています。
Yii::setAlias('@runtime', '/var/www/jm2/runtime');
$_SERVER['HTTP_HOST'] = 'jm2.yii';
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../config/web.php'),
    require(__DIR__ . '/config.php'),
    [
        // acceptanceのみ開発dbにつないでいます
        'components' => [
            'db' => [
                'dsn' => 'mysql:host=localhost;dbname=jm2',
            ],
        ],
        'controllerMap' => [
            'fixture' => [
                'class' => 'yii\faker\FixtureController',
                'fixtureDataPath' => '@tests/codeception/fixtures/data',
                'templatePath' => '@tests/codeception/templates',
                'namespace' => 'tests\codeception\fixtures',
            ],
        ],
    ]
);
