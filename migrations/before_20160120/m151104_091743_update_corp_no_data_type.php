<?php

use yii\db\Schema;
use yii\db\Migration;

class m151104_091743_update_corp_no_data_type extends Migration
{
    public function up()
    {
        $this->update("function_item_set", ["item_data_type" => "数字"], "item_column = 'corp_no'");
    }

    public function down()
    {
        $this->update("function_item_set", ["item_data_type" => "テキスト"], "item_column = 'corp_no'");
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
