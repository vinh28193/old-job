<?php

use yii\db\Migration;

class m160301_041131_fix_column_name_in_list_disp extends Migration
{
    public function safeUp()
    {
        $this->update('list_disp', ['column_name' => 'client_master_id'], ['column_name' => 'clientMaster']);
    }

    public function safeDown()
    {
    }
}
