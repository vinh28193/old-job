<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class HotJobPriorityFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\HotJobPriority';
    public $depends = [];
}