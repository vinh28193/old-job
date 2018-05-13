<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class JobMasterFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 50;
    public $modelClass = 'app\models\manage\JobMaster';
    public $depends = [];
}