<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class JobWageFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 100;
    public $modelClass = 'app\models\manage\searchkey\JobWage';
    public $depends = [];
}