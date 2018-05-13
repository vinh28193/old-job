<?php

namespace models\manage;

use tests\codeception\unit\JmTestCase;
use tests\codeception\unit\fixtures\WidgetFixture;
use app\models\WidgetDisp;

/**
 * @group widgets
 */
class WidgetDispTest extends JmTestCase
{
    /**
     * フィクスチャ設定
     * @return array
     */
    public function fixtures()
    {
        return [
            'widget' => WidgetFixture::className(),
        ];
    }

    /**
     * フィクスチャロード
     */
    public function setUp()
    {
        parent::setUp();
    }
    
}
