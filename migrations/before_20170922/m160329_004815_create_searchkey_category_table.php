<?php

use yii\db\Schema;
use yii\db\Migration;
/**
 * 検索キーのカテゴリーで使用するテーブルを作成する
 */

class m160329_004815_create_searchkey_category_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }

        //検索キーカテゴリー
        for ($i = 1; $i <=10; $i++) {//searchkey_category1~10まで作成

            $this->createTable('searchkey_category'.$i, [
                'id' => $this->integer()->notNull()->defaultValue(0). " COMMENT '主キー' ",
                'tenant_id' => $this->integer()->notNull()->defaultValue(0). " COMMENT 'テナントID' ",
                'searchkey_category_name' => $this->string(50)->notNull(). " COMMENT 'カテゴリ名' ",
                'sort' => $this->integer()->notNull()->defaultValue(0). " COMMENT '表示順' ",
                'valid_chk' => $this->boolean()->notNull()->defaultValue(1) . " COMMENT '公開状況' ",
            ], $tableOptions);
            $this->addPrimaryKey('pk_searchkey_category'.$i, 'searchkey_category'.$i, ['id']);
            $this->alterColumn('searchkey_category'.$i, 'id', 'INT(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
            $this->createIndex('idx_searchkey_category'.$i.'_tenant_id', 'searchkey_category'.$i, 'tenant_id');
            $this->createIndex('idx_searchkey_category'.$i.'_valid_chk', 'searchkey_category'.$i, 'valid_chk');
        }
    }

    public function down()
    {
        for($j = 1; $j<=20; $j++) {
            $this->dropTable('searchkey_category' . $j);
        }
        return false;
    }

}
