<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class WidgetDataAreaFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\WidgetDataArea';
    public $depends = [];
}