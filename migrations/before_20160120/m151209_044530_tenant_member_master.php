<?php

use yii\db\Schema;
use yii\db\Migration;

class m151209_044530_tenant_member_master extends Migration

{
    public function Up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 登録者テーブル
        $this->dropTable('member_master');

        $this->createTable('member_master', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'member_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "登録者ナンバー"',
            'login_id' => Schema::TYPE_STRING . ' COMMENT "ログインＩＤ"',
            'password' => Schema::TYPE_STRING . ' COMMENT "パスワード"',
            'name_sei' => Schema::TYPE_STRING . ' COMMENT "名前(性)"',
            'name_mei' => Schema::TYPE_STRING . ' COMMENT "名前(名)"',
            'kana_sei' => Schema::TYPE_STRING . ' COMMENT "かな(性)"',
            'kana_mei' => Schema::TYPE_STRING . ' COMMENT "かな(名)"',
            'sex_type' => Schema::TYPE_STRING . ' COMMENT "性別"',
            'birth_date' => Schema::TYPE_DATE . ' COMMENT "誕生日"',
            'mail_address_flg' => 'TINYINT COMMENT "メールアドレス判別フラグ"',
            'mail_address' => Schema::TYPE_STRING . ' COMMENT "メールアドレス"',
            'occupation_cd' => 'SMALLINT COMMENT "属性コード"',
            'area_cd' => 'SMALLINT COMMENT "エリアコード"',
            'option100' => Schema::TYPE_TEXT . ' COMMENT "オプション項目100"',
            'option101' => Schema::TYPE_TEXT . ' COMMENT "オプション項目101"',
            'option102' => Schema::TYPE_TEXT . ' COMMENT "オプション項目102"',
            'option103' => Schema::TYPE_TEXT . ' COMMENT "オプション項目103"',
            'option104' => Schema::TYPE_TEXT . ' COMMENT "オプション項目104"',
            'option105' => Schema::TYPE_TEXT . ' COMMENT "オプション項目105"',
            'option106' => Schema::TYPE_TEXT . ' COMMENT "オプション項目106"',
            'option107' => Schema::TYPE_TEXT . ' COMMENT "オプション項目107"',
            'option108' => Schema::TYPE_TEXT . ' COMMENT "オプション項目108"',
            'option109' => Schema::TYPE_TEXT . ' COMMENT "オプション項目109"',
            'regist_datetime' => Schema::TYPE_TIMESTAMP . ' COMMENT "登録日時"',
            'update_datetime' => Schema::TYPE_TIMESTAMP . ' COMMENT "更新日時"',
            'carrier_type' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "登録機器"',
        ], $tableOptions. ' COMMENT="登録"');

        $this->addPrimaryKey('pk_member_master', 'member_master', ['id', 'tenant_id']);
        $this->alterColumn('member_master', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE member_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

    }


    public function down()
    {
            // 登録者テーブル
            $this->dropTable('member_master');

            $sql = <<<SQL
    CREATE TABLE member_master
    (
        member_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        password LONGTEXT,
        name_sei LONGTEXT,
        name_mei LONGTEXT,
        kana_sei LONGTEXT,
        kana_mei LONGTEXT,
        mail_address LONGTEXT,
        sex_type LONGTEXT,
        login_id LONGTEXT,
        birthdate DATE,
        occupation_cd SMALLINT,
        area_cd SMALLINT,
        mail_address_flg SMALLINT,
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
        regist_datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
        carrier_type SMALLINT DEFAULT 0 NOT NULL,
        skill LONGTEXT,
    );
    CREATE UNIQUE INDEX member_master_PKI ON member_master (member_id);
SQL;
            $this->execute($sql);
        }
}
