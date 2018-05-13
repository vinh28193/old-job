<?php

use yii\db\Migration;

class m170124_041141_drop_valid_chk_column_from_pref extends Migration
{
    public function up()
    {
        $this->dropColumn('pref', 'valid_chk');
    }

    public function down()
    {
        $this->addColumn('pref', 'valid_chk', $this->boolean()->defaultValue(1) . ' COMMENT"状態"');
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
