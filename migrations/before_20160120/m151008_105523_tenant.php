<?php

use yii\db\Schema;
use yii\db\Migration;

class m151008_105523_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 項目管理サブセットテーブル
        $this->dropTable('function_item_subset');

        $this->createTable('function_item_subset', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'function_item_subset_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "項目管理サブセットID"',
            'function_item_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "項目管理ID"',
            'function_item_subset_name' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "選択肢名"',
        ], $tableOptions. ' COMMENT="項目管理サブセット"');

        $this->addPrimaryKey('pk_function_item_subset', 'function_item_subset', ['id', 'tenant_id']);
        $this->alterColumn('function_item_subset', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_function_item_subset_function_item_subset_id', 'function_item_subset', 'function_item_subset_id');
        $this->execute('ALTER TABLE function_item_subset PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

    }

    public function down()
    {
        // 項目管理サブセットテーブル
        $this->dropTable('function_item_subset');

        $sql = <<<SQL
CREATE TABLE function_item_subset
(
    function_item_subset_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    function_item_id INT NOT NULL,
    function_item_subset_name LONGTEXT NOT NULL
);
CREATE UNIQUE INDEX function_item_subset_PKI ON function_item_subset (function_item_subset_id);
CREATE INDEX function_item_subset_id ON function_item_subset (function_item_subset_id);

SQL;
        $this->execute($sql);
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
