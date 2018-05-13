<?php

use yii\db\Migration;

class m151021_044403_remove_partition_in_client_charge extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE client_charge REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE client_charge  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_client_charge_tenant_id', 'client_charge', 'tenant_id');
        // カラム名修正
        $this->renameColumn('client_charge', 'client_id', 'client_master_id');

    }

    public function down()
    {
        // カラム名修正
        $this->renameColumn('client_charge', 'client_master_id', 'client_id');
        // テーブル構造修正
        $this->dropIndex('idx_client_charge_tenant_id', 'client_charge');
        $this->execute('ALTER TABLE client_charge  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE client_charge PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
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
