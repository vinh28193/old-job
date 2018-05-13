<?php
namespace tests\models;

use app\common\Helper\JmUtils;
use app\models\FreeContentElement;
use app\models\queries\FreeContentElementQuery;
use tests\codeception\unit\JmTestCase;

/**
 * Class FreeContentElementTest
 * @package tests\models
 */
class FreeContentElementTest extends JmTestCase
{
    // tableNameとattributeLabelsはテスト不要

    /**
     * srcUrlのtest
     */
    public function testSrcUrl()
    {
        $model = new FreeContentElement();
        $model->image_file_name = 'fileName';
        verify($model->srcUrl())->equals(JmUtils::fileUrl('free-content/fileName?public=1'));
    }

    /**
     * findのtest
     */
    public function testFind()
    {
        verify(FreeContentElement::find())->isInstanceOf(FreeContentElementQuery::className());
    }
}
