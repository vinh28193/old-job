<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/05/17
 * Time: 14:11
 */

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class WageCategoryFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\searchkey\WageCategory';
    public $depends = [];
}