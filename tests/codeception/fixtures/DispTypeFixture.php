<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class DispTypeFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 3;
    public $modelClass = 'app\models\manage\DispType';
    public $depends = [];
}