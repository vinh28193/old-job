<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class AuthItemFixture extends JmFixture
{
    public $tableName = 'auth_item';
    public $depends = ['tests\codeception\fixtures\AuthRuleFixture'];
}