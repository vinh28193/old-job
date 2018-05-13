<?php

use yii\db\Migration;

class m160401_051345_create_wage_category extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->createTable('wage_category', [
            'id' => $this->integer()->notNull(). " COMMENT '主キー' ",
            'tenant_id' => $this->integer()->notNull()->defaultValue(0). " COMMENT 'テナントID' ",
            'wage_category_name' => $this->string(50)->notNull(). " COMMENT 'カテゴリ名' ",
            'sort' => $this->integer()->notNull()->defaultValue(0). " COMMENT '表示順' ",
            'valid_chk' => $this->boolean()->notNull()->defaultValue(1) . " COMMENT '公開状況' ",
        ], $tableOptions);
        $this->addPrimaryKey('pk_wage_category','wage_category', ['id']);
        $this->alterColumn('wage_category','id','INT(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_wage_category'.'_tenant_id', 'wage_category', 'tenant_id');
        $this->createIndex('idx_wage_category'.'_valid_chk', 'wage_category', 'valid_chk');
    }
    public function safeDown()
    {
        $this->dropTable('wage_category');
    }
}
