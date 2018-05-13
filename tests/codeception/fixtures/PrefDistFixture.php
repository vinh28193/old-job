<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class PrefDistFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\searchkey\PrefDist';
    public $depends = [];
}