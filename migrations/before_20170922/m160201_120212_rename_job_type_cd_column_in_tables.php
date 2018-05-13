<?php

use yii\db\Schema;
use yii\db\Migration;

class m160201_120212_rename_job_type_cd_column_in_tables extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('job_master', 'disp_type_cd', 'disp_type_id');
        $this->renameColumn('client_charge_plan', 'disp_type_cd', 'disp_type_id');
        $this->update('function_item_set', ['item_column' => 'disp_type_id'], ['item_column' => 'disp_type_cd']);
        $this->update('job_column_set', ['column_name' => 'disp_type_id'], ['column_name' => 'disp_type_cd']);
    }

    public function safeDown()
    {
        $this->renameColumn('job_master', 'disp_type_id', 'disp_type_cd');
        $this->renameColumn('client_charge_plan', 'disp_type_id', 'disp_type_cd');
        $this->update('function_item_set', ['item_column' => 'disp_type_cd'], ['item_column' => 'disp_type_id']);
        $this->update('job_column_set', ['column_name' => 'disp_type_cd'], ['column_name' => 'disp_type_id']);
    }
}
