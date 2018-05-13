<?php

use yii\db\Migration;

class m151021_044420_remove_partition_in_client_charge_plan extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE client_charge_plan REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE client_charge_plan  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_client_charge_plan_tenant_id', 'client_charge_plan', 'tenant_id');
    }

    public function down()
    {
        // テーブル構造修正
        $this->dropIndex('idx_client_charge_plan_tenant_id', 'client_charge_plan');
        $this->execute('ALTER TABLE client_charge_plan  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE client_charge_plan PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
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
