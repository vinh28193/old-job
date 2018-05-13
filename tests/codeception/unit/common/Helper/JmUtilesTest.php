<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2018/01/04
 * Time: 15:09
 */
namespace test\common\Helper;

use app\common\Helper\JmUtils;
use tests\codeception\unit\JmTestCase;

/**
 * Class JmUtilesTest
 * @package test\common\Helper
 *
 * todo 無いtest methodsの追加
 */
class JmUtilesTest extends JmTestCase
{
    /**
     * extensionのtest
     */
    public static function testExtension()
    {
        verify(JmUtils::extension('unit/test.ext'))->equals('ext');
    }

    /**
     */
    public static function testFileUrl()
    {
        verify(JmUtils::fileUrl('unit/test.ext'))->equals('/systemdata/unit/test.ext');
    }
}
