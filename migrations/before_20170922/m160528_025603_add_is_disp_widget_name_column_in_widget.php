<?php

use yii\db\Migration;

class m160528_025603_add_is_disp_widget_name_column_in_widget extends Migration
{
    public function safeUp()
    {
        $this->addColumn('widget', 'is_disp_widget_name', $this->boolean()->notNull()->defaultValue(0).' COMMENT \'ウィジェット名の表示チェック(0=非表示,1=表示)\'');
    }

    public function safeDown()
    {
        $this->dropColumn('widget', 'is_disp_widget_name');
    }
}
