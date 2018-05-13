<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/05/17
 * Time: 14:11
 */

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class WageCategoryFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 4;
    public $modelClass = 'app\models\manage\searchkey\WageCategory';
    public $depends = [];
}