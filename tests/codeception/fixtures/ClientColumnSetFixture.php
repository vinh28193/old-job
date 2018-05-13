<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class ClientColumnSetFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\ClientColumnSet';
    public $depends = [];

}