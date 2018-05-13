<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class JobTypeCategoryFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 2;
    public $modelClass = 'app\models\manage\searchkey\JobTypeCategory';
    public $depends = [];
}