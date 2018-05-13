<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class JobDistFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 1000;
    public $modelClass = 'app\models\manage\searchkey\JobDist';
    public $depends = [];
}