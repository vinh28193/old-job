<?php

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class ClientChargePlanFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 6;
    public $modelClass = 'app\models\manage\ClientChargePlan';
    public $depends = [];

}