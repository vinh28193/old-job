<?php

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class PrefDistFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 50;
    public $modelClass = 'app\models\manage\searchkey\PrefDist';
    public $depends = [];
}