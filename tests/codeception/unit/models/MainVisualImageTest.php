<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2018/01/04
 * Time: 19:51
 */

namespace test\models;

use app\common\Helper\JmUtils;
use app\models\MainVisualImage;
use tests\codeception\unit\JmTestCase;

/**
 * Class MainVisualImageTest
 * @package test\models
 * todo 他のメソッドのテスト
 */
class MainVisualImageTest extends JmTestCase
{
    /**
     * getImageUrlのtest
     */
    public function testGetImageUrl()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36';
        $model = new MainVisualImage();
        $model->file_name = 'fileName';
        $model->file_name_sp = 'fileNameSp';
        verify($model->imageUrl)->equals(JmUtils::fileUrl('data/content/fileName') . '?public=1');

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Mobile Safari/537.36';
        $model = new MainVisualImage();
        $model->file_name = 'fileName';
        $model->file_name_sp = 'fileNameSp';
        verify($model->imageUrl)->equals(JmUtils::fileUrl('data/content/fileNameSp') . '?public=1');
    }
}
