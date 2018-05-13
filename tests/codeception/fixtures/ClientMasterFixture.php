<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class ClientMasterFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\ClientMaster';
    public $depends = [];

}