<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class ApplicationStatusFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\ApplicationStatus';
    public $depends = [];

}