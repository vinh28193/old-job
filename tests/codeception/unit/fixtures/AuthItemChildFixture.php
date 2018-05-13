<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class AuthItemChildFixture extends JmFixture
{
    public $tableName = 'auth_item_child';
    public $depends = [];
}