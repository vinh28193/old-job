<?php

use yii\db\Migration;

/**
 * Handles the dropping for unnecessary tables.
 */
class m161206_035748_drop_unnecessary_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropTable('access_jobtype_ranking');
        $this->dropTable('access_log_job_data');
        $this->dropTable('access_merit_ranking');
        $this->dropTable('access_phone_count_new');

        $this->dropTable('application_is_read');

        $this->dropTable('client_charge_plan_employment_type');
        $this->dropTable('client_charge_plan_job_type_big');
        $this->dropTable('client_master_tmp');
        $this->dropTable('client_scout_condition');
        $this->dropTable('client_scout_favorite_member');
        $this->dropTable('client_scout_limit');
        $this->dropTable('client_scout_template');

        $this->dropTable('emergency_master');

        $this->dropTable('expo_master');
        $this->dropTable('expo_reserve_log');

        //$this->dropTable('featured_job_set');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->createTable('access_jobtype_ranking',[
            'job_type_big_id' => $this->integer(11)->notNull(),
            'area_id' => $this->integer(11)->notNull(),
            'access_count_pc' => $this->integer(11)->notNull()->defaultValue(0),
            'access_count_mb' => $this->integer(11)->notNull()->defaultValue(0),
            'access_date' => $this->date()->notNull()->defaultValue('2014-01-01'),
            'access_count_smart' => $this->integer(11)->notNull()->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');

        $this->createTable('access_log_job_data',[
            'job_id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->notNull(),
            'corp_id' => $this->integer(11)->notNull(),
            'job_type_big_cds' => $this->text()->notNull(),
            'job_type_small_cds' => $this->text()->notNull(),
            'pref_cds' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');

        $this->createTable('access_merit_ranking',[
            'merit_cd' => $this->integer(11)->notNull(),
            'area_id' => $this->integer(11)->notNull(),
            'access_count_pc' => $this->integer(11)->notNull()->defaultValue(0),
            'access_count_mb' => $this->integer(11)->notNull()->defaultValue(0),
            'access_date' => $this->date()->notNull()->defaultValue('2014-01-01'),
            'access_count_smart' => $this->integer(11)->notNull()->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');

        $this->createTable('access_phone_count_new',[
            'phone_log_id' => $this->primaryKey(),
            'job_id' => $this->integer(11)->notNull(),
            'access_phone_date' => $this->date()->notNull()->defaultValue('2014-01-01'),
            'access_phone_type' => $this->smallInteger(6)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');

        $this->createTable('application_is_read',[
            'application_id' => $this->primaryKey(),
            'is_read_for_admin' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_read_for_corp' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_read_for_client' => $this->smallInteger()->notNull()->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');

        $this->createTable('client_charge_plan_employment_type',[
            'client_charge_plan_id' => $this->integer(11)->notNull(),
            'employment_type_cd' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_client_charge_plan_employment_type', 'client_charge_plan_employment_type', ['client_charge_plan_id','employment_type_cd']);

        $this->createTable('client_charge_plan_job_type_big',[
            'client_charge_plan_id' => $this->integer(11)->notNull(),
            'job_type_big_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_client_charge_plan_job_type_big', 'client_charge_plan_job_type_big', ['client_charge_plan_id','job_type_big_id']);

        $this->createTable('client_master_tmp',[
            'tmp_id' => $this->integer(11)->notNull(),
            'corp_id' => $this->integer(11)->notNull(),
            'client_name' => $this->text()->notNull(),
            'client_name_kana' => $this->text()->notNull(),
            'tel_no' => $this->text()->notNull(),
            'address' => $this->text()->notNull(),
            'tanto_name' => $this->text()->notNull(),
            'created_at' => $this->integer(11)->notNull(). ' COMMENT "登録日"',
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'client_business_outline' => $this->text()->notNull(),
            'client_corporate_url' => $this->text()->notNull(),
            'option100' => $this->text()->notNull(),
            'option101' => $this->text()->notNull(),
            'option102' => $this->text()->notNull(),
            'option103' => $this->text()->notNull(),
            'option104' => $this->text()->notNull(),
            'option105' => $this->text()->notNull(),
            'option106' => $this->text()->notNull(),
            'option107' => $this->text()->notNull(),
            'option108' => $this->text()->notNull(),
            'option109' => $this->text()->notNull(),
            'tmp_key' => $this->integer(11)->defaultValue(null),
            'job_limit_1' => $this->integer(11)->notNull()->defaultValue(0),
            'job_limit_2' => $this->integer(11)->notNull()->defaultValue(0),
            'job_limit_3' => $this->integer(11)->notNull()->defaultValue(0),
            'client_limit_date' => $this->date()->notNull()->defaultValue('2014-01-01')
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');

        $this->createTable('client_scout_condition',[
            'client_scout_condition_id' => $this->integer(11)->notNull()->defaultValue(0),
            'client_id' => $this->integer(11)->notNull(),
            'skill_op0' => $this->text()->notNull(),
            'skill_op1' => $this->text()->notNull(),
            'skill_op2' => $this->text()->notNull(),
            'skill_op3' => $this->text()->notNull(),
            'skill_op4' => $this->text()->notNull(),
            'skill_op5' => $this->text()->notNull(),
            'skill_op6' => $this->text()->notNull(),
            'skill_op7' => $this->text()->notNull(),
            'skill_op8' => $this->text()->notNull(),
            'skill_op9' => $this->text()->notNull(),
            'skill_op10' => $this->text()->notNull(),
            'skill_op11' => $this->text()->notNull(),
            'skill_op12' => $this->text()->notNull(),
            'skill_op13' => $this->text()->notNull(),
            'skill_op14' => $this->text()->notNull(),
            'skill_op15' => $this->text()->notNull(),
            'skill_op16' => $this->text()->notNull(),
            'skill_op17' => $this->text()->notNull(),
            'skill_op18' => $this->text()->notNull(),
            'skill_op19' => $this->text()->notNull(),
            'skill_op20' => $this->text()->notNull(),
            'skill_op21' => $this->text()->notNull(),
            'skill_op22' => $this->text()->notNull(),
            'skill_op23' => $this->text()->notNull(),
            'skill_op24' => $this->text()->notNull(),
            'pref_cds' => $this->text()->notNull(),
            'pref_dist_master_ids' => $this->text(),
            'merit_cds' => $this->text()->notNull(),
            'job_type_big_cds' => $this->text()->notNull(),
            'work_time_cds' => $this->text()->notNull(),
            'wage_master_ids' => $this->text(),
            'employment_type_cds' => $this->text()->notNull(),
            'sex_type' => $this->text()->notNull(),
            'carrier_type' => $this->smallInteger(6)->defaultValue(null),
            'favorite_flg' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'scout_flg' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'client_scout_condition_name' => $this->text()->notNull(),
            'age_end' => $this->integer(11)->defaultValue(null),
            'age_start' => $this->integer(11)->defaultValue(null),
            'area_id' => $this->text()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_client_scout_condition_id','client_scout_condition',['client_scout_condition_id']);

        $this->createTable('client_scout_favorite_member',[
            'client_id'  => $this->integer(11)->notNull(),
            'member_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull(). ' COMMENT "コメント未定"',
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_client_scout_favorite_member','client_scout_favorite_member',['client_id','member_id']);

        $this->createTable('client_scout_limit',[
            'id'  => $this->primaryKey(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'client_master_id' => $this->integer(11)->notNull(). ' COMMENT "掲載企業ID"',
            'last_send_date' => $this->date()->notNull(). ' COMMENT "最終送信日"',
            'send_num' => $this->integer(11)->notNull()->defaultValue(0). ' COMMENT "送信数"',
            'send_num_limit' => $this->integer(11)->notNull()->defaultValue(0). ' COMMENT "送信上限数"',
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="掲載企業スカウトメール上限数"');
        $this->createIndex('idx_client_scout_limit_client_id','client_scout_limit','client_master_id');
        $this->createIndex('idx_client_scout_limit_tenant_id','client_scout_limit','tenant_id');

        $this->createTable('client_scout_template',[
            'client_scout_template_id'  => $this->primaryKey(),
            'client_id' => $this->integer(11)->notNull(),
            'template_name' => $this->text()->notNull(),
            'title' => $this->text()->notNull(),
            'contents' => $this->text(),
            'updated_at' => $this->integer(11)->notNull(). ' COMMENT "コメント未定"',
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');

        $this->createTable('emergency_master',[
            'emergency_master_id'  => $this->primaryKey(),
            'carrier_type' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'job_id' => $this->integer(11)->notNull(),
            'area_id' => $this->integer(11)->notNull(),
            'satellite_id' => $this->integer(11)->notNull()->defaultValue(0),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');

        $this->createTable('expo_master',[
            'expo_id'  => $this->primaryKey(),
            'expo_date' => $this->date()->defaultValue(null),
            'expo_time' => $this->text()->notNull(),
            'disp_end_date' => $this->date()->defaultValue(null),
            'title' => $this->text()->notNull(),
            'place' => $this->text()->notNull(),
            'contents' => $this->text()->notNull(),
            'pr' => $this->text()->notNull(),
            'map' => $this->text()->notNull(),
            'mail_address' => 'varchar(32) DEFAULT NULL',
            'tel' => $this->text()->notNull(),
            'regist_date' => $this->date()->notNull()->defaultValue('2014-01-01'),
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'map_mb' => $this->text()->notNull(),
            'corp_name' => $this->text()->notNull(),
            'disp_start_date' => $this->date()->defaultValue(null),
            'area_cds' => $this->text()->notNull(),
            'map_smart' => $this->text()->notNull(),
            'satellite_id' => $this->integer(11)->notNull()->defaultValue(0),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');

        $this->createTable('expo_reserve_log',[
            'expo_reserve_id'  => $this->primaryKey(),
            'expo_id' => $this->integer(11),
            'name_sei' => $this->text()->notNull(),
            'name_mei' => $this->text()->notNull(),
            'kana_sei' => $this->text()->notNull(),
            'kana_mei' => $this->text()->notNull(),
            'tel_no' => 'varchar(255) NOT NULL',
            'mail_address' => 'varchar(32) DEFAULT NULL',
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'occupation_cd' => $this->integer(11)->notNull(),
            'sex' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'carrier_type' => $this->smallInteger(6)->notNull()->defaultValue(0),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
    }
}
