<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class AreaFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 8;
    public $modelClass = 'app\models\manage\searchkey\Area';
}