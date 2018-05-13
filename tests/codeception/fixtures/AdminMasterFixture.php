<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class AdminMasterFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\AdminMaster';
    public $depends = [];
}
