<?php

use yii\db\Schema;
use yii\db\Migration;

class m151106_022808_tenant extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }


        // 仕事-給与関連テーブル
        $this->dropTable('job_wage');

        $this->createTable('job_wage', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'wage_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwage_masterのカラムid"',
        ], $tableOptions. ' COMMENT="仕事-給与関連"');

        $this->addPrimaryKey('pk_job_wage', 'job_wage', ['id', 'tenant_id']);
        $this->alterColumn('job_wage', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_wage PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事-希望の勤務日数関連テーブル
        $this->dropTable('job_work_date');

        $this->createTable('job_work_date', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'work_date_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwork_date_cdのカラムid"',
        ], $tableOptions. ' COMMENT="仕事-希望の勤務日数関連"');

        $this->addPrimaryKey('pk_job_work_date', 'job_work_date', ['id', 'tenant_id']);
        $this->alterColumn('job_work_date', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_work_date PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事-希望の勤務時間関連テーブル
        $this->dropTable('job_work_hour');

        $this->createTable('job_work_hour', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'work_hour_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwork_hour_cdのカラムid"',
        ], $tableOptions. ' COMMENT="仕事-希望の勤務時間関連"');

        $this->addPrimaryKey('pk_job_work_hour', 'job_work_hour', ['id', 'tenant_id']);
        $this->alterColumn('job_work_hour', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_work_hour PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事-希望の勤務期間関連テーブル
        $this->dropTable('job_work_term');

        $this->createTable('job_work_term', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'work_term_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwork_term_cdのカラムid"',
        ], $tableOptions. ' COMMENT="仕事-希望の勤務期間関連"');

        $this->addPrimaryKey('pk_job_work_term', 'job_work_term', ['id', 'tenant_id']);
        $this->alterColumn('job_work_term', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_work_term PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // 仕事-希望の勤務時間帯関連テーブル
        $this->dropTable('job_work_time');

        $this->createTable('job_work_time', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'work_time_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwork_time_cdのカラムid"',
        ], $tableOptions. ' COMMENT="仕事-希望の勤務時間帯関連"');

        $this->addPrimaryKey('pk_job_work_time', 'job_work_time', ['id', 'tenant_id']);
        $this->alterColumn('job_work_time', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_work_time PARTITION BY HASH (tenant_id) PARTITIONS 2000;');


        // メリットカテゴリテーブル
        $this->dropTable('merit_category_cd');

        $this->createTable('merit_category_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'merit_category_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "メリットカテゴリコード"',
            'merit_category_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "メリットカテゴリ名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'del_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "削除フラグ"',
            'sort' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "表示順"',
            'merit_display_type' => 'TINYINT DEFAULT 0 COMMENT "メリット表示タイプ(0=チェックボックス 1=ラジオボタン)"',
            'merit_search_type' => 'TINYINT DEFAULT 0 COMMENT "メリット検索タイプ(0=AND検索 1=OR検索)"',
        ], $tableOptions. ' COMMENT="メリットカテゴリ"');


        // メリットテーブル
        $this->dropTable('merit_cd');

        $this->createTable('merit_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'merit_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "メリットコード"',
            'merit_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "メリット名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "表示順"',
            'merit_category_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルmerit_category_cdのカラムid"',
        ], $tableOptions. ' COMMENT="メリット"');


        // オプション検索キーカテゴリテーブル
        $this->dropTable('option_search_category_cd');

        $this->createTable('option_search_category_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'option_search_category_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "オプション検索キーカテゴリコード"',
            'option_display_type' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "表示タイプ(0=チェックボックス 1=ラジオボタン)"',
            'option_search_type' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "検索タイプ(0=AND検索 1=OR検索)"',
        ], $tableOptions. ' COMMENT="オプション検索キーカテゴリ"');


        // オプション検索キーテーブル
        $this->dropTable('option_search_cd');

        $this->createTable('option_search_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'option_search_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "オプション検索キーコード"',
            'option_search_category_cd_id' => Schema::TYPE_INTEGER . ' COMMENT "テーブルoption_search_category_cdカラムid"',
            'option_search_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "オプション検索キー名"',
            'sort_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "表示順"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
        ], $tableOptions. ' COMMENT="オプション検索キー"');


        // 詳細順番-掲載タイプテーブル
        $this->dropTable('option_sort_disp');

        $this->createTable('option_sort_disp', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'option_sort_disp_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "詳細順番-掲載タイプナンバー"',
            'function_item_set_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルfunction_item_setのカラムid"',
            'sort_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "表示順"',
            'disp_type_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "掲載タイプ"',
        ], $tableOptions. ' COMMENT="詳細順番-掲載タイプ"');


        // 都道府県-市区町村関連テーブル
        $this->dropTable('pref_dist');

        $this->createTable('pref_dist', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'pref_dist_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルpref_dist_masterのカラムid"',
            'dist_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルdist_cdのカラムid"',
        ], $tableOptions. ' COMMENT="都道府県-市区町村関連"');


        // 地域テーブル
        $this->dropTable('pref_dist_master');

        $this->createTable('pref_dist_master', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'pref_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルpref_cdのカラムid"',
            'pref_dist_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "地域名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
        ], $tableOptions. ' COMMENT="地域"');


        // サンプル画像テーブル
        $this->dropTable('sample_pict');

        $this->createTable('sample_pict', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'file_name' => Schema::TYPE_STRING . ' COMMENT "ファイル名"',
            'update_time' => Schema::TYPE_DATETIME . ' NOT NULL COMMENT "更新日時"',
            'admin_master_id' => Schema::TYPE_INTEGER . ' COMMENT "テーブルadmin_masterのカラムid"',
        ], $tableOptions. ' COMMENT="サンプル画像"');
    }


    public function down()
    {
        // 仕事-給与関連テーブル
        $this->dropTable('job_wage');

        $sql = <<<SQL
CREATE TABLE job_wage
(
    job_id INT NOT NULL,
    wage_master_id INT NOT NULL,
    PRIMARY KEY (job_id, wage_master_id)
);
CREATE UNIQUE INDEX job_wage_PKI ON job_wage (job_id, wage_master_id);
SQL;
        $this->execute($sql);


        // 仕事-希望の勤務日数関連テーブル
        $this->dropTable('job_work_date');

        $sql = <<<SQL
CREATE TABLE job_work_date
(
    job_id INT NOT NULL,
    work_date_cd INT NOT NULL,
    PRIMARY KEY (work_date_cd, job_id)
);
CREATE UNIQUE INDEX job_work_date_PKI ON job_work_date (work_date_cd, job_id);
SQL;
        $this->execute($sql);


        // 仕事-希望の勤務時間関連テーブル
        $this->dropTable('job_work_hour');

        $sql = <<<SQL
CREATE TABLE job_work_hour
(
    job_id INT NOT NULL,
    work_hour_cd INT NOT NULL,
    PRIMARY KEY (work_hour_cd, job_id)
);
CREATE UNIQUE INDEX job_work_hour_PKI ON job_work_hour (work_hour_cd, job_id);
SQL;
        $this->execute($sql);


        // 仕事-希望の勤務期間関連テーブル
        $this->dropTable('job_work_term');

        $sql = <<<SQL
CREATE TABLE job_work_term
(
    job_id INT NOT NULL,
    work_term_cd INT NOT NULL,
    PRIMARY KEY (work_term_cd, job_id)
);
CREATE UNIQUE INDEX job_work_term_PKI ON job_work_term (work_term_cd, job_id);
SQL;
        $this->execute($sql);


        // 仕事-希望の勤務時間帯関連テーブル
        $this->dropTable('job_work_time');

        $sql = <<<SQL
CREATE TABLE job_work_time
(
    job_id INT NOT NULL,
    work_time_cd INT NOT NULL,
    merubaito_work_time_cd INT,
    PRIMARY KEY (work_time_cd, job_id)
);
CREATE UNIQUE INDEX job_work_time_PKI ON job_work_time (work_time_cd, job_id);
SQL;
        $this->execute($sql);


        // メリットカテゴリテーブル
        $this->dropTable('merit_category_cd');

        $sql = <<<SQL
CREATE TABLE merit_category_cd
(
    merit_category_cd INT PRIMARY KEY NOT NULL,
    merit_category_name VARCHAR(255) DEFAULT '' NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    del_chk SMALLINT DEFAULT 0 NOT NULL,
    sort INT DEFAULT 0 NOT NULL,
    merit_display_type INT DEFAULT 0 NOT NULL,
    merit_search_type SMALLINT DEFAULT 0 NOT NULL
);
CREATE UNIQUE INDEX merit_category_cd_PKI ON merit_category_cd (merit_category_cd);
SQL;
        $this->execute($sql);


        // メリットテーブル
        $this->dropTable('merit_cd');

        $sql = <<<SQL
CREATE TABLE merit_cd
(
    merit_cd INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    merit_name VARCHAR(255) DEFAULT '' NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort INT DEFAULT 0 NOT NULL,
    merit_category_cd INT,
    merubaito_merit_cd INT
);
CREATE UNIQUE INDEX merit_cd_PKI ON merit_cd (merit_cd);
SQL;
        $this->execute($sql);


        // オプションカテゴリ検索キーテーブル
        $this->dropTable('option_search_category_cd');

        $sql = <<<SQL
CREATE TABLE option_search_category_cd
(
    option_search_category_cd INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    option_display_type INT DEFAULT 0 NOT NULL,
    option_search_type INT DEFAULT 0 NOT NULL
);
CREATE UNIQUE INDEX option_search_category_cd_PKI ON option_search_category_cd (option_search_category_cd);
SQL;
        $this->execute($sql);


        // オプション検索キーテーブル
        $this->dropTable('option_search_cd');

        $sql = <<<SQL
CREATE TABLE option_search_cd
(
    option_search_cd BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    option_search_category_cd INT,
    option_search_name VARCHAR(200) NOT NULL,
    sort_no INT DEFAULT 0 NOT NULL,
    valid_chk TINYINT DEFAULT 1 NOT NULL
);
CREATE UNIQUE INDEX option_search_cd_PKI ON option_search_cd (option_search_cd);
SQL;
        $this->execute($sql);


        // 詳細順番-掲載タイプテーブル
        $this->dropTable('option_sort_disp');

        $sql = <<<SQL
CREATE TABLE option_sort_disp
(
    option_sort_disp_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    function_item_id INT NOT NULL,
    sort_no INT NOT NULL,
    disp_type_cd INT NOT NULL
);
CREATE UNIQUE INDEX option_sort_disp_PKI ON option_sort_disp (option_sort_disp_id);
CREATE INDEX option_sort_disp_id ON option_sort_disp (option_sort_disp_id);
SQL;
        $this->execute($sql);


        // 都道府県-市区町村関連テーブル
        $this->dropTable('pref_dist');

        $sql = <<<SQL
CREATE TABLE pref_dist
(
    pref_dist_master_id INT NOT NULL,
    dist_cd INT NOT NULL,
    PRIMARY KEY (pref_dist_master_id, dist_cd)
);
CREATE UNIQUE INDEX pref_dist_PKI ON pref_dist (pref_dist_master_id, dist_cd);
SQL;
        $this->execute($sql);


        // 地域テーブル
        $this->dropTable('pref_dist_master');

        $sql = <<<SQL
CREATE TABLE pref_dist_master
(
    pref_cd INT NOT NULL,
    pref_dist_name LONGTEXT NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    pref_dist_master_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT
);
CREATE UNIQUE INDEX pref_dist_master_PKI ON pref_dist_master (pref_dist_master_id);
CREATE INDEX pref_dist_master_pref_cd_idx ON pref_dist_master (pref_cd);
SQL;
        $this->execute($sql);


        // サンプル画像テーブル
        $this->dropTable('sample_pict');

        $sql = <<<SQL
CREATE TABLE sample_pict
(
    sample_pict_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    file_name VARCHAR(200),
    update_time DATETIME NOT NULL,
    admin_id INT
);
CREATE UNIQUE INDEX stock_pict_PKI ON sample_pict (sample_pict_id);
SQL;
        $this->execute($sql);
    }
}
