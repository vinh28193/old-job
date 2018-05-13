<?php

use yii\db\Migration;

class m151203_004139_update_valid_chk_in_function_item_set extends Migration
{
    public function safeUp()
    {
        $this->update('function_item_set', ['is_must_item' => 0], ['manage_menu_id' => 1, 'item_default_name' => '代理店名']);
        $this->update('function_item_set', ['is_must_item' => 0], ['manage_menu_id' => 1, 'item_default_name' => '掲載企業名']);
    }

    public function safeDown()
    {
        $this->update('function_item_set', ['is_must_item' => 1], ['manage_menu_id' => 1, 'item_default_name' => '代理店名']);
        $this->update('function_item_set', ['is_must_item' => 1], ['manage_menu_id' => 1, 'item_default_name' => '掲載企業名']);
    }
}
