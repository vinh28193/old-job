<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class HeaderFooterFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 1;
    public $modelClass = 'app\models\manage\HeaderFooterSetting';
    public $depends = [];
}