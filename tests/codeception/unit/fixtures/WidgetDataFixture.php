<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class WidgetDataFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 30;
    public $modelClass = 'app\models\manage\WidgetData';
}