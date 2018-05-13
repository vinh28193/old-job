<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * 掲載企業詳細表示のテーブル作成
 * @author Yukinori Nakamura<y_nakamura@id-frontier.jp>
 */
class m160315_074737_create_client_disp extends Migration
{    
    const TABLE_NAME = 'client_disp';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TABLE_NAME, [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'client_column' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "掲載企業項目カラム名"',
            'sort_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "表示順"',
            'disp_type_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "掲載タイプ"',
        ], $tableOptions . ' COMMENT="掲載企業詳細-掲載タイプ"');
    }

    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
