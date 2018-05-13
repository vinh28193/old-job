<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class JobStationInfoFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\searchkey\JobStationInfo';
    public $depends = [];
}