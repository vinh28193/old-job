<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class CorpMasterFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 3;
    public $modelClass = 'app\models\manage\CorpMaster';
    public $depends = [];

}