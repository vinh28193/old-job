<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class PolicyFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\Policy';
    public $depends = [];
}