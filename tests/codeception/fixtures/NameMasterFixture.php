<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class NameMasterFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\NameMaster';
    public $depends = [];
}