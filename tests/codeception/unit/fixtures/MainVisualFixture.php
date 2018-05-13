<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

/**
 * Class AccessLogFixture
 * @package tests\codeception\unit\fixtures
 */
class MainVisualFixture extends JmFixture
{
    /** @var string */
    public $modelClass = 'app\models\manage\MainVisual';
    /** @var array */
    public $depends = [];
}
