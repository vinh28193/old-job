<?php

use yii\db\Migration;

class m151020_041401_remove_partition_in_function_item_set extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE function_item_set REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE function_item_set  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_function_item_set_tenant_id', 'function_item_set', 'tenant_id');
    }

    public function down()
    {
        // テーブル構造修正
        $this->dropIndex('idx_function_item_set_tenant_id', 'function_item_set');
        $this->execute('ALTER TABLE function_item_set  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE function_item_set PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
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
