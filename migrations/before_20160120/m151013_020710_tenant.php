<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_020710_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 管理者除外メニューセットテーブル
        $this->dropTable('manage_menu_admin_exception');

        $this->createTable('manage_menu_admin_exception', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'admin_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "管理者ID"',
            'manage_menu_main_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "管理画面小メニューID"',
        ], $tableOptions. ' COMMENT="管理者除外メニューセット"');

        $this->addPrimaryKey('pk_manage_menu_admin_exception', 'manage_menu_admin_exception', ['id', 'tenant_id']);
        $this->alterColumn('manage_menu_admin_exception', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_manage_menu_admin_exception_admin_id', 'manage_menu_admin_exception', 'admin_id');
        $this->createIndex('idx_manage_menu_admin_exception_manage_menu_main_id', 'manage_menu_admin_exception', 'manage_menu_main_id');
        $this->execute('ALTER TABLE manage_menu_admin_exception PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

    }

    public function down()
    {
        //  管理者除外メニューセットテーブル
        $this->dropTable('manage_menu_admin_exception');

        $sql = <<<SQL
CREATE TABLE manage_menu_admin_exception
(
    admin_id INT NOT NULL,
    manage_menu_main_id INT NOT NULL,
    PRIMARY KEY (admin_id, manage_menu_main_id)
);
CREATE UNIQUE INDEX manage_menu_admin_exception_PKI ON manage_menu_admin_exception (admin_id, manage_menu_main_id);

SQL;
        $this->execute($sql);
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
