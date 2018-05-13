<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class NameMasterFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 4;
    public $modelClass = 'app\models\manage\NameMaster';
    public $depends = [];
}