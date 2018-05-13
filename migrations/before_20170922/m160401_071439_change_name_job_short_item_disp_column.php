<?php

use yii\db\Migration;

class m160401_071439_change_name_job_short_item_disp_column extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('job_short_item_disp', 'function_item_set_id', 'job_column_set_id');
    }

    public function safeDown()
    {
        $this->renameColumn('job_short_item_disp', 'job_column_set_id', 'function_item_set_id');
    }
}
