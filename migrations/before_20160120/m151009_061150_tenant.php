<?php

use yii\db\Schema;
use yii\db\Migration;

class m151009_061150_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 掲載企業スカウトメール上限数テーブル
        $this->dropTable('client_scout_limit');

        $this->createTable('client_scout_limit', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'client_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "掲載企業ID"',
            'last_send_date' => Schema::TYPE_DATE . ' NOT NULL COMMENT "最終送信日"',
            'send_num' => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "送信数"',
            'send_num_limit' => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "送信上限数"',
        ], $tableOptions. ' COMMENT="掲載企業スカウトメール上限数"');

        $this->addPrimaryKey('pk_client_scout_limit', 'client_scout_limit', ['id', 'tenant_id']);
        $this->alterColumn('client_scout_limit', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_client_scout_limit_client_id', 'client_scout_limit', 'client_id');
        $this->execute('ALTER TABLE client_scout_limit PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
    }

    public function down()
    {
        // 掲載企業スカウトメール上限数テーブル
        $this->dropTable('client_scout_limit');

        $sql = <<<SQL
CREATE TABLE client_scout_limit
(
    client_id INT PRIMARY KEY NOT NULL,
    last_send_date DATE NOT NULL,
    send_num INT DEFAULT 0 NOT NULL,
    send_num_limit INT DEFAULT 0 NOT NULL
);
CREATE UNIQUE INDEX client_scout_limit_PKI ON client_scout_limit (client_id);
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
