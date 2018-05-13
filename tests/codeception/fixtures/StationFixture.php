<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class StationFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\searchkey\Station';
    public $depends = [];
}
