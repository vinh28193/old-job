<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_052918_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 管理者メニューテーブル
        $this->dropTable('manage_menu_main');

        $this->createTable('manage_menu_main', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'manage_menu_main_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "管理画面少メニューID"',
            'manage_menu_category_id' => Schema::TYPE_INTEGER . ' COMMENT "管理画面大メニューID"',
            'title' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "タイトル"',
            'href' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "URL"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "状態"',
            'sort' => 'SMALLINT DEFAULT 0 NOT NULL COMMENT "表示順"',
            'corp_available' => 'SMALLINT DEFAULT 0 NOT NULL COMMENT "代理店閲覧メニュー"',
            'client_available' => 'SMALLINT DEFAULT 0 NOT NULL COMMENT "掲載企業閲覧メニュー"',
        ], $tableOptions. ' COMMENT="管理者メニュー"');

        $this->addPrimaryKey('pk_manage_menu_main', 'manage_menu_main', ['id', 'tenant_id']);
        $this->alterColumn('manage_menu_main', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_manage_menu_main_manage_menu_main_id', 'manage_menu_main', 'manage_menu_main_id');
        $this->createIndex('idx_manage_menu_main_manage_menu_category_id', 'manage_menu_main', 'manage_menu_category_id');
        $this->execute('ALTER TABLE manage_menu_main PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

    }

    public function down()
    {
        //  管理者メニューテーブル
        $this->dropTable('manage_menu_main');

        $sql = <<<SQL
CREATE TABLE manage_menu_main
(
    manage_menu_main_id INT PRIMARY KEY NOT NULL,
    manage_menu_category_id INT,
    title LONGTEXT NOT NULL,
    href LONGTEXT NOT NULL,
    valid_chk SMALLINT DEFAULT 0 NOT NULL,
    sort SMALLINT DEFAULT 0 NOT NULL,
    corp_available SMALLINT DEFAULT 0 NOT NULL,
    client_available SMALLINT DEFAULT 0 NOT NULL
);
CREATE UNIQUE INDEX manage_menu_main_PKI ON manage_menu_main (manage_menu_main_id);
CREATE INDEX manage_menu_main_manage_menu_category_id_idx ON manage_menu_main (manage_menu_category_id);

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
