<?php

use yii\db\Migration;

class m160422_110831_fix_relation_column_in_job_short_item_disp_result extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('job_short_item_disp_result', 'function_item_set_id', $this->string(30)->notNull() . ' COMMENT "job_masterのカラム名"');
        $this->renameColumn('job_short_item_disp_result', 'function_item_set_id', 'column_name');
    }

    public function safeDown()
    {
        $this->renameColumn('job_short_item_disp_result', 'function_item_set_id', 'column_name');
        $this->alterColumn('job_short_item_disp_result', 'function_item_set_id', 'int(11)');
    }
}

