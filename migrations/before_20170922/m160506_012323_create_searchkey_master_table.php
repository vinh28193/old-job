<?php

use yii\db\Migration;

class m160506_012323_create_searchkey_master_table extends Migration
{
    public function up()
    {
        $this->dropTable('searchkey_master');

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="検索キーマスター"';
        }

        //検索キーマスター
        $this->createTable('searchkey_master', [
            'id' => $this->primaryKey(). " COMMENT '主キー' ",
            'tenant_id' => $this->integer()->notNull()->defaultValue(0) . " COMMENT 'テナントID' ",
            'searchkey_no' => $this->integer()->notNull()->defaultValue(0) . " COMMENT '表示用主キー' ",
            'table_name' => $this->string(50)->notNull()->defaultValue(''). " COMMENT 'テーブル名' ",
            'searchkey_name' => $this->string(50)->notNull()->defaultValue(''). " COMMENT '検索キー名' ",
            'first_hierarchy_cd' => $this->string(10) . " COMMENT '第一階層URLコード' ",
            'second_hierarchy_cd' => $this->string(10) . " COMMENT '第二階層URLコード' ",
            'third_hierarchy_cd' => $this->string(10) . " COMMENT '第三階層URLコード' ",
            'is_category_label' => $this->boolean() . " COMMENT 'カテゴリラベル' ",
            'is_and_search' => $this->boolean() . " COMMENT '検索条件' ",
            'sort' => $this->boolean() . " COMMENT '表示順' ",
            'search_input_tool' => $this->boolean() . " COMMENT '表示タイプ' ",
            'is_more_search' => $this->boolean() . " COMMENT 'さらに絞り込み' ",
            'is_on_top' => $this->boolean() . " COMMENT '表示ページ' ",
            'valid_chk' => $this->boolean()->notNull()->defaultValue(1) . " COMMENT '公開状況' ",
        ], $tableOptions);
        $this->createIndex('idx_searchkey_master_tenant_id', 'searchkey_master', 'tenant_id');
        $this->createIndex('idx_searchkey_master_table_name', 'searchkey_master', 'table_name');
    }

    public function down()
    {
        $this->dropTable('searchkey_master');

        return false;
    }
}
