<?php

use yii\db\Migration;

class m160422_082335_change_job_short_item_disp_table_job_column_set_id_column extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('job_short_item_disp', 'job_column_set_id', 'column_name');
        $this->alterColumn('job_short_item_disp', 'column_name', $this->string(30)->notNull() . ' COMMENT "job_masterのカラム名"');
    }

    public function safeDown()
    {
        $this->renameColumn('job_short_item_disp', 'job_column_set_id', 'column_name');
        $this->alterColumn('job_short_item_disp', 'job_column_set_id', 'int(11)');
    }
}
