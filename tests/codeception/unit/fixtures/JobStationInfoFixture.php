<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class JobStationInfoFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 80;
    public $modelClass = 'app\models\manage\searchkey\JobStationInfo';
    public $depends = [];
}