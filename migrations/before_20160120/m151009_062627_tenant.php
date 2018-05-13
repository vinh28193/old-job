<?php

use yii\db\Schema;
use yii\db\Migration;

class m151009_062627_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 掲載企業申込みプランテーブル
        $this->dropTable('client_charge');

        $this->createTable('client_charge', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'client_charge_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "掲載企業申込みプランID"',
            'client_charge_plan_id' => 'TINYINT NOT NULL COMMENT "申込みプランID"',
            'client_id' => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "掲載企業ID"',
            'limit_num' => 'TINYINT COMMENT "枠数"',
            'disp_end_date' => Schema::TYPE_DATE . ' COMMENT "掲載終了日"',
        ], $tableOptions. ' COMMENT="掲載企業申込みプラン"');

        $this->addPrimaryKey('pk_client_charge', 'client_charge', ['id', 'tenant_id']);
        $this->alterColumn('client_charge', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_client_charge_client_charge_id', 'client_charge', 'client_charge_id');
        $this->execute('ALTER TABLE client_charge PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

    }

    public function down()
    {
        // 掲載企業申込みプランテーブル
        $this->dropTable('client_charge');

        $sql = <<<SQL
CREATE TABLE client_charge
(
    client_charge_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    client_charge_plan_id TINYINT DEFAULT 0 NOT NULL,
    client_id INT DEFAULT 0 NOT NULL,
    limit_num TINYINT,
    disp_end_date DATE
);
CREATE UNIQUE INDEX client_charge_PKI ON client_charge (client_charge_id);
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
