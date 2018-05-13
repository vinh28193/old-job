<?php

use yii\db\Schema;
use yii\db\Migration;

class m151009_073718_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 申込みプランテーブル
        $this->dropTable('client_charge_plan');

        $this->createTable('client_charge_plan', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'client_charge_plan_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "申込みプランID"',
            'client_charge_type' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "課金タイプ"',
            'disp_type_cd' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "掲載タイプ"',
            'plan_name' => Schema::TYPE_TEXT . ' COMMENT "申込みプラン名"',
            'price' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "料金"',
            'oiwai_price_type' => 'SMALLINT DEFAULT 1 NOT NULL COMMENT "お祝い金種別  0:固定 1:範囲"',
            'oiwai_price' => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "お祝い金額  0:固定 1:範囲"',
            'oiwai_price_from' => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "お祝い金From"',
            'oiwai_price_to' => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "お祝い金To"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 NOT NULL COMMENT "公開状況"',
        ], $tableOptions. ' COMMENT="申込みプラン"');

        $this->addPrimaryKey('pk_client_charge_plan', 'client_charge_plan', ['id', 'tenant_id']);
        $this->alterColumn('client_charge_plan', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_client_charge_plan_client_charge_plan_id', 'client_charge_plan', 'client_charge_plan_id');
        $this->execute('ALTER TABLE client_charge_plan PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

    }

    public function down()
    {
        // 申込みプランテーブル
        $this->dropTable('client_charge_plan');

        $sql = <<<SQL
CREATE TABLE client_charge_plan
(
    client_charge_plan_id INT PRIMARY KEY NOT NULL,
    client_charge_type TINYINT DEFAULT 0,
    disp_type_cd SMALLINT DEFAULT 0 NOT NULL,
    plan_name LONGTEXT,
    price INT NOT NULL,
    oiwai_price_type SMALLINT DEFAULT 1 NOT NULL,
    oiwai_price INT DEFAULT 0 NOT NULL,
    oiwai_price_from INT DEFAULT 0 NOT NULL,
    oiwai_price_to INT DEFAULT 0 NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL
);
CREATE UNIQUE INDEX client_charge_plan_PKI ON client_charge_plan (client_charge_plan_id);
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
