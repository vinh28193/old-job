<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class AuthItemChildFixture extends JmFixture
{
    public $tableName = 'auth_item_child';
    public $depends = ['tests\codeception\fixtures\AuthItemFixture'];
}