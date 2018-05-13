<?php

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class SiteMasterFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\SiteMaster';
    public $depends = [];
}