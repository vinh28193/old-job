<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation for table `inquiry_column_subset`.
 */
class m161007_065127_create_inquiry_column_subset_table extends Migration
{
    const CREATE_INFO = [
        'inquiry' => '問い合わせ'
    ];

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        foreach (self::CREATE_INFO as $key => $name) {
            $this->createTable($key . '_column_subset', [
                'id' => Schema::TYPE_PK . ' COMMENT "主キーID"',
                'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
                'column_name' => $this->string(30)->notNull() . ' COMMENT "' . $key . '_masterのカラム名"',
                'subset_name' => $this->string()->notNull() . ' COMMENT "選択肢項目名"',
            ], $tableOptions . ' COMMENT="' . $name . 'のオプション項目の選択肢"');
            $this->createIndex('idx_' . $key . '_column_subset_1_tenant_id_2_column_name', $key . '_column_subset', ['tenant_id', 'column_name']);
        }
    }

    public function safeDown()
    {
        foreach (self::CREATE_INFO as $key => $name) {
            $this->dropIndex('idx_' . $key . '_column_subset_1_tenant_id_2_column_name', $key . '_column_subset');
            $this->dropTable($key . '_column_subset');
        }
    }
}
