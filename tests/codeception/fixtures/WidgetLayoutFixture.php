<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class WidgetLayoutFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\WidgetLayout';
    public $depends = [
        'tests\codeception\fixtures\WidgetFixture',
    ];
}