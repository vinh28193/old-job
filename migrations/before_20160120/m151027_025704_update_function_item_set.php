<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_025704_update_function_item_set extends Migration
{
    public function up()
    {
        $this->update('function_item_set', ['item_column' => 'corp_no', 'item_data_type' => '数値'], 'function_item_id=9');
    }

    public function down()
    {
        $this->update('function_item_set', ['item_column' => 'corp_id', 'item_data_type' => 'テキスト'], 'function_item_id=9');
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
