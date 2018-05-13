<?php

use yii\db\Migration;

class m151021_044330_remove_partition_in_client_option extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE client_option REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE client_option  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_client_option_tenant_id', 'client_option', 'tenant_id');
        // カラム名修正
        $this->renameColumn('client_option', 'option', 'option_value');
        $this->renameColumn('client_option', 'client_id', 'client_master_id');

    }

    public function down()
    {
        // カラム名修正
        $this->renameColumn('client_option', 'option_value', 'option');
        $this->renameColumn('client_option', 'client_master_id', 'client_id');

        // テーブル構造修正
        $this->dropIndex('idx_client_option_tenant_id', 'client_option');
        $this->execute('ALTER TABLE client_option  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE client_option PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

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
