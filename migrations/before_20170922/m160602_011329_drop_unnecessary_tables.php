<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `unnecessary_tabeles`.
 */
class m160602_011329_drop_unnecessary_tables extends Migration
{
    public function safeUp()
    {
        $this->dropTable('work_date_cd');
        $this->dropTable('work_group_cd');
        $this->dropTable('work_hour_cd');
        $this->dropTable('work_term_cd');
        $this->dropTable('work_time_cd');
        $this->dropTable('job_work_date');
        $this->dropTable('job_work_date_tmp');
        $this->dropTable('job_work_hour');
        $this->dropTable('job_work_hour_tmp');
        $this->dropTable('job_work_term');
        $this->dropTable('job_work_term_tmp');
        $this->dropTable('job_work_time');
        $this->dropTable('job_work_time_tmp');
        $this->dropTable('merit_category_cd');
    }

    public function safeDown()
    {
        $this->createTable('work_date_cd',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'work_date_cd' => $this->integer(11)->notNull(). ' COMMENT "希望の勤務日数コード"',
            'work_date_name' => $this->string(255)->notNull(). ' COMMENT "希望の勤務日数名"',
            'valid_chk' => $this->boolean()->defaultValue(1). ' COMMENT "状態"',
            'sort' => $this->smallInteger(6)->defaultValue(null). ' COMMENT "表示順"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="希望の勤務日数"');
            $this->addPrimaryKey('pk_work_date_cd', 'work_date_cd', ['id']);

        $this->createTable('work_group_cd',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'work_group_cd' => $this->integer(11)->notNull(). ' COMMENT "勤務グループコード"',
            'work_group_name' => $this->string(255)->notNull(). ' COMMENT "勤務グループ名"',
            'valid_chk' => $this->boolean()->defaultValue(1). ' COMMENT "状態"',
            'sort' => $this->smallInteger(6)->defaultValue(null). ' COMMENT "表示順"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="勤務グループ"');
        $this->addPrimaryKey('pk_work_group_cd', 'work_group_cd', ['id']);
        
        $this->createTable('work_hour_cd',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'work_hour_cd' => $this->integer(11)->notNull(). ' COMMENT "勤務時間コード"',
            'work_hour_name' => $this->string(255)->notNull(). ' COMMENT "勤務時間名"',
            'valid_chk' => $this->boolean()->defaultValue(1). ' COMMENT "状態"',
            'sort' => $this->smallInteger(6)->defaultValue(null). ' COMMENT "表示順"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="希望の勤務時間"');
        $this->addPrimaryKey('pk_work_hour_cd', 'work_hour_cd', ['id']);

        $this->createTable('work_term_cd',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'work_term_cd' => $this->integer(11)->notNull(). ' COMMENT "希望の勤務期間コード"',
            'work_term_name' => $this->string(255)->notNull(). ' COMMENT "希望の勤務期間名"',
            'valid_chk' => $this->boolean()->defaultValue(1). ' COMMENT "状態"',
            'sort' => $this->smallInteger(6)->defaultValue(null). ' COMMENT "表示順"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="希望の勤務期間"');
        $this->addPrimaryKey('pk_work_term_cd', 'work_term_cd', ['id']);
        
        $this->createTable('work_time_cd',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'work_time_name' => $this->string(255)->notNull(). ' COMMENT "勤務時間名"',
            'del_chk' => $this->boolean()->defaultValue(0). ' COMMENT "削除フラグ"',
            'valid_chk' => $this->boolean()->defaultValue(1). ' COMMENT "状態"',
            'sort' => $this->integer(11)->defaultValue(0). ' COMMENT "表示順"',
            'work_group_cd_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルwork_group_cdのカラムid"',
            'work_time_cd_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルwork_time_cdのカラムid"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="勤務時間"');
        $this->addPrimaryKey('pk_work_time_cd', 'work_time_cd', ['id']);

        $this->createTable('job_work_date',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'job_master_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルjob_masterのカラムid"',
            'work_date_cd_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルwork_date_cdのカラムid"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="仕事-希望の勤務日数関連"');
        $this->addPrimaryKey('pk_job_work_date', 'job_work_date', ['id']);

        $this->createTable('job_work_date_tmp',[
            'work_date_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_job_work_date_tmp', 'job_work_date_tmp', ['work_date_cd','tmp_key','tmp_id']);
        $this->createIndex('idx_job_work_date_tmp_1_tmp_key_2_tmp_id', 'job_work_date_tmp', ['tmp_key','tmp_id']);

        $this->createTable('job_work_hour',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'job_master_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルjob_masterのカラムid"',
            'work_hour_cd_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルwork_hour_cdのカラムid"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="仕事-希望の勤務時間関連"');
        $this->addPrimaryKey('pk_job_work_hour', 'job_work_hour', ['id']);

        $this->createTable('job_work_hour_tmp',[
            'work_hour_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_job_work_hour_tmp', 'job_work_hour_tmp', ['work_hour_cd','tmp_key','tmp_id']);
        $this->createIndex('idx_job_work_hour_tmp_1_tmp_key_2_tmp_id', 'job_work_hour_tmp', ['tmp_key','tmp_id']);

        $this->createTable('job_work_term',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'job_master_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルjob_masterのカラムid"',
            'work_term_cd_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルwork_term_cdのカラムid"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="仕事-希望の勤務期間関連"');
        $this->addPrimaryKey('pk_job_work_term', 'job_work_term', ['id']);

        $this->createTable('job_work_term_tmp',[
            'work_term_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_job_work_term_tmp', 'job_work_term_tmp', ['work_term_cd','tmp_key','tmp_id']);
        $this->createIndex('idx_job_work_term_tmp_1_tmp_key_2_tmp_id', 'job_work_term_tmp', ['tmp_key','tmp_id']);

        $this->createTable('job_work_time',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'job_master_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルjob_masterのカラムid"',
            'work_time_cd_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルwork_time_cdのカラムid"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="仕事-勤務時間関連"');
        $this->addPrimaryKey('pk_job_work_time', 'job_work_time', ['id']);

        $this->createTable('job_work_time_tmp',[
            'work_time_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_job_work_time_tmp', 'job_work_time_tmp', ['work_time_cd','tmp_key','tmp_id']);
        $this->createIndex('idx_job_work_time_tmp_11_tmp_key_2_tmp_id', 'job_work_time_tmp', ['tmp_key','tmp_id']);

        $this->createTable('merit_category_cd',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'merit_category_cd' => $this->integer(11)->notNull(). ' COMMENT "メリットカテゴリコード"',
            'merit_category_name' => $this->string(255)->notNull(). ' COMMENT "メリットカテゴリ名"',
            'valid_chk' => $this->boolean()->defaultValue(1). ' COMMENT "状態"',
            'del_chk' => $this->boolean()->defaultValue(0). ' COMMENT "削除フラグ"',
            'sort' => $this->smallInteger(6)->defaultValue(null). ' COMMENT "表示順"',
            'merit_display_type' => 'TINYINT(4) DEFAULT 0  COMMENT "メリット表示タイプ(0=チェックボックス 1=ラジオボタン)"',
            'merit_search_type' =>  'TINYINT(4) DEFAULT 0  COMMENT "メリット検索タイプ(0=AND検索 1=OR検索)"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="メリットカテゴリ"');
        $this->addPrimaryKey('pk_merit_category_cd', 'merit_category_cd', ['id']);
    }
}