<?php

use yii\db\Migration;

class m151027_011537_mod_column_name extends Migration
{
    public function safeUp()
    {
        // カラム名変更(ex.corp_id→corp_master_id)
        $this->renameColumn('admin_master', 'corp_id', 'corp_master_id');
        $this->renameColumn('admin_master', 'client_id', 'client_master_id');
        $this->renameColumn('function_item_subset', 'function_item_id', 'function_item_set_id');
        $this->renameColumn('client_master', 'corp_id', 'corp_master_id');

    }

    public function safeDown()
    {
        // カラム名変更
        $this->renameColumn('admin_master', 'corp_master_id', 'corp_id');
        $this->renameColumn('admin_master', 'client_master_id', 'client_id');
        $this->renameColumn('function_item_subset', 'function_item_set_id', 'function_item_id');
        $this->renameColumn('client_master', 'corp_master_id', 'corp_id');
    }
}
