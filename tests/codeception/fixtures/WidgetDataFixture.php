<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class WidgetDataFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\WidgetData';
    public $depends = [];
}