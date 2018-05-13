<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class ToolMasterFixture extends ActiveFixture
{
    public $modelClass = 'app\models\ToolMaster';
    public $depends = [];
}
