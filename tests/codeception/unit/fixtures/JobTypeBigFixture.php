<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class JobTypeBigFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 10;
    public $modelClass = 'app\models\manage\searchkey\JobTypeBig';
    public $depends = [];
}