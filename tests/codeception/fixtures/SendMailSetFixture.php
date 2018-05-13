<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class SendMailSetFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 9;
    public $modelClass = 'app\models\manage\SendMailSet';
    public $depends = [];
}