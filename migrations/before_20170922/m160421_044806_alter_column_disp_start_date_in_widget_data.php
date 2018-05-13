<?php

use yii\db\Migration;

class m160421_044806_alter_column_disp_start_date_in_widget_data extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('widget_data','disp_start_date',  $this->integer(11) . ' COMMENT "掲載開始日"');

    }

    public function safeDown()
    {
        $this->alterColumn('widget_data', 'disp_start_date', $this->integer(11)->notNull(). ' COMMENT 掲載開始日"');
    }
}
