<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class SocialButtonFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\SocialButton';
    public $depends = [];
}