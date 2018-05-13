<?php

use yii\db\Migration;

class m151023_125155_mod_from_corp_id_to_corp_no extends Migration
{
    public function up()
    {
        // function_item_set.item_columnのcorp_idをcorp_noに修正
        $this->update('function_item_set', ['item_column'=>'corp_no'], 'item_column = "corp_id"');
    }

    public function down()
    {
        $this->update('function_item_set', ['item_column'=>'corp_id'], 'item_column = "corp_no"');

    }
}
