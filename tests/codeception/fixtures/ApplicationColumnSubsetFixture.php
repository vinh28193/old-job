<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class ApplicationColumnSubsetFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\ApplicationColumnSubset';
    public $depends = [];

}