<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160310_070259_create_column_subset_tables
 * 各サブセットテーブルを作成
 */
class m160310_070259_create_column_subset_tables extends Migration
{
    const CREATE_INFO = [
        'job' => '求人原稿',
        'admin' => '管理者',
        'application' => '応募者',
        'client' => '掲載企業',
        'corp' => '代理店'
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
    }
}
