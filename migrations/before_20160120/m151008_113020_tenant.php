<?php

use yii\db\Schema;
use yii\db\Migration;

class m151008_113020_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 代理店テーブル
        $this->dropTable('corp_master');

        $this->createTable('corp_master', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'corp_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "代理店ID"',
            'corp_name' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "代理店名"',
            'regist_datetime' => Schema::TYPE_TIMESTAMP . ' NOT NULL COMMENT "登録日時"',
            'tel_no' => 'VARCHAR(30) COMMENT "電話番号"',
            'tanto_name' => Schema::TYPE_TEXT . ' COMMENT "担当者名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 NOT NULL COMMENT "取引状態"',
        ], $tableOptions. ' COMMENT="代理店"');

        $this->addPrimaryKey('pk_corp_master', 'corp_master', ['id', 'tenant_id']);
        $this->alterColumn('corp_master', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_corp_master_corp_id', 'corp_master', 'corp_id');
        $this->execute('ALTER TABLE corp_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 代理店オプションテーブル
        $this->createTable('corp_option', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'corp_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "代理店ID"',
            'function_item_id' => Schema::TYPE_INTEGER . '  COMMENT "項目管理ID"',
            'option' => Schema::TYPE_TEXT . '  COMMENT "オプション項目内容"',
        ], $tableOptions. ' COMMENT="代理店オプション"');

        $this->addPrimaryKey('pk_corp_option', 'corp_option', ['id', 'tenant_id']);
        $this->alterColumn('corp_option', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_corp_option_corp_id', 'corp_option', 'corp_id');
        $this->execute('ALTER TABLE corp_option PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

    }

    public function down()
    {
        // 代理店テーブル
        $this->dropTable('corp_master');

        $sql = <<<SQL
CREATE TABLE corp_master
(
    corp_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    corp_name LONGTEXT NOT NULL,
    regist_datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    tel_no LONGTEXT NOT NULL,
    tanto_name LONGTEXT NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    option100 LONGTEXT,
    option101 LONGTEXT,
    option102 LONGTEXT,
    option103 LONGTEXT,
    option104 LONGTEXT,
    option105 LONGTEXT,
    option106 LONGTEXT,
    option107 LONGTEXT,
    option108 LONGTEXT,
    option109 LONGTEXT
);
CREATE UNIQUE INDEX corp_master_PKI ON corp_master (corp_id);

SQL;
        $this->execute($sql);

        // 代理店オプションテーブル
        $this->dropTable('corp_option');

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
