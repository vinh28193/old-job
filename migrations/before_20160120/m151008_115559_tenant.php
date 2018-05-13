<?php

use yii\db\Schema;
use yii\db\Migration;

class m151008_115559_tenant extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 掲載企業テーブル
        $this->dropTable('client_master');

        $this->createTable('client_master', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'client_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "掲載企業ID"',
            'corp_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "代理店ID"',
            'client_name' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "掲載企業名"',
            'client_name_kana' => Schema::TYPE_TEXT . ' COMMENT "掲載企業名カナ"',
            'tel_no' => 'VARCHAR(30) COMMENT "電話番号"',
            'address' => Schema::TYPE_TEXT . ' COMMENT "住所"',
            'tanto_name' => Schema::TYPE_TEXT . ' COMMENT "担当者名"',
            'regist_date' => Schema::TYPE_DATE . ' NOT NULL COMMENT "登録日"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 NOT NULL COMMENT "取引状態"',
            'client_business_outline' => Schema::TYPE_TEXT . ' COMMENT "事業内容"',
            'client_corporate_url' => Schema::TYPE_TEXT . ' COMMENT "ホームページ"',
            'admin_memo' => Schema::TYPE_TEXT . ' COMMENT "運営元メモ"',
        ], $tableOptions. ' COMMENT="掲載企業"');

        $this->addPrimaryKey('pk_client_master', 'client_master', ['id', 'tenant_id']);
        $this->alterColumn('client_master', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_client_master_client_id', 'client_master', 'client_id');
        $this->execute('ALTER TABLE client_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 掲載企業オプションテーブル
        $this->createTable('client_option', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'client_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "掲載企業ID"',
            'function_item_id' => Schema::TYPE_INTEGER . '  COMMENT "項目管理ID"',
            'option' => Schema::TYPE_TEXT . '  COMMENT "オプション項目内容"',
        ], $tableOptions. ' COMMENT="掲載企業オプション"');

        $this->addPrimaryKey('pk_client_option', 'client_option', ['id', 'tenant_id']);
        $this->alterColumn('client_option', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_client_option_client_id', 'client_option', 'client_id');
        $this->execute('ALTER TABLE client_option PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
    }

    public function down()
    {
        // 掲載企業テーブル
        $this->dropTable('client_master');

        $sql = <<<SQL
CREATE TABLE client_master
(
    client_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    corp_id INT NOT NULL,
    client_name LONGTEXT NOT NULL,
    client_name_kana LONGTEXT NOT NULL,
    tel_no LONGTEXT NOT NULL,
    address LONGTEXT NOT NULL,
    tanto_name LONGTEXT NOT NULL,
    regist_datetime DATE DEFAULT '2014-01-01' NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    client_business_outline LONGTEXT NOT NULL,
    client_corporate_url LONGTEXT NOT NULL,
    option100 LONGTEXT NOT NULL,
    option101 LONGTEXT NOT NULL,
    option102 LONGTEXT NOT NULL,
    option103 LONGTEXT NOT NULL,
    option104 LONGTEXT NOT NULL,
    option105 LONGTEXT NOT NULL,
    option106 LONGTEXT NOT NULL,
    option107 LONGTEXT NOT NULL,
    option108 LONGTEXT NOT NULL,
    option109 LONGTEXT NOT NULL,
    admin_memo LONGTEXT
);
CREATE UNIQUE INDEX client_master_PKI ON client_master (client_id);
SQL;
        $this->execute($sql);

        // 掲載企業オプションテーブル
        $this->dropTable('client_option');
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
