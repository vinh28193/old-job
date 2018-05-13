<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class AuthAssignmentFixture extends JmFixture
{
    public $tableName = 'auth_assignment';
    public $depends = ['tests\codeception\fixtures\AuthItemFixture'];
}
