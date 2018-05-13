<?php

use yii\db\Schema;
use yii\db\Migration;

class m150928_122012_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // テナントテーブル
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


        // 管理者テーブル
        $this->dropTable('admin_master');

        $this->createTable('admin_master', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'admin_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "管理者ID"',
            'corp_id' => Schema::TYPE_INTEGER . '  COMMENT "代理店ID"',
            'login_id' => 'VARCHAR(255) DEFAULT "" NOT NULL COMMENT "ログインID"',
            'password' => 'VARCHAR(255) DEFAULT "" NOT NULL COMMENT "パスワード"',
            'regist_datetime' => Schema::TYPE_TIMESTAMP . ' NOT NULL COMMENT "登録日時"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 NOT NULL COMMENT "状態"',
            'name_sei' => Schema::TYPE_TEXT . '  COMMENT "名前(性)"',
            'name_mei' => Schema::TYPE_TEXT . '  COMMENT "名前(名)"',
            'tel_no' => 'VARCHAR(30) COMMENT "電話番号"',
            'client_id' => Schema::TYPE_INTEGER . ' COMMENT "掲載企業ID"',
            'mail_address' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "メールアドレス"',
        ], $tableOptions. ' COMMENT="管理者"');

        $this->addPrimaryKey('pk_admin_master', 'admin_master', ['id', 'tenant_id']);
        $this->alterColumn('admin_master', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_admin_master_admin_id', 'admin_master', 'admin_id');
        $this->createIndex('idx_admin_master_login_id', 'admin_master', 'login_id');
        $this->execute('ALTER TABLE admin_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 管理者オプションテーブル
        $this->createTable('admin_option', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'admin_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "管理者ID"',
            'function_item_id' => Schema::TYPE_INTEGER . '  COMMENT "項目管理ID"',
            'option' => Schema::TYPE_TEXT . '  COMMENT "オプション項目内容"',
        ], $tableOptions. ' COMMENT="管理者オプション"');

        $this->addPrimaryKey('pk_admin_option', 'admin_option', ['id', 'tenant_id']);
        $this->alterColumn('admin_option', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_admin_option_admin_id', 'admin_option', 'admin_id');
        $this->execute('ALTER TABLE admin_option PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

    }

    public function down()
    {

        // テナントテーブル
        $this->dropTable('tenant');

        // 管理者テーブル
        $this->dropTable('admin_master');

        $sql = <<<SQL
CREATE TABLE admin_master
(
    admin_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    corp_id INT,
    login_id VARCHAR(255) DEFAULT '' NOT NULL,
    password VARCHAR(255) DEFAULT '' NOT NULL,
    regist_datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    option100 LONGTEXT,
    option101 LONGTEXT,
    option102 LONGTEXT,
    option103 LONGTEXT,
    option104 LONGTEXT,
    option105 LONGTEXT,
    option106 LONGTEXT,
    option107 LONGTEXT,
    option108 LONGTEXT,
    option109 LONGTEXT,
    name_sei LONGTEXT,
    name_mei LONGTEXT,
    tel_no LONGTEXT NOT NULL,
    client_id INT,
    mail_address LONGTEXT NOT NULL
);
CREATE UNIQUE INDEX admin_master_PKI ON admin_master (admin_id);
CREATE INDEX admin_id ON admin_master (admin_id);
CREATE INDEX admin_master_client_id_idx ON admin_master (client_id);
CREATE INDEX admin_master_corp_id_idx ON admin_master (corp_id);
CREATE INDEX admin_master_login_id_idx ON admin_master (login_id);
SQL;
        $this->execute($sql);

        // 管理者オプションテーブル
        $this->dropTable('admin_option');

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
