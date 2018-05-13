<?php

use yii\db\Migration;

class m151021_044223_remove_partition_in_function_item_subset extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE function_item_subset REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE function_item_subset  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_function_item_subset_tenant_id', 'function_item_subset', 'tenant_id');
    }

    public function down()
    {
        // テーブル構造修正
        $this->dropIndex('idx_function_item_subset_tenant_id', 'function_item_subset');
        $this->execute('ALTER TABLE function_item_subset  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE function_item_subset PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
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
