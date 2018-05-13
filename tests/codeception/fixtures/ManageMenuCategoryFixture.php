<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class ManageMenuCategoryFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 13;
    public $modelClass = 'app\models\manage\ManageMenuCategory';
    public $depends = [];
}