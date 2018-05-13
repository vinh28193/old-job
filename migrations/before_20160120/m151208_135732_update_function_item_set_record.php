<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m151208_135732_update_function_item_set_record
 * 【migration更新】function_item_set
 * item_defaut_nameにIDが含まれており、item_data_typeが数字のレコード(管理者ID/代理店IDなど)はitem_maxlength=0に変更
 * function_item_id=104のitem_data_type='メールアドレス'に変更
 */
class m151208_135732_update_function_item_set_record extends Migration
{
    public function safeUp()
    {
        $this->update('function_item_set', ['item_maxlength' => 0], ['and', ['like', 'item_default_name', 'ID'], ['item_data_type' => '数字']]);
        $this->update('function_item_set', ['item_data_type' => 'メールアドレス'], ['function_item_id' => 104]);
    }

    public function safeDown()
    {
        $this->update('function_item_set', ['item_maxlength' => 200], ['and', ['like', 'item_default_name', 'ID'], ['item_data_type' => '数字']]);
        $this->update('function_item_set', ['item_data_type' => 'テキスト'], ['function_item_id' => 104]);
    }
}
