<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class JobTypeBigFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\searchkey\JobTypeBig';
    public $depends = [];
}