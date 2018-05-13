<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class ApplicationResponseLogFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 2;
    public $modelClass = 'app\models\manage\ApplicationResponseLog';
    public $depends = [];

}