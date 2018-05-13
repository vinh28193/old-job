<?php

use yii\db\Migration;

/**
 * widgetテーブルに、is_sliderカラムを追加
 * ウィジェット表示パターンにおいてパターン5とパターン7を区別するためのカラム
 */
class m170721_083221_add_column_slider_pattern_into_widget extends Migration
{
    public function safeup()
    {
        $this->addColumn('widget', 'is_slider', $this->boolean()->notNull()->defaultValue(0)->comment('スライド機能ON/OFF'));
    }

    public function safedown()
    {
        $this->dropColumn('widget', 'is_slider');
    }
}
