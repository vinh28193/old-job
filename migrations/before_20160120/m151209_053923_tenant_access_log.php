<?php

use yii\db\Schema;
use yii\db\Migration;

class m151209_053923_tenant_access_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // アクセスログテーブル
        $this->dropTable('access_log');

        $this->createTable('access_log', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'access_date' => Schema::TYPE_DATE . ' COMMENT "アクセス日"',
            'job_master_id' => Schema::TYPE_INTEGER . ' COMMENT "求人ＩＤ"',
            'client_master_id' => Schema::TYPE_INTEGER . ' COMMENT "掲載企業ＩＤ"',
            'corp_master_id' => Schema::TYPE_INTEGER . ' COMMENT "代理店ＩＤ"',
            'job_type_big_cds' => Schema::TYPE_STRING . ' COMMENT "職種大複数"',
            'job_type_small_cds' => Schema::TYPE_STRING . ' COMMENT "職種小複数"',
            'pref_cds' => Schema::TYPE_STRING . ' COMMENT "都道府県複数"',
            'carrier_type' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "登録機器(0=PC,1=スマホ)"',
            'access_type' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "アクセスタイプ(0=仕事詳細,1=応募)"',
            'access_user_agent' => Schema::TYPE_STRING . ' COMMENT "ユーザーエージェント"',

        ], $tableOptions. ' COMMENT="アクセスログ"');

        $this->addPrimaryKey('pk_access_log', 'access_log', ['id', 'tenant_id']);
        $this->alterColumn('access_log', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE access_log PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

        // アクセスログマンスリーテーブル（バッチによる実行）
        $this->dropTable('access_log_monthly');

        $this->createTable('access_log_monthly', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'access_date' => Schema::TYPE_DATE . ' COMMENT "アクセス日"',
            'detail_count_pc' => Schema::TYPE_INTEGER . ' COMMENT "PC閲覧数"',
            'detail_count_smart' => Schema::TYPE_INTEGER . ' COMMENT "スマホ閲覧数"',
            'application_count_pc' => Schema::TYPE_INTEGER . ' COMMENT "PC応募数"',
            'application_count_smart' => Schema::TYPE_INTEGER . ' COMMENT "スマホ応募数"',
            'member_count_pc' => Schema::TYPE_INTEGER . ' COMMENT "PC登録者数"',
            'member_count_smart' => Schema::TYPE_INTEGER . ' COMMENT "スマホ登録者数"',

        ], $tableOptions. ' COMMENT="アクセスログマンスリー"');

        $this->addPrimaryKey('pk_access_log_monthly', 'access_log_monthly', ['id', 'tenant_id']);
        $this->alterColumn('access_log_monthly', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');

    }


    public function safeDown()
    {
        // アクセスログテーブル
        $this->dropTable('access_log');

        $sql = <<<SQL
CREATE TABLE access_log
(
    access_date DATE,
    job_id INT,
    client INT,
    corp_id INT,
    job_type_big_cds LONGTEXT,
    job_type_small_cds LONGTEXT,
    pref_cds LONGTEXT,
    carrier_type SMALLINT,
    access_type SMALLINT,
    access_log_id INT NOT NULL,
    access_user_agent LONGTEXT,
    PRIMARY KEY (access_log_id)
);
CREATE UNIQUE INDEX access_log_PKI ON access_log (access_log_id);
SQL;
        $this->execute($sql);

        // アクセルログマンスリー
        $this->dropTable('access_log_monthly');

        $sql = <<<SQL
CREATE TABLE access_log_monthly
(
    access_date DATE,
    job_count INT,
    detail_count_pc INT,
    detail_count_mobile INT,
    application_count_pc INT,
    application_count_mobile INT,
    regist_count_pc INT,
    regist_count_mobile INT,
    new_job_count INT,
    detail_count_smart INT,
    application_count_smart INT,
    regist_count_smart INT,
    PRIMARY KEY (access_date)
);
CREATE UNIQUE INDEX access_log_monthly_PKI ON access_log_monthly (access_date);
SQL;
        $this->execute($sql);

    }

}
