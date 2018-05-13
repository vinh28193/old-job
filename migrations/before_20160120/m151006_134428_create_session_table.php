<?php

use yii\db\Schema;
use yii\db\Migration;

class m151006_134428_create_session_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // ユーザーSESSION
        $this->createTable('user_session', [
            'id' => Schema::TYPE_STRING. ' NOT NULL PRIMARY KEY COMMENT "SESSION ID"',
            'expire' => Schema::TYPE_INTEGER . ' COMMENT "有効期限"',
            'data' => Schema::TYPE_TEXT . ' COMMENT "データ"',
        ], $tableOptions);

        // 管理者SESSION情報テーブル
        $this->createTable('manager_session', [
            'id' => Schema::TYPE_STRING. ' NOT NULL PRIMARY KEY COMMENT "SESSION ID"',
            'admin_id' => Schema::TYPE_INTEGER. ' COMMENT "管理者ID"',
            'expire' => Schema::TYPE_INTEGER . ' COMMENT "有効期限"',
            'data' => Schema::TYPE_TEXT . ' COMMENT "データ"',
        ], $tableOptions);
        $this->createIndex('idx_manager_session_admin_id', 'manager_session', 'admin_id');

    }

    public function down()
    {
        $this->dropTable('user_session');
        $this->dropTable('manager_session');
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
