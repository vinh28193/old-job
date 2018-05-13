<?php
/**
 * Application configuration for unit tests
 */
// 特殊なルーティングをしているためにこのように直で値を代入しています。
$runtime = str_replace('tests', 'runtime', Yii::getAlias('@tests'));
Yii::setAlias('@runtime', $runtime);
$_SERVER['HTTP_HOST'] = 'jm2.yii';
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../config/web.php'),
    require(__DIR__ . '/config.php'),
    [
        // 現状test_dbにつないでいます
        'components' => [
            'db' => [
                'dsn' => 'mysql:host=localhost;dbname=jm2_test',
            ]
        ],
        'controllerMap' => [
            'fixture' => [
                'class' => 'yii\faker\FixtureController',
                'fixtureDataPath' => '@tests/codeception/unit/fixtures/data',
                'templatePath' => '@tests/codeception/unit/templates',
                'namespace' => 'tests\codeception\unit\fixtures',
            ],
        ],
    ]
);
