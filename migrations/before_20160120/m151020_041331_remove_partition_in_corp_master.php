<?php

use yii\db\Migration;

class m151020_041331_remove_partition_in_corp_master extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE corp_master REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE corp_master  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_corp_master_tenant_id', 'corp_master', 'tenant_id');
        // カラム名修正
        $this->renameColumn('corp_master', 'corp_id', 'corp_no');
    }

    public function down()
    {
        // カラム名修正
        $this->renameColumn('corp_master', 'corp_no', 'corp_id');
        // テーブル構造修正
        $this->dropIndex('idx_corp_master_tenant_id', 'corp_master');
        $this->execute('ALTER TABLE corp_master  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE corp_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
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
