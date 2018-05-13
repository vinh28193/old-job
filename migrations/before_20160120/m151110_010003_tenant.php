<?php

use yii\db\Schema;
use yii\db\Migration;

class m151110_010003_tenant extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // 学習テーブル
        $this->dropTable('study_cd');


        // 給与値テーブル
        $this->dropTable('wage_master');

        $this->createTable('wage_master', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'wage_type_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwage_type_masterのカラムid"',
            'wage_value_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwage_value_master_idのカラムid"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
        ], $tableOptions. ' COMMENT="給与値"');


        // 給与種別テーブル
        $this->dropTable('wage_type_master');

        $this->createTable('wage_type_master', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'wage_type_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "給与種別名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "表示順"',
        ], $tableOptions. ' COMMENT="給与種別"');



        // 給与金額テーブル
        $this->dropTable('wage_value_master');

        $this->createTable('wage_value_master', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'wage_value_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "給与金額名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "表示順"',
        ], $tableOptions. ' COMMENT="給与金額"');


        // 希望の勤務日数テーブル
        $this->dropTable('work_date_cd');

        $this->createTable('work_date_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'work_date_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "希望の勤務日数コード"',
            'work_date_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "希望の勤務日数名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_SMALLINT . ' COMMENT "表示順"',
            'work_group_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwork_group_cdのカラムid"',
        ], $tableOptions. ' COMMENT="希望の勤務日数"');


        // 勤務グループテーブル
        $this->dropTable('work_group_cd');

        $this->createTable('work_group_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'work_group_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "勤務グループコード"',
            'work_group_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "勤務グループ名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_SMALLINT . ' COMMENT "表示順"',
        ], $tableOptions. ' COMMENT="勤務グループ"');


        // 希望の勤務時間テーブル
        $this->dropTable('work_hour_cd');

        $this->createTable('work_hour_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'work_hour_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "勤務時間コード"',
            'work_hour_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "勤務時間名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_SMALLINT . ' COMMENT "表示順"',
            'work_group_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwork_group_cdのカラムid"',
        ], $tableOptions. ' COMMENT="希望の勤務時間"');


        // 希望の勤務期間テーブル
        $this->dropTable('work_term_cd');

        $this->createTable('work_term_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'work_term_cd' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "希望の勤務期間コード"',
            'work_term_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "希望の勤務期間名"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_SMALLINT . ' COMMENT "表示順"',
            'work_group_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwork_group_cdのカラムid"',
        ], $tableOptions. ' COMMENT="希望の勤務期間"');


        // 勤務時間テーブル
        $this->dropTable('work_time_cd');

        $this->createTable('work_time_cd', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'work_time_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "勤務時間名"',
            'del_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 0 COMMENT "削除フラグ"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
            'sort' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT "表示順"',
            'work_group_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwork_group_cdのカラムid"',
            'work_time_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwork_time_cdのカラムid"',
        ], $tableOptions. ' COMMENT="勤務時間"');


        // コンテンツ_原稿マッチングテーブル
        $this->dropTable('content_job');

        $this->createTable('content_job', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'content_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルcontent_masterのカラムid"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'zenkoku_flg' => Schema::TYPE_BOOLEAN . ' NOT NULL COMMENT "全国フラグ(0=全国TOPに表示させない, 1=全国TOPに表示させる)"',
        ], $tableOptions. ' COMMENT="コンテンツ_原稿マッチング"');


        // コンテンツ管理テーブル
        $this->dropTable('content_master');

        $this->createTable('content_master', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'content_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "コンテンツナンバー"',
            'content_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "コンテンツ名"',
            'content_type' => 'TINYINT NOT NULL COMMENT "コンテンツ種類(0=URL登録型, 1=原稿選択型, 2=動画タグ型)"',
            'widget_id' => Schema::TYPE_SMALLINT . ' NOT NULL COMMENT "テーブルwidgetのカラムid"',
            'title' => Schema::TYPE_STRING . ' COMMENT "タイトル"',
            'pict' => Schema::TYPE_STRING . ' COMMENT "画像"',
            'description' => Schema::TYPE_STRING . ' COMMENT "ディスクリプション"',
            'sort' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "表示順"',
            'disp_start_date' => Schema::TYPE_DATE . ' COMMENT "公開開始日"',
            'disp_end_date' => Schema::TYPE_DATE . ' COMMENT "公開終了日"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
        ], $tableOptions. ' COMMENT="コンテンツ管理"');


        // コンテンツ_動画タグマッチングテーブル
        $this->dropTable('content_movie');

        $this->createTable('content_movie', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'content_master_id' => Schema::TYPE_SMALLINT . ' NOT NULL COMMENT "テーブルcontent_masterのカラムid"',
            'area_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルarea_cdのカラムid(全国TOPは0とする)"',
            'movie_tag' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "動画タグ"',
        ], $tableOptions. ' COMMENT="コンテンツ_動画タグマッチング"');


        // コンテンツ_URLマッチングテーブル
        $this->dropTable('content_url');

        $this->createTable('content_url', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'content_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルcontent_masterのカラムid"',
            'area_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルarea_cdのカラムid(全国TOPは0とする)"',
            'url' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "URL"',
        ], $tableOptions. ' COMMENT="コンテンツ_URLマッチング"');


        // 検索結果一覧画面デフォルト並び順テーブル
        $this->dropTable('job_result_default_sort');

        $this->createTable('job_result_default_sort', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'priority' => 'TINYINT NOT NULL COMMENT "優先順位"',
            'item' => Schema::TYPE_STRING . ' NOT NULL COMMENT "項目名(disp_type：掲載タイプ、oiwai：お祝い金、disp_start：掲載開始日、update_time：更新日時)"',
        ], $tableOptions. ' COMMENT="検索結果一覧画面デフォルト並び順"');


        // 仕事情報簡易表示項目テーブル
        $this->dropTable('job_short_item_disp');

        $this->createTable('job_short_item_disp', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_short_item_disp_no' => 'TINYINT NOT NULL COMMENT "仕事情報簡易表示セットナンバー"',
            'function_item_set_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルfunction_item_setのカラムid"',
        ], $tableOptions. ' COMMENT="仕事情報簡易表示項目"');


        // 仕事情報簡易表示項目(検索結果）テーブル
        $this->dropTable('job_short_item_disp_result');

        $this->createTable('job_short_item_disp_result', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_short_item_disp_no' => 'TINYINT NOT NULL COMMENT "仕事情報簡易表示セットナンバー"',
            'function_item_set_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルfunction_item_setのカラムid"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' NOT NULL COMMENT "状態"',
        ], $tableOptions. ' COMMENT="仕事情報簡易表示項目(検索結果）"');


        // 詳細メイン-掲載タイプテーブル
        $this->dropTable('main_disp');

        $this->createTable('main_disp', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'main_disp_name' => Schema::TYPE_STRING . ' DEFAULT 0 NOT NULL COMMENT "詳細メイン名"',
            'disp_type_cd' => 'TINYINT NOT NULL COMMENT "掲載タイプ"',
            'item_column' => Schema::TYPE_STRING . ' NOT NULL COMMENT "テーブルjob_masterのカラム名"',
            'disp_chk' => 'TINYINT DEFAULT 1 COMMENT "表示チェック"',
        ], $tableOptions. ' COMMENT="詳細メイン-掲載タイプ"');


        // ポリシー管理テーブル
        $this->dropTable('policy_master');

        $this->createTable('policy_master', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'policy_master_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "ポリシーナンバー"',
            'policy_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "ポリシー名"',
            'policy' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "ポリシー文"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
        ], $tableOptions. ' COMMENT="ポリシー"');


        // ソーシャルボタンテーブル
        $this->dropTable('social_button');

        $this->createTable('social_button', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'option_social_button_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "ソーシャルボタンナンバー"',
            'social_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "ソーシャル名"',
            'social_script' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "スクリプト"',
            'social_meta' => Schema::TYPE_TEXT . ' NOT NULL COMMENT "メタタグ"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
        ], $tableOptions. ' COMMENT="ソーシャルボタン"');


        // ウィジェットテーブル
        $this->dropTable('widget');

        $this->createTable('widget', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'widget_no' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "ウィジェットナンバー"',
            'widget_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "ウィジェット名"',
            'widget_name_default' => Schema::TYPE_STRING . ' NOT NULL COMMENT "デフォルトウィジェット名"',
            'element1' => 'TINYINT COMMENT "コンテンツ内で1番目に表示させる要素"',
            'element2' => 'TINYINT COMMENT "コンテンツ内で2番目に表示させる要素"',
            'element3' => 'TINYINT COMMENT "コンテンツ内で3番目に表示させる要素"',
            'element4' => 'TINYINT COMMENT "コンテンツ内で4番目に表示させる要素"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' DEFAULT 1 COMMENT "状態"',
        ], $tableOptions. ' COMMENT="ウィジェット"');


        // ウィジェットレイアウトテーブル
        $this->dropTable('widget_layout');

        $this->createTable('widget_layout', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'widget_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルwidgetのカラムid"',
            'area_type' => 'TINYINT NOT NULL COMMENT "エリアタイプ（0=全国TOP、1=エリアTOP)"',
            'sort' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT ""',
        ], $tableOptions. ' COMMENT="ウィジェットレイアウト"');


        // 項目管理テーブル(カラム名変更対応)
        $this->update("function_item_set", ["item_column" => "admin_no"], "id = '1'");
        $this->update("function_item_set", ["item_column" => "admin_no"], "id = '194'");
        $this->update("function_item_set", ["item_column" => "client_no"], "id = '13'");
        $this->update("function_item_set", ["item_column" => "client_no"], "id = '206'");
        $this->update("function_item_set", ["item_column" => "job_no"], "id = '22'");
        $this->update("function_item_set", ["item_column" => "job_no"], "id = '215'");
        $this->update("function_item_set", ["item_column" => "application_no"], "id = '70'");
        $this->update("function_item_set", ["item_column" => "application_no"], "id = '263'");

    }


    public function safeDown()
    {

        // 学習テーブル
        $sql = <<<SQL
CREATE TABLE study_cd
(
    study_cd INT PRIMARY KEY NOT NULL,
    study_name LONGTEXT NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL
);
CREATE UNIQUE INDEX study_cd_PKI ON study_cd (study_cd);
SQL;
        $this->execute($sql);


        // 給与値テーブル
        $this->dropTable('wage_master');

        $sql = <<<SQL
CREATE TABLE wage_master
(
    wage_type_master_id INT NOT NULL,
    wage_value_master_id INT NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    wage_master_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    merubaito_wage_master_id INT
);
CREATE UNIQUE INDEX wage_master_PKI ON wage_master (wage_master_id);
SQL;
        $this->execute($sql);


        // 給与種別テーブル
        $this->dropTable('wage_type_master');

        $sql = <<<SQL
CREATE TABLE wage_type_master
(
    wage_type_name VARCHAR(255) NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort INT DEFAULT 0 NOT NULL,
    wage_type_master_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT
);
CREATE UNIQUE INDEX wage_type_master_PKI ON wage_type_master (wage_type_master_id);
SQL;
        $this->execute($sql);


        // 給与金額テーブル
        $this->dropTable('wage_value_master');

        $sql = <<<SQL
CREATE TABLE wage_value_master
(
    wage_value_master_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    wage_value_name VARCHAR(255) NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort INT DEFAULT 0 NOT NULL
);
CREATE UNIQUE INDEX wage_value_master_PKI ON wage_value_master (wage_value_master_id);
SQL;
        $this->execute($sql);


        // 希望の勤務日数テーブル
        $this->dropTable('work_date_cd');

        $sql = <<<SQL
CREATE TABLE work_date_cd
(
    work_date_cd INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    work_date_name LONGTEXT NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort_no SMALLINT,
    work_group_cd INT NOT NULL
);
CREATE UNIQUE INDEX work_date_cd_PKI ON work_date_cd (work_date_cd);
SQL;
        $this->execute($sql);


        // 勤務グループテーブル
        $this->dropTable('work_group_cd');

        $sql = <<<SQL
CREATE TABLE work_group_cd
(
    work_group_cd INT PRIMARY KEY NOT NULL,
    work_group_name LONGTEXT NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort SMALLINT
);
CREATE UNIQUE INDEX work_group_cd_PKI ON work_group_cd (work_group_cd);
SQL;
        $this->execute($sql);


        // 希望の勤務時間テーブル
        $this->dropTable('work_hour_cd');

        $sql = <<<SQL
CREATE TABLE work_hour_cd
(
    work_hour_cd INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    work_hour_name LONGTEXT NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort_no SMALLINT,
    work_group_cd INT NOT NULL
);
CREATE UNIQUE INDEX work_hour_cd_PKI ON work_hour_cd (work_hour_cd);
SQL;
        $this->execute($sql);


        // 希望の勤務期間テーブル
        $this->dropTable('work_term_cd');

        $sql = <<<SQL
CREATE TABLE work_term_cd
(
    work_term_cd INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    work_term_name LONGTEXT NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort_no SMALLINT,
    work_group_cd INT NOT NULL
);
CREATE UNIQUE INDEX work_term_cd_PKI ON work_term_cd (work_term_cd);
SQL;
        $this->execute($sql);


        // 勤務時間テーブル
        $this->dropTable('work_time_cd');

        $sql = <<<SQL
CREATE TABLE work_time_cd
(
    work_time_name VARCHAR(255) NOT NULL,
    del_chk SMALLINT DEFAULT 0 NOT NULL,
    valid_chk SMALLINT DEFAULT 1 NOT NULL,
    sort INT DEFAULT 0 NOT NULL,
    work_group_cd INT NOT NULL,
    work_time_cd INT PRIMARY KEY NOT NULL AUTO_INCREMENT
);
CREATE UNIQUE INDEX work_time_cd_PKI ON work_time_cd (work_time_cd);
SQL;
        $this->execute($sql);


        // コンテンツ_原稿マッチングテーブル
        $this->dropTable('content_job');

        $sql = <<<SQL
CREATE TABLE content_job
(
    content_job_id BIGINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    content_id SMALLINT NOT NULL,
    job_id INT NOT NULL,
    zenkoku_flg TINYINT NOT NULL
);
CREATE UNIQUE INDEX content_job_id ON content_job (content_job_id);
SQL;
        $this->execute($sql);


        // コンテンツ管理テーブル
        $this->dropTable('content_master');

        $sql = <<<SQL
CREATE TABLE content_master
(
    content_id BIGINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    content_name LONGTEXT NOT NULL,
    content_type TINYINT NOT NULL,
    widget_id TINYINT NOT NULL,
    title LONGTEXT,
    pict LONGTEXT,
    description LONGTEXT,
    sort INT NOT NULL,
    disp_start_date DATE,
    disp_end_date DATE,
    valid_chk TINYINT DEFAULT 1 NOT NULL
);
CREATE UNIQUE INDEX content_id ON content_master (content_id);
SQL;
        $this->execute($sql);


        // コンテンツ_動画タグマッチングテーブル
        $this->dropTable('content_movie');

        $sql = <<<SQL
CREATE TABLE content_movie
(
    content_movie_id BIGINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    content_id SMALLINT NOT NULL,
    area_cd TINYINT NOT NULL,
    movie_tag LONGTEXT NOT NULL
);
CREATE UNIQUE INDEX content_movie_id ON content_movie (content_movie_id);
SQL;
        $this->execute($sql);


        // コンテンツ_URLマッチングテーブル
        $this->dropTable('content_url');

        $sql = <<<SQL
CREATE TABLE content_url
(
    content_url_id BIGINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    content_id SMALLINT NOT NULL,
    area_cd TINYINT NOT NULL,
    url LONGTEXT NOT NULL
);
CREATE UNIQUE INDEX content_url_id ON content_url (content_url_id);
SQL;
        $this->execute($sql);


        // 検索結果一覧画面デフォルト並び順テーブル
        $this->dropTable('job_result_default_sort');

        $sql = <<<SQL
CREATE TABLE job_result_default_sort
(
    priority TINYINT PRIMARY KEY NOT NULL,
    item LONGTEXT NOT NULL
);
SQL;
        $this->execute($sql);


        // 仕事情報簡易表示項目テーブル
        $this->dropTable('job_short_item_disp');

        $sql = <<<SQL
CREATE TABLE job_short_item_disp
(
    job_short_item_disp_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    function_item_id INT NOT NULL
);
CREATE UNIQUE INDEX job_short_item_disp_PKI ON job_short_item_disp (job_short_item_disp_id);
SQL;
        $this->execute($sql);


        // 仕事情報簡易表示項目(検索結果）テーブル
        $this->dropTable('job_short_item_disp_result');

        $sql = <<<SQL
CREATE TABLE job_short_item_disp_result
(
    job_short_item_disp_id BIGINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    function_item_id INT NOT NULL,
    valid_chk TINYINT NOT NULL
);
CREATE UNIQUE INDEX job_short_item_disp_id ON job_short_item_disp_result (job_short_item_disp_id);
SQL;
        $this->execute($sql);


        // 詳細メイン-掲載タイプテーブル
        $this->dropTable('main_disp');

        $sql = <<<SQL
CREATE TABLE main_disp
(
    main_disp_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    main_disp_name VARCHAR(200) NOT NULL,
    disp_type_cd TINYINT NOT NULL,
    item_column VARCHAR(200) NOT NULL,
    disp_chk TINYINT DEFAULT 1 NOT NULL
);
CREATE UNIQUE INDEX main_disp_PKI ON main_disp (main_disp_id);
SQL;
        $this->execute($sql);


        // ポリシー管理テーブル
        $this->dropTable('policy_master');

        $sql = <<<SQL
CREATE TABLE policy_master
(
    policy_master_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    policy_name VARCHAR(200) DEFAULT '' NOT NULL,
    policy LONGTEXT NOT NULL,
    valid_chk TINYINT DEFAULT 1 NOT NULL
);
CREATE UNIQUE INDEX policy_master_PKI ON policy_master (policy_master_id);
SQL;
        $this->execute($sql);


        // ソーシャルボタンテーブル
        $this->dropTable('social_button');

        $sql = <<<SQL
CREATE TABLE social_button
(
    option_social_button_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    social_name VARCHAR(200) NOT NULL,
    social_script LONGTEXT NOT NULL,
    social_meta LONGTEXT NOT NULL,
    valid_chk INT DEFAULT 1 NOT NULL
);
CREATE UNIQUE INDEX social_button_PKI ON social_button (option_social_button_id);
SQL;
        $this->execute($sql);


        // ウィジェットテーブル
        $this->dropTable('widget');

        $sql = <<<SQL
CREATE TABLE widget
(
    widget_id TINYINT PRIMARY KEY NOT NULL,
    widget_name LONGTEXT NOT NULL,
    widget_name_default LONGTEXT NOT NULL,
    element1 TINYINT,
    element2 TINYINT,
    element3 TINYINT,
    element4 TINYINT,
    valid_chk TINYINT
);
SQL;
        $this->execute($sql);


        // ウィジェットレイアウトテーブル
        $this->dropTable('widget_layout');

        $sql = <<<SQL
CREATE TABLE widget_layout
(
    widget_layout_id INT NOT NULL,
    widget_id INT NOT NULL,
    area_type INT NOT NULL,
    sort INT NOT NULL,
    PRIMARY KEY (widget_layout_id, widget_id, area_type)
);
CREATE UNIQUE INDEX widget_layout_PKI ON widget_layout (widget_layout_id, widget_id, area_type);
CREATE INDEX widget_layout_idx ON widget_layout (widget_layout_id, widget_id, area_type);
SQL;
        $this->execute($sql);


        // 項目管理テーブル(カラム名変更対応)
        $this->update("function_item_set", ["item_column" => "admin_id"], "id = '1'");
        $this->update("function_item_set", ["item_column" => "admin_id"], "id = '194'");
        $this->update("function_item_set", ["item_column" => "client_id"], "id = '13'");
        $this->update("function_item_set", ["item_column" => "client_id"], "id = '206'");
        $this->update("function_item_set", ["item_column" => "job_id"], "id = '22'");
        $this->update("function_item_set", ["item_column" => "job_id"], "id = '215'");
        $this->update("function_item_set", ["item_column" => "application_id"], "id = '70'");
        $this->update("function_item_set", ["item_column" => "application_id"], "id = '263'");

    }
}
