<?php

use yii\db\Schema;
use yii\db\Migration;

class m151008_054429_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 項目管理テーブル
        $this->dropTable('function_item_set');

        $this->createTable('function_item_set', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'function_item_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "項目管理ID"',
            'manage_menu_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "メニューID"',
            'item_name' => Schema::TYPE_TEXT . ' COMMENT "項目名"',
            'item_data_type' => Schema::TYPE_TEXT . ' COMMENT "入力項目形式"',
            'item_maxlength' => Schema::TYPE_INTEGER . ' COMMENT "文字数上限"',
            'is_must_item' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "必須入力"',
            'is_list_menu_item' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "検索一覧表示"',
            'is_search_menu_item' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "検索項目表示"',
            'is_system_item' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "システム項目"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "公開状況"',
            'index_no' => Schema::TYPE_INTEGER . ' COMMENT "行番号"',
            'item_default_name' => Schema::TYPE_TEXT . ' COMMENT "デフォルト項目名"',
            'is_option' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "オプション項目"',
            'is_file' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "ファイルアップロード項目"',
            'place_holder' => Schema::TYPE_TEXT . ' COMMENT "プレースホルダ"',
            'item_column' => 'VARCHAR(200) COMMENT "カラム名"',
            'freeword_flg' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "フリーワード検索フラグ"',
            'is_common' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "連携項目"',
            'is_common_target_id' => Schema::TYPE_INTEGER . '  COMMENT "連携項目ID"',
        ], $tableOptions. ' COMMENT="項目管理"');

        $this->addPrimaryKey('pk_function_item_set', 'function_item_set', ['id', 'tenant_id']);
        $this->alterColumn('function_item_set', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_function_item_set_function_item_id', 'function_item_set', 'function_item_id');
        $this->execute('ALTER TABLE function_item_set PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
    }

    public function down()
    {
        // 項目管理テーブル
        $this->dropTable('function_item_set');
        $sql = <<<SQL
CREATE TABLE function_item_set
(
    function_item_id INT PRIMARY KEY NOT NULL,
    manage_menu_id INT NOT NULL,
    item_name LONGTEXT,
    item_data_type LONGTEXT,
    item_maxlength INT,
    is_must_item TINYINT DEFAULT 0,
    is_list_menu_item TINYINT DEFAULT 0,
    is_search_menu_item TINYINT DEFAULT 0,
    is_system_item TINYINT DEFAULT 0,
    valid_chk TINYINT DEFAULT 0,
    index_no INT,
    item_default_name LONGTEXT,
    is_option TINYINT DEFAULT 0,
    emoji LONGTEXT,
    is_file INT DEFAULT 0 NOT NULL,
    place_holder LONGTEXT,
    item_column VARCHAR(200),
    freeword_flg TINYINT DEFAULT 0,
    is_common TINYINT DEFAULT 0,
    is_common_target_id INT
);
CREATE UNIQUE INDEX function_item_set_PKI ON function_item_set (function_item_id);
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
