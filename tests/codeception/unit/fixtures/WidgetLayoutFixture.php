<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class WidgetLayoutFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 6;
    public $modelClass = 'app\models\manage\WidgetLayout';
}