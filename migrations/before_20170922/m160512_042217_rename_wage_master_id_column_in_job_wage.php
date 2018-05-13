<?php

use yii\db\Migration;

class m160512_042217_rename_wage_master_id_column_in_job_wage extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('job_wage', 'wage_master_id', 'wage_item_id');
    }

    public function safeDown()
    {
        $this->renameColumn('job_wage', 'wage_item_id', 'wage_master_id');
    }
}
