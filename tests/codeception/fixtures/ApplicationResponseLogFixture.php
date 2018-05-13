<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class ApplicationResponseLogFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\ApplicationResponseLog';
    public $depends = [];

}