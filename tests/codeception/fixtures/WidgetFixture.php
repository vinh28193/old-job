<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class WidgetFixture extends JmFixture
{

    public $modelClass = 'app\models\manage\Widget';
    public $depends = [
        'tests\codeception\fixtures\WidgetDataFixture',
    ];

}
