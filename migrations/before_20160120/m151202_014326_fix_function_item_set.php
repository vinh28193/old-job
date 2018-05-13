<?php

use yii\db\Schema;
use yii\db\Migration;

class m151202_014326_fix_function_item_set extends Migration
{
    public function safeUp()
    {
        $this->update('function_item_set', ['item_column' => 'regist_datetime'], ['manage_menu_id' => 14, 'item_default_name' => '応募日']);
        $this->update('function_item_set', ['item_data_type' => 'プルダウン'], ['manage_menu_id' => 7, 'item_default_name' => '代理店名']);
    }

    public function safeDown()
    {
        $this->update('function_item_set', ['item_column' => 'register_datetime'], ['manage_menu_id' => 14, 'item_default_name' => '応募日']);
        $this->update('function_item_set', ['item_data_type' => 'テキスト'], ['manage_menu_id' => 7, 'item_default_name' => '代理店名']);
    }
}
