<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class SiteHtmlFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 1;
    public $modelClass = 'app\models\manage\SiteHtml';
    public $depends = [];
}