<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class JobColumnSubsetFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\JobColumnSubset';
    public $depends = [];

}