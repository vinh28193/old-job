<?php

namespace models\manage;

use app\models\manage\WidgetDataArea;
use tests\codeception\unit\fixtures\WidgetDataAreaFixture;
use tests\codeception\unit\JmTestCase;
use tests\codeception\unit\fixtures\WidgetLayoutFixture;

/**
 * @group widgets
 * @property WidgetLayoutFixture $widget_layout
 */
class WidgetDataAreaTest extends JmTestCase
{
    /**
     * テーブル名テスト
     */
    public function testTableName()
    {
        $model = new WidgetDataArea();
        verify($model->tableName())->equals('widget_data_area');
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new WidgetDataArea();
        verify(count($model->attributeLabels()))->notEmpty();
    }
}
