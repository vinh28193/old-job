<?php

use yii\db\Schema;
use yii\db\Migration;

class m151022_142415_change_column_manage_menu_admin_exception extends Migration
{
    public function up()
    {
        // カラム名修正
        $this->renameColumn('manage_menu_admin_exception', 'admin_id', 'admin_master_id');
    }

    public function down()
    {
        // カラム名修正
        $this->renameColumn('manage_menu_admin_exception', 'admin_master_id', 'admin_id');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
