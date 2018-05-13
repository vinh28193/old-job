<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class JobMasterDispFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\JobMaster';
    public $depends = [
        'tests\codeception\unit\fixtures\ClientMasterFixture',
        'tests\codeception\unit\fixtures\JobStationInfoFixture',
    ];

}