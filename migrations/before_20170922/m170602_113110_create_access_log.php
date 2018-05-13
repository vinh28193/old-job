<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m170602_113110_create_access_log
 * アクセスログのテーブル作成
 */
class m170602_113110_create_access_log extends Migration
{
    const TABLE_NAME = 'access_log';

    public function safeUp()
    {
        //既存のテーブルを削除
//        $this->dropTable(self::TABLE_NAME);
        $this->execute('DROP TABLE IF EXISTS ' . self::TABLE_NAME);

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TABLE_NAME, [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'accessed_at' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "アクセスされた日時"',
            'job_master_id' => Schema::TYPE_INTEGER . '  COMMENT "テーブルjob_masterのカラムid"',
            'application_master_id' => Schema::TYPE_INTEGER . '  COMMENT "テーブルapplication_masterの応募id"',
            'carrier_type' => 'TINYINT(4) NOT NULL DEFAULT 0 COMMENT "アクセスされた機器"',
            'access_url' => 'VARCHAR(255) COMMENT "アクセスされたURL"',
            'access_browser' => 'VARCHAR(255) COMMENT "アクセスされたブラウザ"',
            'access_user_agent' => 'VARCHAR(255) COMMENT "アクセスされたユーザーエージェント"',
            'access_referrer' => 'VARCHAR(255) COMMENT "アクセスされたリファラー"',
            'search_date' => $this->date()->defaultValue(null) . ' COMMENT "検索用日付"',
        ], $tableOptions . ' COMMENT="アクセスログ"');

        // プライマリーキーの削除・追加
        $this->execute('ALTER TABLE access_log DROP PRIMARY KEY , ADD PRIMARY KEY ( id, tenant_id )');

        //indexの追加(access_logテーブル)
        $this->createIndex('idx_access_log_job_master_id', 'access_log', ['job_master_id']);
        $this->createIndex('idx_access_log_accessed_at', 'access_log', ['accessed_at']);
        $this->createIndex('idx_access_log_search_date', 'access_log', ['search_date']);
        $this->createIndex('idx_access_log_1_tenant_id_2_access_referrer', 'access_log', ['tenant_id', 'access_referrer']);
        $this->createIndex('idx_access_log_1_tenant_id_2_access_user_agent', 'access_log', ['tenant_id', 'access_user_agent']);

        //indexの追加(job_masterテーブル)
        $this->createIndex('idx_job_master_client_master_id', 'job_master', ['client_master_id']);

        // パーテーション設定
        $this->execute('ALTER TABLE access_log PARTITION BY HASH (tenant_id) PARTITIONS 35;');
    }

    public function safeDown()
    {
        //indexの削除(access_logテーブル)
        $this->dropIndex('idx_access_log_search_date', 'access_log');
        $this->dropIndex('idx_access_log_job_master_id', 'access_log', ['job_master_id']);
        $this->dropIndex('idx_access_log_accessed_at', 'access_log', ['accessed_at']);
        $this->dropIndex('idx_access_log_1_tenant_id_2_access_referrer', 'access_log', ['tenant_id', 'access_referrer']);
        $this->dropIndex('idx_access_log_1_tenant_id_2_access_user_agent', 'access_log', ['tenant_id', 'access_user_agent']);

        //indexの削除(job_masterテーブル)
        $this->dropIndex('idx_job_master_client_master_id', 'job_master', ['client_master_id']);

        // パーテーション解除
        $this->execute('ALTER TABLE access_log REMOVE PARTITIONING;');

        // プライマリーキーの削除・追加
        $this->execute('ALTER TABLE access_log DROP PRIMARY KEY , ADD PRIMARY KEY ( id )');

        $this->dropTable(self::TABLE_NAME);

        // 元のテーブルを作成
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TABLE_NAME, [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'created_at' => Schema::TYPE_DATE . ' COMMENT "アクセス日"',
            'job_master_id' => Schema::TYPE_INTEGER . ' COMMENT "求人ＩＤ"',
            'client_master_id' => Schema::TYPE_INTEGER . ' COMMENT "掲載企業ＩＤ"',
            'corp_master_id' => Schema::TYPE_INTEGER . ' COMMENT "代理店ＩＤ"',
            'job_type_big_id' => Schema::TYPE_STRING . ' COMMENT "職種大複数"',
            'job_type_small_id' => Schema::TYPE_STRING . ' COMMENT "職種小複数"',
            'pref_id' => Schema::TYPE_STRING . ' COMMENT "都道府県複数"',
            'carrier_type' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "登録機器(0=PC,1=スマホ)"',
            'access_type' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "アクセスタイプ(0=仕事詳細,1=応募)"',
            'access_user_agent' => Schema::TYPE_STRING . ' COMMENT "ユーザーエージェント"',
        ], $tableOptions. ' COMMENT="アクセスログ"');

        $this->addPrimaryKey('pk_access_log', 'access_log', ['id', 'tenant_id']);
        $this->alterColumn('access_log', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE access_log PARTITION BY HASH (tenant_id) PARTITIONS 2000;');
    }
}
