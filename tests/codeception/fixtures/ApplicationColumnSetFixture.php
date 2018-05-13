<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class ApplicationColumnSetFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 27;
    public $modelClass = 'app\models\manage\ApplicationColumnSet';
    public $depends = [];

}