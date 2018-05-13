<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class WidgetFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 10;
    public $modelClass = 'app\models\manage\Widget';
}