<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_133900_site_master_table extends Migration
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
        // サイト設定テーブル
        $this->dropTable('site_master');

        $this->createTable('site_master', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'site_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "サイトマスターID"',
            'site_name'  => 'TINYTEXT NOT NULL COMMENT "サイト名"',
            'company_name'  => 'TINYTEXT NOT NULL COMMENT "管理会社"',
            'tanto_name'  => 'TINYTEXT NOT NULL COMMENT "担当者"',
            'support_tel_no'  => 'TINYTEXT NOT NULL COMMENT "ユーザーサポート用電話番号"',
            'site_url'  => 'TINYTEXT NOT NULL COMMENT "サイトURL"',
            'site_title'  => 'TINYTEXT NOT NULL COMMENT "PCサイトタイトル"',
            'meta_description'  => 'TINYTEXT NOT NULL COMMENT "PCメタタグ / description"',
            'meta_keywords'  => 'TINYTEXT NOT NULL COMMENT "PCメタタグ / keywords"',
            'support_mail_name'  => 'TINYTEXT NOT NULL COMMENT "ユーザーサポート送信用アドレス（名前）"',
            'support_mail_address'  => 'TINYTEXT NOT NULL COMMENT "ユーザーサポート送信用アドレス（メールアドレス）"',
            'support_mail_subject'  => 'TINYTEXT NOT NULL COMMENT "ユーザーサポート送信用アドレス（件名）"',
            'application_mail_name'  => 'TINYTEXT NOT NULL COMMENT "応募通知送信用アドレス（名前）"',
            'application_mail_address'  => 'TINYTEXT NOT NULL COMMENT "応募通知送信用アドレス（メールアドレス）"',
            'application_mail_subject'  => 'TINYTEXT NOT NULL COMMENT "応募通知送信用アドレス（件名）"',
            'regist_mail_name'  => 'TINYTEXT NOT NULL COMMENT "登録通知送信用アドレス（名前）"',
            'regist_mail_address'  => 'TINYTEXT NOT NULL COMMENT "登録通知送信用アドレス（メールアドレス）"',
            'regist_mail_subject'  => 'TINYTEXT NOT NULL COMMENT "登録通知送信用アドレス（件名）"',
            'password_mail_name'  => 'TINYTEXT NOT NULL COMMENT "パスワード再設定用アドレス（名前）"',
            'password_mail_address'  => 'TINYTEXT NOT NULL COMMENT "パスワード再設定用アドレス（メールアドレス）"',
            'password_mail_subject'  => 'TINYTEXT NOT NULL COMMENT "パスワード再設定用アドレス（件名）"',
            'expo_mail_name'  => 'TINYTEXT NOT NULL COMMENT "説明会予約通知用アドレス（名前）"',
            'expo_mail_address'  => 'TINYTEXT NOT NULL COMMENT "説明会予約通知用アドレス（メールアドレス）"',
            'expo_mail_subject'  => 'TINYTEXT NOT NULL COMMENT "説明会予約通知用アドレス（件名）"',
            'job_mail_name'  => 'TINYTEXT NOT NULL COMMENT "仕事情報転送用アドレス（名前）"',
            'job_mail_address'  => 'TINYTEXT NOT NULL COMMENT "仕事情報転送用アドレス（メールアドレス）"',
            'job_mail_subject'  => 'TINYTEXT NOT NULL COMMENT "仕事情報転送用アドレス（件名）"',
            'friend_mail_name'  => 'TINYTEXT NOT NULL COMMENT "友達に紹介する（名前）"',
            'friend_mail_address'  => 'TINYTEXT NOT NULL COMMENT "友達に紹介する（メールアドレス）"',
            'friend_mail_subject'  => 'TINYTEXT NOT NULL COMMENT "友達に紹介する（件名）"',
            'mail_sign'  => 'TINYTEXT NOT NULL COMMENT "メールの署名"',
            'review_required'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "審査機能"',
            'review_mail_name'  => 'TINYTEXT NOT NULL COMMENT "審査通知メール送信用アドレス（名前）"',
            'review_mail_address'  => 'TINYTEXT NOT NULL COMMENT "審査通知メール送信用アドレス（メールアドレス）"',
            'review_mail_subject'  => 'TINYTEXT NOT NULL COMMENT "審査通知メール送信用阿蘇レス（件名）"',
            'application_required'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "応募上限機能"',
            'oiwai_required'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "お祝い金機能"',
            'oiwai_mail_name'  => 'TINYTEXT NOT NULL COMMENT "お祝い金申請送信用アドレス（名前）"',
            'oiwai_mail_address'  => 'TINYTEXT NOT NULL COMMENT "お祝い金申請送信用アドレス（メールアドレス）"',
            'oiwai_mail_subject'  => 'TINYTEXT NOT NULL COMMENT "お祝い金申請送信用アドレス（件名）"',
            'webmail_required'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "WEBメール機能"',
            'scout_use'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "スカウトメール機能"',
            'member_use'  => Schema::TYPE_BOOLEAN . ' DEFAULT 1 NOT NULL COMMENT "会員機能"',
            'smart_site_title'  => 'TINYTEXT NOT NULL COMMENT "スマホサイトタイトル"',
            'smart_meta_description'  => 'TINYTEXT NOT NULL COMMENT "スマホメタタグ / description"',
            'smart_meta_keywords'  => 'TINYTEXT NOT NULL COMMENT "スマホメタタグ / keywords"',
            'oiwai_entry_form'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "お祝い金申込フォーム "',
            'auto_admit_required'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "自動承認機能"',
            'adoption_reminder_day'  => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "採用課金自動採用前リマインダー日数"',
            'auto_adoption_day'  => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "採用課金自動採用日数"',
            'auto_admit_day'  => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "応募課金自動承認日数"',
            'area_pref_flg'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "47都道府県機能"',
            'medium_application_flg'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "外部応募機能"',
            'encryption_flg'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "暗号化機能"',
            'login_ssl_required'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "ログインSSL対応"',
            'oiwai_entry_deadline'  => Schema::TYPE_INTEGER . ' DEFAULT 90 NOT NULL COMMENT "お祝い金申請期日"',
            'alert_job_num_flg'  => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL COMMENT "原稿数アラート機能"',
            'alert_job_num_limit'  => Schema::TYPE_INTEGER . ' DEFAULT 10000 NOT NULL COMMENT "アラート上限数"',
        ], $tableOptions. ' COMMENT="サイト設定"');


        $this->addPrimaryKey('pk_site_master', 'site_master', ['id', 'tenant_id']);
        $this->alterColumn('site_master', 'id', 'int(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->createIndex('idx_site_master_site_master_id', 'site_master', 'site_master_id');
        $this->execute('ALTER TABLE site_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

    }

    public function down()
    {
        $this->dropTable('site_master');

        $sql = <<<SQL
CREATE TABLE site_master
(
    site_master_id INT PRIMARY KEY NOT NULL,
    site_name LONGTEXT NOT NULL,
    company_name LONGTEXT NOT NULL,
    tanto_name LONGTEXT NOT NULL,
    support_tel_no LONGTEXT NOT NULL,
    site_url LONGTEXT NOT NULL,
    site_title LONGTEXT NOT NULL,
    meta_description LONGTEXT NOT NULL,
    meta_keywords LONGTEXT NOT NULL,
    support_mail_name LONGTEXT NOT NULL,
    support_mail_address LONGTEXT NOT NULL,
    support_mail_subject LONGTEXT NOT NULL,
    application_pc_mail_name LONGTEXT NOT NULL,
    application_pc_mail_address LONGTEXT NOT NULL,
    application_pc_mail_subject LONGTEXT NOT NULL,
    application_mobile_mail_name LONGTEXT NOT NULL,
    application_mobile_mail_address LONGTEXT NOT NULL,
    application_mobile_mail_subject LONGTEXT NOT NULL,
    regist_pc_mail_name LONGTEXT NOT NULL,
    regist_pc_mail_address LONGTEXT NOT NULL,
    regist_pc_mail_subject LONGTEXT NOT NULL,
    regist_mobile_mail_name LONGTEXT NOT NULL,
    regist_mobile_mail_address LONGTEXT NOT NULL,
    regist_mobile_mail_subject LONGTEXT NOT NULL,
    password_pc_mail_name LONGTEXT NOT NULL,
    password_pc_mail_address LONGTEXT NOT NULL,
    password_pc_mail_subject LONGTEXT NOT NULL,
    password_mobile_mail_name LONGTEXT NOT NULL,
    password_mobile_mail_address LONGTEXT NOT NULL,
    password_mobile_mail_subject LONGTEXT NOT NULL,
    expo_pc_mail_name LONGTEXT NOT NULL,
    expo_pc_mail_address LONGTEXT NOT NULL,
    expo_pc_mail_subject LONGTEXT NOT NULL,
    expo_mobile_mail_name LONGTEXT NOT NULL,
    expo_mobile_mail_address LONGTEXT NOT NULL,
    expo_mobile_mail_subject LONGTEXT NOT NULL,
    job_mail_name LONGTEXT NOT NULL,
    job_mail_address LONGTEXT NOT NULL,
    job_mail_subject LONGTEXT NOT NULL,
    url_mail_name LONGTEXT NOT NULL,
    url_mail_address LONGTEXT NOT NULL,
    url_mail_subject LONGTEXT NOT NULL,
    friend_mail_name LONGTEXT NOT NULL,
    friend_mail_address LONGTEXT NOT NULL,
    friend_mail_subject LONGTEXT NOT NULL,
    mail_sign LONGTEXT NOT NULL,
    review_required SMALLINT DEFAULT 1 NOT NULL,
    review_mail_name LONGTEXT NOT NULL,
    review_mail_address LONGTEXT NOT NULL,
    review_mail_subject LONGTEXT NOT NULL,
    mb_linecolor LONGTEXT NOT NULL,
    mb_beltcolor LONGTEXT NOT NULL,
    mb_textcolor001 LONGTEXT NOT NULL,
    mb_textcolor002 LONGTEXT NOT NULL,
    mb_textcolor003 LONGTEXT NOT NULL,
    mb_alinkcolor LONGTEXT NOT NULL,
    mb_vlinkcolor LONGTEXT NOT NULL,
    mb_bgcolor LONGTEXT NOT NULL,
    application_required SMALLINT DEFAULT 1,
    mobile_site_title LONGTEXT,
    mobile_meta_description LONGTEXT,
    mobile_meta_keywords LONGTEXT,
    oiwai_required SMALLINT DEFAULT 0,
    oiwai_mail_name LONGTEXT NOT NULL,
    oiwai_mail_address LONGTEXT NOT NULL,
    oiwai_mail_subject LONGTEXT NOT NULL,
    webmail_required SMALLINT DEFAULT 0 NOT NULL,
    scout_use SMALLINT DEFAULT 0,
    member_use SMALLINT DEFAULT 1,
    smart_site_title LONGTEXT NOT NULL,
    smart_meta_description LONGTEXT NOT NULL,
    smart_meta_keywords LONGTEXT NOT NULL,
    smart_use SMALLINT DEFAULT 0 NOT NULL,
    satellite_use SMALLINT DEFAULT 0 NOT NULL,
    oiwai_entry_form SMALLINT DEFAULT 0,
    auto_admit_required SMALLINT DEFAULT 0,
    adoption_reminder_day INT DEFAULT 0,
    auto_adoption_day INT DEFAULT 0,
    auto_admit_day INT DEFAULT 0,
    client_charge_required SMALLINT DEFAULT 0,
    area_pref_flg SMALLINT DEFAULT 0,
    slash_flg INT DEFAULT 0 NOT NULL,
    medium_application_flg SMALLINT DEFAULT 0 NOT NULL,
    encryption_flg SMALLINT DEFAULT 0 NOT NULL
);
CREATE UNIQUE INDEX site_master_PKI ON site_master (site_master_id);


SQL;
        $this->execute($sql);


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
