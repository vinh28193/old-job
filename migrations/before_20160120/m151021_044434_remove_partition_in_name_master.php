<?php

use yii\db\Migration;

class m151021_044434_remove_partition_in_name_master extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE name_master REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE name_master  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_name_master_tenant_id', 'name_master', 'tenant_id');
    }

    public function down()
    {
        // テーブル構造修正
        $this->dropIndex('idx_name_master_tenant_id', 'name_master');
        $this->execute('ALTER TABLE name_master  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE name_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
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
