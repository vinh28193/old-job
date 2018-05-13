<?php

use yii\db\Migration;

class m160401_054755_create_wage_item extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->createTable('wage_item', [
            'id' => $this->integer()->notNull()->defaultValue(0). " COMMENT '主キー' ",
            'tenant_id' => $this->integer()->notNull()->defaultValue(0). " COMMENT 'テナントID' ",
            'wage_category_id' => $this->integer()->notNull(). " COMMENT '外部キー' ",
            'wage_item_name' => $this->integer()->notNull(). " COMMENT '項目名' ",
            'sort' => $this->integer()->notNull()->defaultValue(0). " COMMENT '表示順' ",
            'valid_chk' => $this->boolean()->notNull()->defaultValue(1) . " COMMENT '公開状況' ",
        ], $tableOptions);
        $this->addPrimaryKey('pk_wage_item','wage_item', ['id']);
        $this->alterColumn('wage_item','id','INT(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_wage_item_tenant_id', 'wage_item', 'tenant_id');
        $this->createIndex('idx_wage_item_valid_chk', 'wage_item', 'valid_chk');
    }

    public function safeDown()
    {
        $this->dropTable('wage_item');
    }
}
