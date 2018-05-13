<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class ManageMenuMainFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 80;
    public $modelClass = 'app\models\manage\ManageMenuMain';
    public $depends = [];
}