<?php

use yii\db\Schema;
use yii\db\Migration;

class m151009_075512_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 名前テーブル
        $this->dropTable('name_master');

        $this->createTable('name_master', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'name_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "名前ID"',
            'change_name' => 'VARCHAR(200) NOT NULL COMMENT "変更後名称"',
            'default_name' => 'VARCHAR(200) NOT NULL COMMENT "初期名称"',
        ], $tableOptions. ' COMMENT="名前"');

        $this->addPrimaryKey('pk_name_master', 'name_master', ['id', 'tenant_id']);
        $this->alterColumn('name_master', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_name_master_name_id', 'name_master', 'name_id');
        $this->execute('ALTER TABLE name_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

    }

    public function down()
    {
        // 名前テーブル
        $this->dropTable('name_master');

        $sql = <<<SQL
CREATE TABLE name_master
(
    name_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    change_name VARCHAR(200) NOT NULL,
    default_name VARCHAR(200) NOT NULL
);

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
