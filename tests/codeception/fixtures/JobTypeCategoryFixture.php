<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class JobTypeCategoryFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\searchkey\JobTypeCategory';
    public $depends = [];
}