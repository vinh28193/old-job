<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/05/17
 * Time: 14:10
 */

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class WageItemFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 20;
    public $modelClass = 'app\models\manage\searchkey\WageItem';
    public $depends = [];
}