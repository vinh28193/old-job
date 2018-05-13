<?php

use yii\db\Schema;
use yii\db\Migration;

class m151029_071722_tenant extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 応募テーブル
        $this->dropTable('application_master');

        $this->createTable('application_master', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'application_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "応募ナンバー"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'member_master_id' => Schema::TYPE_INTEGER . ' COMMENT "テーブルmember_masterのカラムid"',
            'name_sei' => Schema::TYPE_STRING . ' COMMENT "名前(性)"',
            'name_mei' => Schema::TYPE_STRING . ' COMMENT "名前(名)"',
            'kana_sei' => Schema::TYPE_STRING . ' COMMENT "かな(性)"',
            'kana_mei' => Schema::TYPE_STRING . ' COMMENT "かな(名)"',
            'sex' => Schema::TYPE_STRING . ' COMMENT "性別"',
            'birth_date' => Schema::TYPE_DATE . ' COMMENT "誕生日"',
            'pref_cd' => 'SMALLINT COMMENT "都道府県コード"',
            'address' => Schema::TYPE_TEXT . ' COMMENT "住所"',
            'tel_no' => 'VARCHAR(30) COMMENT "電話番号"',
            'mail_address_flg' => 'TINYINT COMMENT "メールアドレス判別フラグ"',
            'mail_address' => Schema::TYPE_STRING . ' COMMENT "メールアドレス"',
            'occupation_cd' => Schema::TYPE_INTEGER . ' COMMENT "属性"',
            'self_pr' => Schema::TYPE_TEXT . ' COMMENT "自己PR"',
            'regist_datetime' => Schema::TYPE_TIMESTAMP . ' NOT NULL COMMENT "応募日時"',
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
            'status' => 'TINYINT DEFAULT 0 COMMENT "採用状況"',
            'carrier_type' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "応募機器"',
            'application_memo' => Schema::TYPE_TEXT . ' COMMENT "備考"',
            'oiwai_status' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "お祝い金申請状況"',
            'oiwai_pass' => Schema::TYPE_STRING . ' COMMENT "お祝い金パスワード"',
            'oiwai_price' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "お祝い金金額(応募時)"',
            'disp_price' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "掲載料金"',
            'admit_date' => Schema::TYPE_DATE . ' COMMENT "確定日"',
            'application_file' => Schema::TYPE_STRING . ' NOT NULL COMMENT "添付ファイル名"',
            'application_file_disp' => Schema::TYPE_STRING . ' NOT NULL COMMENT "添付ファイル名(ユーザーが命名)"',
            'admit_status' => 'TINYINT DEFAULT 0 COMMENT "確定状況"',
            'first_admit_date' => Schema::TYPE_DATE . ' COMMENT "初回確定日"',
        ], $tableOptions. ' COMMENT="応募"');

        $this->addPrimaryKey('pk_application_master', 'application_master', ['id', 'tenant_id']);
        $this->alterColumn('application_master', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE application_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 応募者採用状況テーブル
        $this->dropTable('application_status_cd');

        $this->createTable('application_status_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'application_status_cd' => 'TINYINT NOT NULL COMMENT "状況コード"',
            'application_status' => Schema::TYPE_STRING . ' NOT NULL COMMENT "状況名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'display_order' => 'TINYINT DEFAULT 0 COMMENT "表示順"',
        ], $tableOptions. ' COMMENT="応募者採用状況"');


        // 掲載タイプテーブル
        $this->dropTable('disp_type_cd');

        $this->createTable('disp_type_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'disp_type_cd' => 'TINYINT NOT NULL COMMENT "掲載タイプコード"',
            'disp_type_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "掲載タイプ名"',
            'sort_no' => 'TINYINT NOT NULL COMMENT "表示順"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'del_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "削除フラグ"',
        ], $tableOptions. ' COMMENT="掲載タイプ"');


        // 市区町村テーブル
        $this->dropTable('dist_cd');

        $this->createTable('dist_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'pref_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルpref_cdのカラムid"',
            'dist_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "市区町村名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'dist_sub_cd' => Schema::TYPE_INTEGER . ' DEFAULT 1 COMMENT "市区町村サブコード"',
            'sort' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "表示順"',
            'dist_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "市区町村コード"',
        ], $tableOptions. ' COMMENT="市区町村"');


        // 雇用形態コードテーブル
        $this->dropTable('employment_type_cd');

        $this->createTable('employment_type_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'employment_type_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "雇用形態名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "表示順"',
            'employment_type_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "雇用形態コード"',
        ], $tableOptions. ' COMMENT="雇用形態コード"');


        // 職種小テーブル
        $this->dropTable('job_type_small');

        $this->createTable('job_type_small', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'job_type_small_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_type_small_cdのカラムid"',
        ], $tableOptions. ' COMMENT="職種小"');


        // 職種小コードテーブル
        $this->dropTable('job_type_small_cd');

        $this->createTable('job_type_small_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_type_small_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "職種小名"',
            'job_type_big_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_type_big_cdのカラムid"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_INTEGER . ' COMMENT "表示順"',
            'job_type_small_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "職種小コード"',
        ], $tableOptions. ' COMMENT="職種小コード"');


        // 属性テーブル
        $this->dropTable('occupation_cd');

        $this->createTable('occupation_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'occupation_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "属性名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_INTEGER . ' COMMENT "表示順"',
            'occupation_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "属性コード"',
        ], $tableOptions. ' COMMENT="属性"');


        // 都道府県テーブル
        $this->dropTable('pref_cd');

        $this->createTable('pref_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'pref_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "都道府県コード"',
            'pref_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "都道府県名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'area_cd_id' => Schema::TYPE_INTEGER . ' COMMENT "テーブルare_cdのカラムid"',
            'sort' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "表示順"',
        ], $tableOptions. ' COMMENT="都道府県"');


        // 管理者メニューテーブル
        $this->update('manage_menu_main', ['href' => '/manage/secure/job/list','title' => '求人情報一覧'], 'href="/manage/secure/job/job_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/corp/list','title' => '代理店情報一覧'], 'href="/manage/secure/corp/corp_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/client/list','title' => '掲載企業情報一覧'], 'href="/manage/secure/client/client_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/admin/list','title' => '管理者情報一覧'], 'href="/manage/secure/admin/admin_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/application/list'], 'href="/manage/secure/application/application_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_job/list','title' => '求人原稿項目設定'], 'href="/manage/secure/option_job/job_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_corp/list','title' => '代理店項目設定'], 'href="/manage/secure/option_corp/corp_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_client/list','title' => '掲載企業項目設定'], 'href="/manage/secure/option_client/client_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_admin/list','title' => '管理者項目設定'], 'href="/manage/secure/option_admin/admin_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_application/list','title' => '応募者項目設定'], 'href="/manage/secure/option_application/application_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_member/list','title' => '登録者項目設定'], 'href="/manage/secure/option_member/member_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/jobtype/list'], 'href="/manage/secure/jobtype/jobtype_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/area/list'], 'href="/manage/secure/area/area_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/pref/list'], 'href="/manage/secure/pref/pref_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/dist/list'], 'href="/manage/secure/dist/dist_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/prefdist/list'], 'href="/manage/secure/prefdist/prefdist_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/route/list'], 'href="/manage/secure/route/route_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/station/list'], 'href="/manage/secure/station/station_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/employment/list'], 'href="/manage/secure/employment/employment_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/occupation/list'], 'href="/manage/secure/occupation/occupation_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/worktime/list'], 'href="/manage/secure/worktime/worktime_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/wage/list'], 'href="/manage/secure/wage/wage_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_disptype/list'], 'href="/manage/secure/option_disptype/disptype_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/merit/list'], 'href="/manage/secure/merit/merit_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_scout/list'], 'href="/manage/secure/option_scout/scout_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/media_upload/list'], 'href="/manage/secure/media_upload/upload_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/admin/create'], 'href="/manage/secure/admin/admin_regist/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/profile/create'], 'href="/manage/secure/profile/profile_regist/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/media_upload/create'], 'href="/manage/secure/media_upload/upload_regist/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_search/list'], 'href="/manage/secure/option_search/option_search_list/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/corp/create'], 'href="/manage/secure/corp/corp_regist/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/client/create'], 'href="/manage/secure/client/client_regist/"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/job/create'], 'href="/manage/secure/job/job_regist/"');

        $this->insert('manage_menu_main', [
            'tenant_id' => '1',
            'manage_menu_main_id' => '80',
            'manage_menu_category_id' => '6',
            'title' => '管理者の編集',
            'href' => '/manage/secure/admin/update',
            'valid_chk' => '1',
            'sort' => '3',
            'corp_available' => '0',
            'client_available' => '0',
            'icon_key' => 'edit',
        ]);
        $this->insert('manage_menu_main', [
            'tenant_id' => '1',
            'manage_menu_main_id' => '81',
            'manage_menu_category_id' => '2',
            'title' => '代理店の編集',
            'href' => '/manage/secure/corp/update',
            'valid_chk' => '1',
            'sort' => '3',
            'corp_available' => '0',
            'client_available' => '0',
            'icon_key' => 'edit',
        ]);
        $this->insert('manage_menu_main', [
            'tenant_id' => '1',
            'manage_menu_main_id' => '82',
            'manage_menu_category_id' => '2',
            'title' => '掲載企業の編集',
            'href' => '/manage/secure/client/update',
            'valid_chk' => '1',
            'sort' => '3',
            'corp_available' => '1',
            'client_available' => '0',
            'icon_key' => 'edit',
        ]);
        $this->insert('manage_menu_main', [
            'tenant_id' => '2',
            'manage_menu_main_id' => '80',
            'manage_menu_category_id' => '6',
            'title' => '管理者の編集',
            'href' => '/manage/secure/admin/update',
            'valid_chk' => '1',
            'sort' => '3',
            'corp_available' => '0',
            'client_available' => '0',
            'icon_key' => 'edit',
        ]);
        $this->insert('manage_menu_main', [
            'tenant_id' => '2',
            'manage_menu_main_id' => '81',
            'manage_menu_category_id' => '6',
            'title' => '代理店の編集',
            'href' => '/manage/secure/corp/update',
            'valid_chk' => '1',
            'sort' => '3',
            'corp_available' => '0',
            'client_available' => '0',
            'icon_key' => 'edit',
        ]);
        $this->insert('manage_menu_main', [
            'tenant_id' => '2',
            'manage_menu_main_id' => '82',
            'manage_menu_category_id' => '2',
            'title' => '掲載企業の編集',
            'href' => '/manage/secure/client/update',
            'valid_chk' => '1',
            'sort' => '3',
            'corp_available' => '1',
            'client_available' => '0',
            'icon_key' => 'edit',
        ]);

    }


    public function safeDown()
    {
        // 応募テーブル
        $this->dropTable('application_master');

        $sql = <<<SQL
CREATE TABLE application_master
(
    application_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    job_id INT NOT NULL,
    member_id INT,
    name_sei LONGTEXT,
    name_mei LONGTEXT,
    kana_sei LONGTEXT,
    kana_mei LONGTEXT,
    sex LONGTEXT,
    birth_date DATE,
    pref_cd SMALLINT,
    address LONGTEXT,
    tel_no LONGTEXT,
    mail_address_flg SMALLINT,
    mail_address LONGTEXT,
    occupation_cd INT,
    self_pr LONGTEXT,
    regist_datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
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
    status INT DEFAULT 0,
    carrier_type SMALLINT DEFAULT 0 NOT NULL,
    application_memo LONGTEXT,
    oiwai_status SMALLINT DEFAULT 0,
    oiwai_pass LONGTEXT,
    oiwai_price INT DEFAULT 0,
    disp_price INT DEFAULT 0,
    admit_date DATE,
    application_file LONGTEXT NOT NULL,
    application_file_disp LONGTEXT NOT NULL,
    admit_status SMALLINT DEFAULT 0,
    first_admit_date DATE
);
CREATE UNIQUE INDEX application_master_PKI ON application_master (application_id);
CREATE INDEX application_master_job_id_idx ON application_master (job_id);
CREATE INDEX application_master_member_id_idx ON application_master (member_id);
SQL;
        $this->execute($sql);


        // 応募者採用状況テーブル
        $this->dropTable('application_status_cd');

        $sql = <<<SQL
CREATE TABLE application_status_cd
(
    application_status_cd INT PRIMARY KEY NOT NULL,
    application_status LONGTEXT,
    valid_chk SMALLINT DEFAULT 1,
    del_chk SMALLINT DEFAULT 0
);
CREATE UNIQUE INDEX application_status_cd_PKI ON application_status_cd (application_status_cd);
SQL;
        $this->execute($sql);


        // 掲載タイプテーブル
        $this->dropTable('disp_type_cd');

        $sql = <<<SQL
CREATE TABLE disp_type_cd
(
    disp_type_cd SMALLINT PRIMARY KEY NOT NULL,
    disp_type_name VARCHAR(255) DEFAULT '' NOT NULL,
    sort_no SMALLINT DEFAULT 0 NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    del_chk SMALLINT DEFAULT 0 NOT NULL
);
CREATE UNIQUE INDEX disp_type_cd_PKI ON disp_type_cd (disp_type_cd);

SQL;
        $this->execute($sql);


        // 市区町村テーブル
        $this->dropTable('dist_cd');

        $sql = <<<SQL
CREATE TABLE dist_cd
(
    pref_cd INT NOT NULL,
    dist_name VARCHAR(255) NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    dist_sub_cd INT DEFAULT 1 NOT NULL,
    sort INT DEFAULT 0 NOT NULL,
    dist_cd INT PRIMARY KEY NOT NULL
);
CREATE UNIQUE INDEX dist_cd_PKI ON dist_cd (dist_cd);
CREATE INDEX dist_cd_pref_cd_idx ON dist_cd (pref_cd);

SQL;
        $this->execute($sql);


        // 雇用形態コードテーブル
        $this->dropTable('employment_type_cd');

        $sql = <<<SQL
CREATE TABLE employment_type_cd
(
    employment_type_name VARCHAR(255) NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort INT DEFAULT 0 NOT NULL,
    employment_type_cd INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    merubaito_employment_type_cd INT
);
CREATE UNIQUE INDEX employment_type_cd_PKI ON employment_type_cd (employment_type_cd);

SQL;
        $this->execute($sql);


        // 職種小テーブル
        $this->dropTable('job_type_small');

        $sql = <<<SQL
CREATE TABLE job_type_small
(
    job_id INT NOT NULL,
    job_type_small_cd INT NOT NULL,
    PRIMARY KEY (job_id, job_type_small_cd)
);
CREATE UNIQUE INDEX job_type_small_PKI ON job_type_small (job_id, job_type_small_cd);

SQL;
        $this->execute($sql);


        // 職種小コードテーブル
        $this->dropTable('job_type_small_cd');

        $sql = <<<SQL
CREATE TABLE job_type_small_cd
(
    job_type_small_name VARCHAR(255) NOT NULL,
    job_type_big_cd INT NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort INT,
    job_type_small_cd INT PRIMARY KEY NOT NULL,
    merubaito_job_type_small_cd INT
);
CREATE UNIQUE INDEX job_type_small_cd_PKI ON job_type_small_cd (job_type_small_cd);

SQL;
        $this->execute($sql);


        // 属性テーブル
        $this->dropTable('occupation_cd');

        $sql = <<<SQL
CREATE TABLE occupation_cd
(
    occupation_name VARCHAR(255) DEFAULT '' NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort INT,
    occupation_cd INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    merubaito_occupation_cd INT
);
CREATE UNIQUE INDEX occupation_cd_PKI ON occupation_cd (occupation_cd);

SQL;
        $this->execute($sql);


        // 都道府県テーブル
        $this->dropTable('pref_cd');

        $sql = <<<SQL
CREATE TABLE pref_cd
(
    pref_cd INT PRIMARY KEY NOT NULL,
    pref_name VARCHAR(255) NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    area_cd INT,
    sort INT DEFAULT 0 NOT NULL
);
CREATE UNIQUE INDEX pref_cd_PKI ON pref_cd (pref_cd);
CREATE INDEX pref_cd_area_cd_idx ON pref_cd (area_cd);

SQL;
        $this->execute($sql);


        // 管理者メニューテーブル
        $this->update('manage_menu_main', ['href' => '/manage/secure/job/job_list/','title' => '求人情報一覧･編集'], 'href="/manage/secure/job/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/corp/corp_list/','title' => '代理店情報一覧･編集'], 'href="/manage/secure/corp/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/client/client_list/','title' => '掲載企業情報一覧･編集'], 'href="/manage/secure/client/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/admin/admin_list/','title' => '管理者情報一覧･編集'], 'href="/manage/secure/admin/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/application/application_list/'], 'href="/manage/secure/application/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_job/job_list/','title' => '求人情報項目一覧・編集'], 'href="/manage/secure/option_job/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_corp/corp_list/','title' => '代理店項目一覧・編集'], 'href="/manage/secure/option_corp/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_client/client_list/','title' => '掲載企業項目一覧・編集'], 'href="/manage/secure/option_client/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_admin/admin_list/','title' => '管理者情報項目一覧・編集'], 'href="/manage/secure/option_admin/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_application/application_list/','title' => '応募者項目一覧・編集'], 'href="/manage/secure/option_application/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_member/member_list/','title' => '登録者情報項目一覧・編集'], 'href="/manage/secure/option_member/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/jobtype/jobtype_list/'], 'href="/manage/secure/jobtype/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/area/area_list/'], 'href="/manage/secure/area/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/pref/pref_list/'], 'href="/manage/secure/pref/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/dist/dist_list/'], 'href="/manage/secure/dist/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/prefdist/prefdist_list/'], 'href="/manage/secure/prefdist/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/route/route_list/'], 'href="/manage/secure/route/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/station/station_list/'], 'href="/manage/secure/station/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/employment/employment_list/'], 'href="/manage/secure/employment/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/occupation/occupation_list/'], 'href="/manage/secure/occupation/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/worktime/worktime_list/'], 'href="/manage/secure/worktime/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/wage/wage_list/'], 'href="/manage/secure/wage/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_disptype/disptype_list/'], 'href="/manage/secure/option_disptype/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/merit/merit_list/'], 'href="/manage/secure/merit/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_scout/scout_list/'], 'href="/manage/secure/option_scout/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/media_upload/upload_list/'], 'href="/manage/secure/media_upload/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/admin/admin_regist/'], 'href="/manage/secure/admin/create"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/profile/profile_regist/'], 'href="/manage/secure/profile/create"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/media_upload/upload_regist/'], 'href="/manage/secure/media_upload/create"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/option_search/option_search_list/'], 'href="/manage/secure/option_search/list"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/corp/corp_regist/'], 'href="/manage/secure/corp/create"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/client/client_regist/'], 'href="/manage/secure/client/create"');
        $this->update('manage_menu_main', ['href' => '/manage/secure/job/job_regist/'], 'href="/manage/secure/job/create"');

        $this->delete('manage_menu_main', 'manage_menu_main_id=80');
        $this->delete('manage_menu_main', 'manage_menu_main_id=81');
        $this->delete('manage_menu_main', 'manage_menu_main_id=82');

    }
}
