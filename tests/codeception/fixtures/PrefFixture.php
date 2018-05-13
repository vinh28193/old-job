<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class PrefFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 48;
    public $modelClass = 'app\models\manage\searchkey\Pref';
    public $depends = [];
}