<?php

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class ClientMasterFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 15;
    public $modelClass = 'app\models\manage\ClientMaster';
    public $depends = [];

}