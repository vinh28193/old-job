<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class JobTypeSmallFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\searchkey\JobTypeSmall';
    public $depends = [];

}