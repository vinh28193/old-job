<?php

use yii\db\Migration;

class m160428_141338_create_password_reminder extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->execute('DROP TABLE IF EXISTS password_reminder');
        $this->createTable('password_reminder', [
            'id' => $this->integer()->notNull(). " COMMENT '主キー' ",
            'tenant_id' => $this->integer()->notNull()->defaultValue(0). " COMMENT 'テナントID' ",
            'key_id' => $this->integer()->notNull(). " COMMENT '会員・管理者ID' ",
            'collation_key' => $this->string(200)->notNull(). " COMMENT '照合キー' ",
            'created_at' => $this->integer()->notNull(). " COMMENT '申請日時' ",
            'key_flg' => $this->boolean()->notNull()->defaultValue(0) . " COMMENT 'アカウントフラグ' ",
        ], $tableOptions);
        $this->addPrimaryKey('pk_password_reminder','password_reminder', ['id']);
        $this->alterColumn('password_reminder','id','INT(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_password_reminder'.'_tenant_id', 'password_reminder', 'tenant_id');
    }
    public function safeDown()
    {
        $this->dropTable('password_reminder');
    }
}
