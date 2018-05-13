<?php

use yii\db\Migration;

class m151020_072414_remove_partition_in_manage_menu_admin_exception extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE manage_menu_admin_exception REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE manage_menu_admin_exception  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_manage_menu_admin_exception_tenant_id', 'manage_menu_admin_exception', 'tenant_id');
    }

    public function down()
    {
        // テーブル構造修正
        $this->dropIndex('idx_manage_menu_admin_exception_tenant_id', 'manage_menu_admin_exception');
        $this->execute('ALTER TABLE manage_menu_admin_exception  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE manage_menu_admin_exception PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
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
