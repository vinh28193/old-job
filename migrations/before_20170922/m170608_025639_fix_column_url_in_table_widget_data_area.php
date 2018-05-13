<?php

use yii\db\Migration;

class m170608_025639_fix_column_url_in_table_widget_data_area extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('widget_data_area', 'url', $this->string(2000)->defaultValue(NULL)->comment('URL'));
    }

    public function safeDown()
    {
        $this->alterColumn('widget_data_area', 'url', $this->string(255)->defaultValue(NULL)->comment('URL'));
    }
}
