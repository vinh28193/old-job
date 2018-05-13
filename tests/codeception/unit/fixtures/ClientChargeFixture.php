<?php

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class ClientChargeFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 50;
    public $modelClass = 'app\models\manage\ClientCharge';
    public $depends = [];
}