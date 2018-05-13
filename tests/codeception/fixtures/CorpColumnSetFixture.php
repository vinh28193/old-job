<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class CorpColumnSetFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\CorpColumnSet';
    public $depends = [];

}