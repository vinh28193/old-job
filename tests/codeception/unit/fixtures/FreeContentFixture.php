<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception\unit\fixtures;

use app\models\FreeContent;
use tests\codeception\JmFixture;

/**
 * Class FreeContentFixture
 * @package tests\codeception\unit\fixtures
 */
class FreeContentFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 10;
    /** @var string */
    public $modelClass = FreeContent::class;
}
