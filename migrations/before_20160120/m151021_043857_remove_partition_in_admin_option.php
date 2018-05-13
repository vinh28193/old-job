<?php

use yii\db\Migration;

class m151021_043857_remove_partition_in_admin_option extends Migration
{
    public function up()
    {
        // パーテーション解除
        $this->execute('ALTER TABLE admin_option REMOVE PARTITIONING;');
        // テーブル構造修正
        $this->execute('ALTER TABLE admin_option  DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');
        $this->createIndex('idx_admin_option_tenant_id', 'admin_option', 'tenant_id');
        // カラム名修正
        $this->renameColumn('admin_option', 'option', 'option_value');
        $this->renameColumn('admin_option', 'admin_id', 'admin_master_id');

    }

    public function down()
    {
        // カラム名修正
        $this->renameColumn('admin_option', 'option_value', 'option');
        $this->renameColumn('admin_option', 'admin_master_id', 'admin_id');

        // テーブル構造修正
        $this->dropIndex('idx_admin_option_tenant_id', 'admin_option');
        $this->execute('ALTER TABLE admin_option  DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');
        // パーテーション設定
        $this->execute('ALTER TABLE admin_option PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

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
