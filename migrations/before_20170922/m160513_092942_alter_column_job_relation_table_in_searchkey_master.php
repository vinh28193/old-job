<?php

use yii\db\Migration;

class m160513_092942_alter_column_job_relation_table_in_searchkey_master extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('searchkey_master', 'job_relation_table', $this->string(30) . ' COMMENT "job_masterとの中間テーブル"');
    }

    public function safeDown()
    {
        $this->alterColumn('searchkey_master', 'job_relation_table', $this->string(30)->notNull() . ' COMMENT "job_masterとの中間テーブル"');
    }
}
