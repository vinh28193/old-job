<?php

use yii\db\Migration;

class m151021_044506_remove_partition_in_site_master extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE site_master REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE site_master  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_site_master_tenant_id', 'site_master', 'tenant_id');
    }

    public function down()
    {
        // テーブル構造修正
        $this->dropIndex('idx_site_master_tenant_id', 'site_master');
        $this->execute('ALTER TABLE site_master  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE site_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
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
