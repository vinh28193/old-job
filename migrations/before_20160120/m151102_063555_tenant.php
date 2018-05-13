<?php

use yii\db\Schema;
use yii\db\Migration;

class m151102_063555_tenant extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // エリアテーブル
        $this->dropTable('area_cd');

        $this->createTable('area_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'area_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "エリア名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'area_tab_name' => Schema::TYPE_STRING . ' COMMENT "エリアタブ名"',
            'sort' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "表示順"',
            'area_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "エリアコード"',
            'area_dir' => Schema::TYPE_STRING . ' COMMENT "エリアURL名"',
        ], $tableOptions. ' COMMENT="エリア"');


        // 仕事-市区町村関連テーブル
        $this->dropTable('job_dist');

        $this->createTable('job_dist', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'dist_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルdist_cdのカラムid"',
        ], $tableOptions. ' COMMENT="仕事-市区町村関連"');

        $this->addPrimaryKey('pk_job_dist', 'job_dist', ['id', 'tenant_id']);
        $this->alterColumn('job_dist', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_dist PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事-雇用形態関連テーブル
        $this->dropTable('job_employment_type');

        $this->createTable('job_employment_type', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'employment_type_cd_id' => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL COMMENT "テーブルemployment_type_cdのカラムid"',
        ], $tableOptions. ' COMMENT="仕事-雇用形態関連"');

        $this->addPrimaryKey('pk_job_employment_type', 'job_employment_type', ['id', 'tenant_id']);
        $this->alterColumn('job_employment_type', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_employment_type PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事テーブル
        $this->dropTable('job_master');

        $this->createTable('job_master', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "仕事ナンバー"',
            'disp_type_cd' => 'TINYINT NOT NULL COMMENT "掲載タイプ"',
            'client_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルclient_masterのカラムid"',
            'corp_name_disp' => Schema::TYPE_STRING . ' COMMENT "会社名"',
            'job_pr' => Schema::TYPE_TEXT . ' COMMENT "メインキャッチ"',
            'main_copy' => Schema::TYPE_TEXT . ' COMMENT "コメント"',
            'job_comment' => Schema::TYPE_TEXT . ' COMMENT "PR"',
            'job_pict_0' => Schema::TYPE_STRING . ' COMMENT "画像０（Aタイプ）"',
            'job_pict_1' => Schema::TYPE_STRING . ' COMMENT "画像１（Bタイプ）"',
            'job_pict_2' => Schema::TYPE_STRING . ' COMMENT "画像２（Cタイプ）"',
            'job_pict_3' => Schema::TYPE_STRING . ' COMMENT "画像３（Cタイプ）"',
            'job_type_text' => Schema::TYPE_TEXT . ' COMMENT "職種（テキスト）"',
            'work_place' => Schema::TYPE_TEXT . ' COMMENT "勤務地（テキスト）"',
            'station' => Schema::TYPE_TEXT . ' COMMENT "最寄り駅"',
            'transport' => Schema::TYPE_TEXT . ' COMMENT "交通"',
            'wage_text' => Schema::TYPE_TEXT . ' COMMENT "給与"',
            'requirement' => Schema::TYPE_TEXT . ' COMMENT "応募資格"',
            'conditions' => Schema::TYPE_TEXT . ' COMMENT "待遇"',
            'holidays' => Schema::TYPE_TEXT . ' COMMENT "休日・休暇"',
            'work_period' => Schema::TYPE_TEXT . ' COMMENT "就労期間"',
            'work_time_text' => Schema::TYPE_TEXT . ' COMMENT "勤務期間（テキスト）"',
            'application' => Schema::TYPE_TEXT . ' COMMENT "応募方法"',
            'application_tel_1' => Schema::TYPE_STRING . ' COMMENT "連絡先電話番号１"',
            'application_tel_2' => Schema::TYPE_STRING . ' COMMENT "連絡先電話番号２"',
            'application_mail' => Schema::TYPE_STRING . ' COMMENT "応募先メールアドレス"',
            'application_place' => Schema::TYPE_TEXT . ' COMMENT "面接地"',
            'application_staff_name' => Schema::TYPE_STRING . ' COMMENT "受付担当者"',
            'agent_name' => Schema::TYPE_STRING . ' COMMENT "営業担当者"',
            'disp_start_date' => Schema::TYPE_DATE . ' DEFAULT "0000-00-00" COMMENT "掲載開始日"',
            'disp_end_date' => Schema::TYPE_DATE . ' DEFAULT "0000-00-00" COMMENT "掲載終了日"',
            'regist_datetime' => Schema::TYPE_TIMESTAMP . ' DEFAULT "0000-00-00 00:00:00" COMMENT "登録日時"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'job_search_number' => Schema::TYPE_STRING . ' COMMENT "お仕事No"',
            'job_pict_text_2' => Schema::TYPE_TEXT . ' COMMENT "画像２（キャプション）"',
            'job_pict_text_3' => Schema::TYPE_TEXT . ' COMMENT "画像３（キャプション）"',
            'map_url' => Schema::TYPE_TEXT . ' COMMENT "MAPをみる-URL"',
            'mail_body' => Schema::TYPE_TEXT . ' COMMENT "通知メール文面"',
            'update_time' => Schema::TYPE_TIMESTAMP . ' NOT NULL COMMENT "更新日時"',
            'job_pict_text_4' => Schema::TYPE_TEXT . ' COMMENT "画像４（キャプション）"',
            'job_pict_4' => Schema::TYPE_STRING . ' COMMENT "画像４（Cタイプ）"',
            'main_copy2' => Schema::TYPE_TEXT . ' COMMENT "コメント2"',
            'job_pr2' => Schema::TYPE_TEXT . ' COMMENT "メインキャッチ2"',
            'option100' => Schema::TYPE_TEXT . ' COMMENT "オプション100"',
            'option101' => Schema::TYPE_TEXT . ' COMMENT "オプション101"',
            'option102' => Schema::TYPE_TEXT . ' COMMENT "オプション102"',
            'option103' => Schema::TYPE_TEXT . ' COMMENT "オプション103"',
            'option104' => Schema::TYPE_TEXT . ' COMMENT "オプション104"',
            'option105' => Schema::TYPE_TEXT . ' COMMENT "オプション105"',
            'option106' => Schema::TYPE_TEXT . ' COMMENT "オプション106"',
            'option107' => Schema::TYPE_TEXT . ' COMMENT "オプション107"',
            'option108' => Schema::TYPE_TEXT . ' COMMENT "オプション108"',
            'option109' => Schema::TYPE_TEXT . ' COMMENT "オプション109"',
            'import_site_job_id' => Schema::TYPE_INTEGER . ' COMMENT "インポートサイト仕事ID"',
            'client_charge_plan_id' => Schema::TYPE_SMALLINT . ' NOT NULL COMMENT "テーブルclient_charge_planのカラムid"',
            'medium_application_pc_url' => Schema::TYPE_TEXT . ' COMMENT "応募媒体URL（PC)"',
            'medium_application_sm_url' => Schema::TYPE_TEXT . ' COMMENT "応募媒体URL（スマホ)"',
            'manager_memo' => Schema::TYPE_TEXT . ' COMMENT "管理者用備考欄"',
            'review_flg' => 'TINYINT DEFAULT 3 COMMENT "審査フラグ(0=改変、1=審査依頼中、2=審査NG、3=審査OK)"',
            'sample_pict_flg_1' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "サンプル画像フラグ1"',
            'sample_pict_flg_2' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "サンプル画像フラグ2"',
            'sample_pict_flg_3' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "サンプル画像フラグ3"',
            'sample_pict_flg_4' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "サンプル画像フラグ4"',
            'sample_pict_flg_5' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "サンプル画像フラグ5"',
        ], $tableOptions. ' COMMENT="仕事"');

        $this->addPrimaryKey('pk_job_master', 'job_master', ['id', 'tenant_id']);
        $this->alterColumn('job_master', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_master PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事-メリット関連テーブル
        $this->dropTable('job_merit');

        $this->createTable('job_merit', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'merit_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルmerit_cdのカラムid"',
        ], $tableOptions. ' COMMENT="仕事-メリット関連"');

        $this->addPrimaryKey('pk_job_merit', 'job_merit', ['id', 'tenant_id']);
        $this->alterColumn('job_merit', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_merit PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事-属性関連テーブル
        $this->dropTable('job_occupation');

        $this->createTable('job_occupation', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'occupation_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルoccupation_cdのカラムid"',
        ], $tableOptions. ' COMMENT="仕事-属性関連"');

        $this->addPrimaryKey('pk_job_occupation', 'job_occupation', ['id', 'tenant_id']);
        $this->alterColumn('job_occupation', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_occupation PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事-お祝い金関連テーブル
        $this->dropTable('job_oiwai_price');

        $this->createTable('job_oiwai_price', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'oiwai_price' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "お祝い金額"',
        ], $tableOptions. ' COMMENT="仕事-お祝い金関連"');

        $this->addPrimaryKey('pk_job_oiwai_price', 'job_oiwai_price', ['id', 'tenant_id']);
        $this->alterColumn('job_oiwai_price', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_oiwai_price PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事-オプション検索キー関連テーブル
        $this->dropTable('job_option_search');

        $this->createTable('job_option_search', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'option_search_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルoption_search_cdのカラムid"',
        ], $tableOptions. ' COMMENT="仕事-オプション検索キー関連"');

        $this->addPrimaryKey('pk_job_option_search', 'job_option_search', ['id', 'tenant_id']);
        $this->alterColumn('job_option_search', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_option_search PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事-都道府県関連テーブル
        $this->dropTable('job_pref');

        $this->createTable('job_pref', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'pref_cd' => Schema::TYPE_SMALLINT . ' NOT NULL COMMENT "都道府県コード"',
        ], $tableOptions. ' COMMENT="仕事-都道府県関連"');

        $this->addPrimaryKey('pk_job_pref', 'job_pref', ['id', 'tenant_id']);
        $this->alterColumn('job_pref', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_pref PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

        // 仕事-駅関連テーブル
        $this->dropTable('job_station_info');

        $this->createTable('job_station_info', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'station_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルstation_cdのカラムstation_cd"',
            'transport_type' => 'TINYINT NOT NULL COMMENT "交通手段(0=徒歩、1=バス)"',
            'transport_time' => Schema::TYPE_INTEGER . ' DEFAULT 1 NOT NULL COMMENT "駅からの所要時間"',
        ], $tableOptions. ' COMMENT="仕事-駅関連"');

        $this->addPrimaryKey('pk_job_station_info', 'job_station_info', ['id', 'tenant_id']);
        $this->alterColumn('job_station_info', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_station_info PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 職種大コードテーブル
        $this->dropTable('job_type_big_cd');

        $this->createTable('job_type_big_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_type_big_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "職種大名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "表示順"',
            'job_type_category_id' => Schema::TYPE_INTEGER . ' COMMENT "テーブルjob_type_categoryのカラムid"',
            'job_type_big_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "職種大コード"',
        ], $tableOptions. ' COMMENT="職種大コード"');


        // 職種カテゴリテーブル
        $this->dropTable('job_type_category');

        $this->createTable('job_type_category', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_type_category_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "職種カテゴリコード"',
            'name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "職種カテゴリ名"',
            'sort' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "表示順"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 NOT NULL COMMENT "状態"',
        ], $tableOptions. ' COMMENT="職種カテゴリ"');


        // 申込みプランテーブル
        $this->renameColumn('client_charge_plan', 'client_charge_plan_id', 'client_charge_plan_no');

    }


    public function safeDown()
    {
        // エリアテーブル
        $this->dropTable('area_cd');

        $sql = <<<SQL
CREATE TABLE area_cd
(
    area_name VARCHAR(255) DEFAULT '' NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    area_tab_name VARCHAR(255),
    sort INT DEFAULT 0 NOT NULL,
    area_cd INT PRIMARY KEY NOT NULL,
    area_dir LONGTEXT
);
CREATE UNIQUE INDEX area_cd_PKI ON area_cd (area_cd);
SQL;
        $this->execute($sql);


        // 仕事-市区町村関連テーブル
        $this->dropTable('job_dist');

        $sql = <<<SQL
CREATE TABLE job_dist
(
    job_id INT NOT NULL,
    dist_cd INT NOT NULL,
    PRIMARY KEY (job_id, dist_cd)
);
CREATE UNIQUE INDEX job_dist_PKI ON job_dist (job_id, dist_cd);
CREATE INDEX job_dist_job_id ON job_dist (job_id);
SQL;
        $this->execute($sql);


        // 仕事-雇用形態関連テーブル
        $this->dropTable('job_employment_type');

        $sql = <<<SQL
CREATE TABLE job_employment_type
(
    job_id INT NOT NULL,
    employment_type_cd INT NOT NULL,
    PRIMARY KEY (job_id, employment_type_cd)
);
CREATE UNIQUE INDEX job_employment_type_PKI ON job_employment_type (job_id, employment_type_cd);
SQL;
        $this->execute($sql);


        // 仕事テーブル
        $this->dropTable('job_master');

        $sql = <<<SQL
CREATE TABLE job_master
(
    job_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    disp_type_cd SMALLINT NOT NULL,
    client_id INT NOT NULL,
    corp_name_disp VARCHAR(255) DEFAULT '',
    job_pr LONGTEXT,
    main_copy LONGTEXT,
    job_comment LONGTEXT,
    job_pict_0 LONGTEXT,
    job_pict_1 LONGTEXT,
    job_pict_2 LONGTEXT,
    job_pict_3 LONGTEXT,
    job_type_text LONGTEXT,
    work_place LONGTEXT,
    station LONGTEXT,
    transport LONGTEXT,
    wage_text LONGTEXT,
    requirement LONGTEXT,
    conditions LONGTEXT,
    holidays LONGTEXT,
    work_period LONGTEXT,
    work_time_text LONGTEXT,
    application LONGTEXT,
    application_tel_1 LONGTEXT,
    application_tel_2 LONGTEXT,
    application_tel_3 LONGTEXT,
    application_mail LONGTEXT,
    application_place LONGTEXT,
    application_staff_name LONGTEXT,
    agent_name LONGTEXT,
    disp_start_date DATE DEFAULT '0000-00-00',
    disp_end_date DATE DEFAULT '0000-00-00',
    regist_datetime TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    del_chk SMALLINT DEFAULT 0 NOT NULL,
    job_search_number LONGTEXT,
    ranking_chk SMALLINT DEFAULT 1 NOT NULL,
    job_test_process LONGTEXT,
    job_pict_text_1 LONGTEXT,
    job_pict_text_2 LONGTEXT,
    job_pict_text_3 LONGTEXT,
    hot_chk SMALLINT DEFAULT 0 NOT NULL,
    agent_mail LONGTEXT,
    map_url LONGTEXT,
    info_mail_chk SMALLINT DEFAULT 0 NOT NULL,
    corp_mail LONGTEXT,
    mail_body LONGTEXT,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    job_pict_text_4 LONGTEXT,
    job_pict_4 LONGTEXT,
    main_copy2 LONGTEXT,
    job_pr2 LONGTEXT,
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
    job_pict_text_0 LONGTEXT,
    icon_mb_gif LONGTEXT,
    icon_mb_png LONGTEXT,
    icon_mb_jpg LONGTEXT,
    map_url_mb LONGTEXT,
    import_site_job_id INT,
    client_charge_type LONGTEXT,
    client_charge_plan_id INT DEFAULT 0,
    medium_application_pc_url LONGTEXT,
    medium_application_mb_url LONGTEXT,
    medium_application_sm_url LONGTEXT,
    manager_memo LONGTEXT,
    review_flg TINYINT DEFAULT 3,
    sample_pict_flg_1 TINYINT DEFAULT 0,
    sample_pict_flg_2 TINYINT DEFAULT 0,
    sample_pict_flg_3 TINYINT DEFAULT 0,
    sample_pict_flg_4 TINYINT DEFAULT 0,
    sample_pict_flg_5 TINYINT DEFAULT 0
);
CREATE UNIQUE INDEX job_master_PKI ON job_master (job_id);
CREATE INDEX client_id_index ON job_master (client_id);
CREATE INDEX job_master_disp_end_date_idx ON job_master (disp_end_date);
CREATE INDEX job_master_disp_start_date_idx ON job_master (disp_start_date);
SQL;
        $this->execute($sql);


        // 仕事-メリット関連テーブル
        $this->dropTable('job_merit');

        $sql = <<<SQL
CREATE TABLE job_merit
(
    job_id INT NOT NULL,
    merit_cd INT NOT NULL,
    PRIMARY KEY (job_id, merit_cd)
);
CREATE UNIQUE INDEX job_merit_PKI ON job_merit (job_id, merit_cd);
CREATE INDEX job_merit_idx ON job_merit (merit_cd, job_id);
SQL;
        $this->execute($sql);


        // 仕事-属性関連テーブル
        $this->dropTable('job_occupation');

        $sql = <<<SQL
CREATE TABLE job_occupation
(
    job_occupation_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    job_id INT NOT NULL,
    occupation_cd INT NOT NULL,
    merubaito_occupation_cd INT
);
CREATE UNIQUE INDEX job_occupation_PKI ON job_occupation (job_occupation_id);
SQL;
        $this->execute($sql);


        // 仕事-お祝い金関連テーブル
        $this->dropTable('job_oiwai_price');

        $sql = <<<SQL
CREATE TABLE job_oiwai_price
(
    job_id INT NOT NULL,
    oiwai_price INT NOT NULL,
    PRIMARY KEY (job_id, oiwai_price)
);
CREATE UNIQUE INDEX job_oiwai_price_PKI ON job_oiwai_price (job_id, oiwai_price);
SQL;
        $this->execute($sql);


        // 仕事-オプション検索キー関連テーブル
        $this->dropTable('job_option_search');

        $sql = <<<SQL
CREATE TABLE job_option_search
(
    job_id INT NOT NULL,
    option_search_cd INT NOT NULL,
    PRIMARY KEY (job_id, option_search_cd)
);
CREATE UNIQUE INDEX job_option_search_PKI ON job_option_search (job_id, option_search_cd);
CREATE INDEX job_option_search_job_id ON job_option_search (job_id);
SQL;
        $this->execute($sql);


        // 仕事-都道府県関連テーブル
        $this->dropTable('job_pref');

        $sql = <<<SQL
CREATE TABLE job_pref
(
    job_id INT NOT NULL,
    pref_cd INT NOT NULL,
    PRIMARY KEY (job_id, pref_cd)
);
CREATE INDEX `_job_id_idx` ON job_pref (job_id);
CREATE INDEX job_pref_job_id_pref_cd_idx ON job_pref (job_id, pref_cd);
CREATE INDEX job_pref_pref_cd ON job_pref (pref_cd);
SQL;
        $this->execute($sql);


        // 仕事-駅関連テーブル
        $this->dropTable('job_station_info');

        $sql = <<<SQL
CREATE TABLE job_station_info
(
    job_station_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    job_id INT NOT NULL,
    station_cd INT NOT NULL,
    transport_type TINYINT NOT NULL,
    transport_time INT DEFAULT 1 NOT NULL
);
CREATE UNIQUE INDEX job_station_info_PKI ON job_station_info (job_station_id);
SQL;
        $this->execute($sql);


        // 職種大コードテーブル
        $this->dropTable('job_type_big_cd');

        $sql = <<<SQL
CREATE TABLE job_type_big_cd
(
    job_type_big_name VARCHAR(255) NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort INT DEFAULT 0,
    job_type_category_id INT,
    job_type_big_cd INT PRIMARY KEY NOT NULL
);
CREATE UNIQUE INDEX job_type_big_cd_PKI ON job_type_big_cd (job_type_big_cd);
SQL;
        $this->execute($sql);


        // 職種カテゴリテーブル
        $this->dropTable('job_type_category');

        $sql = <<<SQL
CREATE TABLE job_type_category
(
    job_type_category_id INT PRIMARY KEY NOT NULL,
    name LONGTEXT NOT NULL,
    sort INT NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL
);
CREATE UNIQUE INDEX job_type_category_PKI ON job_type_category (job_type_category_id);
SQL;
        $this->execute($sql);


        // 申込みプランテーブル
        $this->renameColumn('client_charge_plan', 'client_charge_plan_no', 'client_charge_plan_id');

    }
}
