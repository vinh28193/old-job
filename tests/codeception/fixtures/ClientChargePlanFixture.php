<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class ClientChargePlanFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\ClientChargePlan';
    public $depends = [];

}