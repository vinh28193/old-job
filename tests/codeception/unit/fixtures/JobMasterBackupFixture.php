<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class JobMasterBackupFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 200;
    public $modelClass = 'app\models\manage\JobMasterBackup';
    public $depends = [];
}