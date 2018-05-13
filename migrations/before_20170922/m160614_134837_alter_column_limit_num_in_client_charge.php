<?php

use yii\db\Migration;

class m160614_134837_alter_column_limit_num_in_client_charge extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('client_charge', 'limit_num', 'TINYINT UNSIGNED COMMENT"枠数"');
    }

    public function safeDown()
    {
        $this->alterColumn('client_charge', 'limit_num', 'TINYINT COMMENT"枠数"');
    }
}
