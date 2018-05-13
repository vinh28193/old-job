<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * 検索キーの項目で使用するテーブルを作成する
 */

class m160329_004831_create_searchkey_item_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }

        //検索キー項目
        for($i = 1; $i<=20; $i++) {//searchkey_item1~20まで作成
            $this->createTable('searchkey_item'.$i, [
                'id' => $this->integer()->notNull()->defaultValue(0). " COMMENT '主キー' ",
                'tenant_id' => $this->integer()->notNull()->defaultValue(0). " COMMENT 'テナントID' ",
                'searchkey_category'.$i.'_id' => $this->integer()->defaultValue(0). " COMMENT '外部キー' ",
                'searchkey_item_name' => $this->string(50)->notNull(). " COMMENT '項目名' ",
                'sort' => $this-> integer()->notNull()->defaultValue(0). " COMMENT '表示順' ",
                'valid_chk' => $this->boolean()->notNull()->defaultValue(1) . " COMMENT '公開状況' ",
            ], $tableOptions);
            $this->addPrimaryKey('pk_searchkey_item'.$i, 'searchkey_item'.$i, ['id']);
            $this->alterColumn('searchkey_item'.$i, 'id', 'INT(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
            $this->createIndex('idx_searchkey_item'.$i.'_tenant_id', 'searchkey_item'.$i, 'tenant_id');
            $this->createIndex('idx_searchkey_item'.$i.'_valid_chk', 'searchkey_item'.$i, 'valid_chk');
        }

    }

    public function down()
    {
        for($j = 1; $j <= 20; $j++) {
            $this->dropTable('searchkey_item'.$j);
        }
        return false;
    }

}
