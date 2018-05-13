<?php

use yii\db\Migration;

class m160530_054502_alter_column_policy_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('policy', 'policy_page', 'description');
        $this->alterColumn('policy', 'description', $this->string(50)->defaultValue(null). ' COMMENT "ディスクリプション"');
        $this->alterColumn('policy', 'policy_name', $this->string(30)->notNull(). ' COMMENT "規約名"');
    }

    public function safeDown()
    {
        $this->renameColumn('policy', 'description', 'policy_page');
        $this->alterColumn('policy', 'policy_page', $this->string(20)->defaultValue(null). ' COMMENT "表示場所"');
        $this->alterColumn('policy', 'policy_name', $this->string(20)->notNull(). ' COMMENT "規約名"');
    }
}
