<?php

use yii\db\Migration;

/**
 * Handles the dropping for unnecessary tables.
 */
class m161219_072145_drop_unnecessary_tables extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropTable('job_clip');
        $this->dropTable('member_cookie_sessionid');
//        $this->dropTable('member_master');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->createTable('job_clip',[
            'job_clip_id' => $this->primaryKey(),
            'job_id' => $this->text()->notNull(),
            'member_id' => $this->integer(11)->notNull(),
            'valid_chk' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createTable('member_cookie_sessionid',[
            'member_id' => $this->integer(11)->notNull(),
            'session_id' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

//        $this->createTable('member_master',[
//            'id' => $this->integer(11)->comment('ID'),
//            'tenant_id' => $this->integer()->notNull()->comment('テナントID'),
//            'member_no' => $this->integer()->notNull()->comment('登録者ナンバー'),
//            'login_id' => 'varchar(255) DEFAULT NULL COMMENT "ログインＩＤ"',
//            'password' => 'varchar(255) DEFAULT NULL COMMENT "パスワード"',
//            'name_sei' => 'varchar(255) DEFAULT NULL COMMENT "名前(性)"',
//            'name_mei' => 'varchar(255) DEFAULT NULL COMMENT "名前(名)"',
//            'kana_sei' => 'varchar(255) DEFAULT NULL COMMENT "かな(性)"',
//            'kana_mei' => 'varchar(255) DEFAULT NULL COMMENT "かな(名)"',
//            'sex_type' => 'varchar(255) DEFAULT NULL COMMENT "性別"',
//            'birth_date' => $this->date()->notNull()->comment('誕生日'),
//            'mail_address_flg' => $this->bigInteger(4)->comment('メールアドレス判別フラグ'),
//            'mail_address' => 'varchar(32) DEFAULT NULL',
//            'occupation_id' => $this->smallInteger(6)->defaultValue(null)->comment('属性コード'),
//            'area_id' => $this->smallInteger(6)->defaultValue(null)->comment('エリアコード'),
//            'option100' => $this->text()->comment('オプション項目100'),
//            'option101' => $this->text()->comment('オプション項目101'),
//            'option102' => $this->text()->comment('オプション項目102'),
//            'option103' => $this->text()->comment('オプション項目103'),
//            'option104' => $this->text()->comment('オプション項目104'),
//            'option105' => $this->text()->comment('オプション項目105'),
//            'option106' => $this->text()->comment('オプション項目106'),
//            'option107' => $this->text()->comment('オプション項目107'),
//            'option108' => $this->text()->comment('オプション項目108'),
//            'option109' => $this->text()->comment('オプション項目109'),
//            'created_at' => $this->integer(11)->notNull()->comment('登録日時'),
//            'updated_at' => $this->integer(11)->notNull()->comment('更新日時'),
//            'carrier_type' => $this->bigInteger(4)->notNull()->defaultValue(0)->comment('登録機器'),
//        ],  'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT="登録"');
//        $this->addPrimaryKey('pk-member_master','member_master',['id','tenant_id']);
    }
}
