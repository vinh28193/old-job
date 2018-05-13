<?php

use yii\db\Migration;

class m151020_065102_remove_partition_in_manage_menu_main extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE manage_menu_main REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE manage_menu_main  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_manage_menu_main_tenant_id', 'manage_menu_main', 'tenant_id');
    }

    public function down()
    {
        // テーブル構造修正
        $this->dropIndex('idx_manage_menu_main_tenant_id', 'manage_menu_main');
        $this->execute('ALTER TABLE manage_menu_main  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE manage_menu_main PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
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
