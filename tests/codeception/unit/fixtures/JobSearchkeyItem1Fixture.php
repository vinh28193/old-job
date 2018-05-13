<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class JobSearchkeyItem1Fixture extends JmFixture
{
    const RECORDS_PER_TENANT = 200;
    public $modelClass = 'app\models\manage\searchkey\JobSearchkeyItem1';
    public $depends = [];
}