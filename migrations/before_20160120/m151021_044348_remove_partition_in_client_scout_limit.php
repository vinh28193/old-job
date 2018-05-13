<?php

use yii\db\Migration;

class m151021_044348_remove_partition_in_client_scout_limit extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE client_scout_limit REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE client_scout_limit  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_client_scout_limit_tenant_id', 'client_scout_limit', 'tenant_id');
        // カラム名修正
        $this->renameColumn('client_scout_limit', 'client_id', 'client_master_id');

    }

    public function down()
    {
        // カラム名修正
        $this->renameColumn('client_scout_limit', 'client_master_id', 'client_id');
        // テーブル構造修正
        $this->dropIndex('idx_client_scout_limit_tenant_id', 'client_scout_limit');
        $this->execute('ALTER TABLE client_scout_limit  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE client_scout_limit PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
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
