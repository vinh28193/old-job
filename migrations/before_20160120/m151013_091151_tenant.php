<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_091151_tenant extends Migration
{
    /**
     *
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->dropTable('tenant');
        // テナントテーブル
        $this->createTable('tenant', [
            'tenant_id' => Schema::TYPE_PK. ' COMMENT "テナントID"',
            'tenant_code' => 'VARCHAR(20) NOT NULL COMMENT "テナントコード(ドメイン名)"',
            'tenant_name' => 'VARCHAR(50) COMMENT "テナント名(サイト名)"',
            'company_name' => 'VARCHAR(20) COMMENT "会社名"',
            'language_code' => 'CHAR(2) NOT NULL COMMENT "言語コード"',
            'regist_date' => Schema::TYPE_DATETIME. ' NOT NULL COMMENT "登録日"',
            'update_date' => Schema::TYPE_DATETIME. ' NOT NULL COMMENT "更新日"',
            'del_chk' => Schema::TYPE_BOOLEAN. ' DEFAULT 0 NOT NULL COMMENT "削除フラグ"',
        ], $tableOptions. ' COMMENT="テナント"');

    }

    public function down()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        // テナントテーブル
        $this->dropTable('tenant');
        $this->createTable('tenant', [
            'tenant_id' => Schema::TYPE_PK. ' COMMENT "テナントID"',
            'tenant_code' => 'VARCHAR(20) NOT NULL COMMENT "テナントコード"',
            'tenant_name' => 'VARCHAR(50) COMMENT "テナント名"',
            'tenent_name_short' => 'VARCHAR(20) COMMENT "テナント名(略称)"',
            'language_code' => 'CHAR(2) NOT NULL COMMENT "言語コード"',
            'regist_date' => Schema::TYPE_DATETIME. ' NOT NULL COMMENT "登録日"',
            'update_date' => Schema::TYPE_DATETIME. ' NOT NULL COMMENT "更新日"',
            'del_chk' => Schema::TYPE_BOOLEAN. ' DEFAULT 0 NOT NULL COMMENT "削除フラグ"',
        ], $tableOptions. ' COMMENT="テナント"');
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
