<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * 応募関係のitem_columnを修正します
 */
class m160202_021751_fix_function_item_set extends Migration
{    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        //応募IDのカラム名をapplication_noに修正
        $this->update('function_item_set', ['item_column' => 'application_no'], ['item_column' => 'application_id']);
    }

    public function safeDown()
    {
        //応募IDのカラム名をappliction_idに戻す
        $this->update('function_item_set', ['item_column' => 'application_id'], ['item_column' => 'application_no']);
    }
    
}
