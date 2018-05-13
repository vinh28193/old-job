<?php

use yii\db\Schema;
use yii\db\Migration;

class m151106_070333_update_manage_menu_main extends Migration
{
    public function up()
    {
        $this->update("manage_menu_main", ["valid_chk" => "0"], "manage_menu_main_id = '80'");
        $this->update("manage_menu_main", ["valid_chk" => "0"], "manage_menu_main_id = '81'");
        $this->update("manage_menu_main", ["valid_chk" => "0"], "manage_menu_main_id = '82'");
    }

    public function down()
    {
        $this->update("manage_menu_main", ["valid_chk" => "1"], "manage_menu_main_id = '80'");
        $this->update("manage_menu_main", ["valid_chk" => "1"], "manage_menu_main_id = '81'");
        $this->update("manage_menu_main", ["valid_chk" => "1"], "manage_menu_main_id = '82'");
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
