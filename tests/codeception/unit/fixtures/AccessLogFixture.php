<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class AccessLogFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 150;
    public $modelClass = 'app\models\manage\AccessLog';
    public $depends = [];
}