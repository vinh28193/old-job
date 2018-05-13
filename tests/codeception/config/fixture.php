<?php
/**
 *  php codeception/bin/yii fixture/generate 【テーブル名】 --count=【生成レコード数】
 * でfixtureのdataを生成する際に使われる設定ファイル
 * コメントアウトでacceptanceとunitを使い分けてください
 */
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/config.php'),
    [
        'controllerMap' => [
            // acceptance
           'fixture' => [
               'class' => 'yii\faker\FixtureController',
               'fixtureDataPath' => '@tests/codeception/fixtures/data',
               'templatePath' => '@tests/codeception/templates',
               'namespace' => 'tests\codeception\fixtures',
           ],
            // unit
//            'fixture' => [
//                'class' => 'yii\faker\FixtureController',
//                'fixtureDataPath' => '@tests/codeception/unit/fixtures/data',
//                'templatePath' => '@tests/codeception/unit/templates',
//                'namespace' => 'tests\codeception\unit\fixtures',
//            ],
        ],
    ]
);
