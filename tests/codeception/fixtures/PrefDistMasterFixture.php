<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class PrefDistMasterFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\searchkey\PrefDistMaster';
    public $depends = [];
}