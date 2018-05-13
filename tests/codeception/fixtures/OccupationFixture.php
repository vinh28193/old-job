<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class OccupationFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\Occupation';
    public $depends = [];

}