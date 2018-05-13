<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class HotJobFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\HotJob';
    public $depends = [
        'tests\codeception\fixtures\DispTypeFixture',
    ];
}