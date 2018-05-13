<?php

use yii\db\Schema;
use yii\db\Migration;

class m160201_105050_create_job_option_column_sub_set extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('job_option_column_sub_set', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'column_name' => 'VARCHAR(30) NOT NULL COMMENT "job_masterのカラム"',
            'name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "オプション項目名"',
        ], $tableOptions . ' COMMENT="求人情報オプションサブ項目管理"');
        $this->createIndex('idx_job_option_column_sub_set', 'job_option_column_sub_set', 'id');
    }

    public function safeDown()
    {
        $this->dropTable('job_option_column_sub_set');
    }
}
