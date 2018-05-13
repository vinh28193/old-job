<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class JobTypeFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 200;
    public $modelClass = 'app\models\manage\searchkey\JobType';
    public $depends = [];
}