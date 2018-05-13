<?php

namespace models\manage;

use tests\codeception\unit\JmTestCase;
use app\models\manage\WidgetLayout;
use tests\codeception\unit\fixtures\WidgetLayoutFixture;

/**
 * @group widgets
 * @property WidgetLayoutFixture $widget_layout
 */
class WidgetLayoutTest extends JmTestCase
{
    /**
     * テーブル名テスト
     */
    public function testTableName()
    {
        $model = new WidgetLayout();
        verify($model->tableName())->equals('widget_layout');
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new WidgetLayout();
        verify(count($model->attributeLabels()))->notEmpty();
    }
}
