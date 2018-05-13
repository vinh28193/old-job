<?php

use yii\db\Migration;

class m160524_082602_add_policy_page_column_iin_policy extends Migration
{
    public function safeUp()
    {
        $this->addColumn('policy', 'policy_page', $this->string(20) . ' COMMENT "表示場所" AFTER policy_name');
    }

    public function safeDown()
    {
        $this->dropColumn('policy', 'policy_page');
    }
}
