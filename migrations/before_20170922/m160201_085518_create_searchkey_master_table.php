<?php

use yii\db\Schema;
use yii\db\Migration;

class m160201_085518_create_searchkey_master_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }

        //検索キーマスター
        $this->createTable('searchkey_master', [
            'id' => $this->integer()->notNull()->defaultValue(0). " COMMENT '主キー' ",
            'tenant_id' => $this->integer()->notNull()->defaultValue(0) . " COMMENT 'テナントID' ",
            'searchkey_no' => $this->integer()->notNull()->defaultValue(0) . " COMMENT '表示用主キー' ",
            'table_name' => $this->string(50)->notNull()->defaultValue(''). " COMMENT 'テーブル名' ",
            'searchkey_name' => $this->string(50)->notNull()->defaultValue(''). " COMMENT '検索キー名' ",
            'category_select' => $this->boolean() . " COMMENT 'カテゴリ選択' ",
            'and_select' => $this->boolean() . " COMMENT '検索条件' ",
            'sort' => $this->boolean() . " COMMENT '表示順' ",
            'disp_method' => $this->boolean() . " COMMENT '表示タイプ' ",
            'more_select' => $this->boolean() . " COMMENT 'さらに絞り込み' ",
            'disp_page_top' => $this->boolean() . " COMMENT '表示ページ' ",
            'valid_chk' => $this->boolean()->notNull()->defaultValue(1) . " COMMENT '公開状況' ",
        ], $tableOptions);
        $this->addPrimaryKey('pk_searchkey_master', 'searchkey_master', ['id']);
        $this->alterColumn('searchkey_master', 'id', 'INT(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_searchkey_master_tenant_id', 'searchkey_master', 'tenant_id');
        $this->createIndex('idx_searchkey_master_table_name', 'searchkey_master', 'table_name');
    }

    public function down()
    {
        $this->dropTable('searchkey_master');

        return false;
    }

}
