<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception\unit\fixtures;

use app\models\FreeContentElement;
use tests\codeception\JmFixture;

/**
 * Class FreeContentElementFixture
 * @package tests\codeception\unit\fixtures
 */
class FreeContentElementFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 100;
    /** @var string */
    public $modelClass = FreeContentElement::class;
}
