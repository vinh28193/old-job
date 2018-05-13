<?php

use yii\db\Migration;

class m151020_041339_remove_partition_in_corp_option extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE corp_option REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE corp_option  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_corp_option_tenant_id', 'corp_option', 'tenant_id');
        // カラム名修正
        $this->renameColumn('corp_option', 'option', 'option_value');
        $this->renameColumn('corp_option', 'corp_id', 'corp_master_id');
    }

    public function down()
    {
        // カラム名修正
        $this->renameColumn('corp_option', 'option_value', 'option');
        $this->renameColumn('corp_option', 'corp_master_id', 'corp_id');

        // テーブル構造修正
        $this->dropIndex('idx_corp_option_tenant_id', 'corp_option');
        $this->execute('ALTER TABLE corp_option  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE corp_option PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

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
