<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class AdminColumnSetFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 19;
    public $modelClass = 'app\models\manage\AdminColumnSet';
    public $depends = [];

}