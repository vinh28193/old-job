<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `unnecessary`.
 */
class m161206_061408_drop_unnecessary_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropTable('member_condition');
        $this->dropTable('member_resume');
        $this->dropTable('member_scout');
        $this->dropTable('nice_answer');
        $this->dropTable('oiwai_application');
        $this->dropTable('oiwai_master');
        $this->dropTable('prod');
        $this->dropTable('ranking_for_mobile');
        $this->dropTable('satellite_master');
        $this->dropTable('send_mail_contents_master');
        $this->dropTable('send_mail_contents_mobile');
        $this->dropTable('send_mail_contents_pc');
        $this->dropTable('send_mail_logs');
        $this->dropTable('send_mail_logs_application');
        $this->dropTable('send_mail_logs_from_client');
        $this->dropTable('send_mail_logs_from_user');
        $this->dropTable('send_mail_logs_scout');
        $this->dropTable('send_mail_logs_scout_subset');
        $this->dropTable('send_mail_logs_subset');
        $this->dropTable('send_mail_master');
        $this->dropTable('send_mail_scout_master');
        $this->dropTable('wage_master');
        $this->dropTable('wage_type_master');
        $this->dropTable('wage_value_master');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->createTable('member_condition',[
            'member_condition_id' => $this->integer(11)->notNull()->defaultValue(0),
            'member_id' => $this->integer(11)->notNull(),
            'mail_chk' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'area_id' => $this->integer(11)->notNull(),
            'pref_cds' => $this->text()->notNull(),
            'job_type_big_cds' => $this->text()->notNull(),
            'job_type_small_cds' => $this->text()->notNull(),
            'work_time_cds' => $this->text()->notNull(),
            'employment_type_cds' => $this->text()->notNull(),
            'occupation_cd' => $this->text(),
            'route_cd' => $this->integer(11)->defaultValue(null),
            'merit_cds' => $this->text()->notNull(),
            'free_word' => $this->text()->notNull(),
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'station_cd_start' => $this->integer(11)->defaultValue(null),
            'station_cd_end' => $this->integer(11)->defaultValue(null),
            'pref_dist_master_ids' => $this->text()->notNull(),
            'wage_master_ids' => $this->text()->notNull(),
            'work_date_cds' => $this->text()->notNull(),
            'work_hour_cds' => $this->text()->notNull(),
            'work_term_cds' => $this->text()->notNull(),
            'dist_group_cds' => $this->text()->notNull(),

        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_member_condition_id', 'member_condition', ['member_condition_id']);

        $this->createTable('member_resume',[
            'member_id' => $this->integer(11)->notNull(),
            'tel_no' => $this->text()->notNull(),
            'pref_id' => $this->smallInteger(6)->defaultValue(null),
            'address' => $this->text()->notNull(),
            'self_pr' => $this->text()->notNull(),
            'created_at' => $this->text()->notNull(). ' COMMENT "登録日時"',
            'member_resume_id' => $this->integer(11)->defaultValue(0),
            'work_time_cds' => $this->text()->notNull(),
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
            'application_file' => $this->text()->notNull(),
            'application_file_disp' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_member_resume_id', 'member_resume', ['member_resume_id']);

        $this->createTable('member_scout',[
            'member_id' => $this->integer(11)->notNull(),
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
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
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_member_id', 'member_scout', ['member_id']);

        $this->createTable('nice_answer',[
            'qa_answer_id' => $this->integer(11)->notNull(),
            'member_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_nice_answer', 'nice_answer', ['member_id','qa_answer_id']);

        $this->createTable('oiwai_application',[
            'oiwai_application_id' => $this->integer(11)->notNull()->defaultValue(0),
            'mail_address' => $this->string(32)->defaultValue(null),
            'collation_key' => $this->text()->notNull(),
            'created_at' => $this->integer(11)->notNull(). ' COMMENT "申請日時"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_oiwai_application_id', 'oiwai_application', ['oiwai_application_id']);

        $this->createTable('oiwai_master',[
            'oiwai_master_id' => $this->integer(11)->notNull()->defaultValue(0),
            'application_id' => $this->integer(11)->notNull(),
            'name' => $this->text()->notNull(),
            'address' => $this->text()->notNull(),
            'tel' => $this->text()->notNull(),
            'mail_address' => $this->string(32)->notNull(),
            'created_at' => $this->integer(11)->notNull(). ' COMMENT "申請日時"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_oiwai_master', 'oiwai_master', ['oiwai_master_id','application_id']);

        $this->createTable('prod',[
            'prod_id' => $this->integer(11)->notNull(),
            'prod_name' => $this->text()->notNull(),
            'price' => $this->integer(11)->defaultValue(null),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_prod_id', 'prod', ['prod_id']);

        $this->createTable('ranking_for_mobile',[
            'ranking_for_mobile_id' => $this->integer(11)->notNull()->defaultValue(0),
            'job_id' => $this->integer(11)->notNull(),
            'area_id' => $this->integer(11)->notNull(),
            'sort_no' => $this->integer(11)->notNull(),
            'disp_date' => $this->date()->notNull()->defaultValue('2014-01-01'),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_ranking_for_mobile_id', 'ranking_for_mobile', ['ranking_for_mobile_id']);

        $this->createTable('satellite_master',[
            'satellite_id' => $this->integer(11)->notNull()->defaultValue(0),
            'satellite_name' => $this->text()->notNull(),
            'satellite_dir' => $this->text()->notNull(),
            'site_title' => $this->string(255)->notNull()->defaultValue(''),
            'meta_description' => $this->string(255)->notNull()->defaultValue(''),
            'meta_keywords' => $this->string(255)->notNull()->defaultValue(''),
            'smart_site_title' => $this->string(255)->notNull()->defaultValue(''),
            'smart_meta_description' => $this->string(255)->notNull()->defaultValue(''),
            'smart_meta_keywords' => $this->string(255)->notNull()->defaultValue(''),
            'job_type_big_cds' => $this->text()->notNull(),
            'merit_cd' => $this->text()->notNull(),
            'created_at' => $this->integer(11)->notNull(). ' COMMENT "作成日時"',
            'updated_at' => $this->integer(11)->notNull(). ' COMMENT "更新日時"',
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_satellite_id', 'satellite_master', ['satellite_id']);

        $this->createTable('send_mail_contents_master',[
            'send_mail_contents_master_id' => $this->integer(11)->notNull()->defaultValue(0),
            'send_mail_contents_field_name' => $this->string(255)->notNull()->defaultValue(''),
            'send_mail_contents_disp_name' => $this->string(255)->notNull()->defaultValue(''),
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'function_item_id' => $this->integer(11)->notNull()->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_contents_master_id', 'send_mail_contents_master', ['send_mail_contents_master_id']);

        $this->createTable('send_mail_contents_mobile',[
            'send_mail_contents_mobile_id' => $this->integer(11)->notNull()->defaultValue(0),
            'send_mail_master_id' => $this->integer(11)->notNull(),
            'send_mail_contents_master_id' => $this->integer(11)->notNull()->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_contents_mobile_id', 'send_mail_contents_mobile', ['send_mail_contents_mobile_id']);

        $this->createTable('send_mail_contents_pc',[
            'send_mail_contents_pc_id' => $this->integer(11)->notNull()->defaultValue(0),
            'send_mail_master_id' => $this->integer(11)->notNull()->defaultValue(0),
            'send_mail_contents_master_id' => $this->integer(11)->notNull()->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_contents_pc_id', 'send_mail_contents_pc', ['send_mail_contents_pc_id']);

        $this->createTable('send_mail_logs',[
            'send_mail_logs_id' => $this->integer(11)->notNull()->defaultValue(0),
            'start_sending_at' => $this->integer(11)->notNull(). ' COMMENT "送信開始日時"',
            'finish_sending_at' => $this->integer(11)->notNull(). ' COMMENT "送信完了日時"',
            'send_mail_success_count' => $this->integer(11)->notNull()->defaultValue(0),
            'send_mail_failure_count' => $this->integer(11)->notNull()->defaultValue(0),
            'mail_type' => $this->text()->notNull(),
            'send_mail_logs_subject' => $this->text()->notNull(),
            'send_mail_logs_from_mail_address' => $this->text()->notNull(),
            'send_mail_logs_content' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_logs_id', 'send_mail_logs', ['send_mail_logs_id']);

        $this->createTable('send_mail_logs_application',[
            'send_mail_logs_application_id' => $this->integer(11)->notNull()->defaultValue(0),
            'application_id' => $this->integer(11)->notNull(),
            'admin_id' => $this->integer(11)->notNull(),
            'from_mail_address' => $this->string(32)->defaultValue(null),
            'title' => $this->text()->notNull(),
            'contents' => $this->text()->notNull(),
            'send_at' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_logs_application_id', 'send_mail_logs_application', ['send_mail_logs_application_id']);

        $this->createTable('send_mail_logs_from_client',[
            'send_mail_logs_from_client_id' => $this->integer(11)->notNull()->defaultValue(0),
            'application_id' => $this->integer(11)->notNull(),
            'send_at' => $this->integer(11)->notNull(). ' COMMENT "送信日時"',
            'subject' => $this->text()->notNull(),
            'contents' => $this->text()->notNull(),
            'user_read_chk' => $this->smallInteger(6)->defaultValue(0),
            'client_del_chk' => $this->smallInteger(6)->defaultValue(0),
            'user_del_chk' => $this->smallInteger(6)->defaultValue(0),
            'important_flg' => $this->smallInteger(6)->defaultValue(0),
            'important_for_client' => $this->smallInteger(6)->defaultValue(0),
            'reply_flg_from_user' => $this->smallInteger(6)->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_logs_from_client_id', 'send_mail_logs_from_client', ['send_mail_logs_from_client_id']);

        $this->createTable('send_mail_logs_from_user',[
            'send_mail_logs_from_user_id' => $this->integer(11)->notNull()->defaultValue(0),
            'application_id' => $this->integer(11)->notNull(),
            'send_at' => $this->integer(11)->notNull(). ' COMMENT "送信日時"',
            'subject' => $this->text()->notNull(),
            'contents' => $this->text()->notNull(),
            'user_read_chk' => $this->smallInteger(6)->defaultValue(0),
            'client_del_chk' => $this->smallInteger(6)->defaultValue(0),
            'user_del_chk' => $this->smallInteger(6)->defaultValue(0),
            'important_flg' => $this->smallInteger(6)->defaultValue(0),
            'important_for_client' => $this->smallInteger(6)->defaultValue(0),
            'reply_flg_from_user' => $this->smallInteger(6)->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_logs_from_user_id', 'send_mail_logs_from_user', ['send_mail_logs_from_user_id']);

        $this->createTable('send_mail_logs_scout',[
            'client_id' => $this->integer(11)->notNull(),
            'from_name' => $this->text()->notNull(),
            'from_mail_address' => $this->string(32)->defaultValue(null),
            'send_at' => $this->integer(11)->notNull(). ' COMMENT "送信日時"',
            'title' => $this->text()->notNull(),
            'contents' => $this->text()->notNull(),
            'send_mail_logs_scout_id' => $this->integer(11)->notNull()->defaultValue(0),
            'job_id' => $this->integer(11)->notNull(),
            'mb_title' => $this->text()->notNull(),
            'mb_contents' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createTable('send_mail_logs_scout_subset',[
            'send_mail_logs_scout_subset_id' => $this->integer(11)->notNull()->defaultValue(0),
            'send_mail_logs_scout_id' => $this->integer(11)->notNull(),
            'member_id' => $this->integer(11)->notNull(),
            'to_mail_address' => $this->string(32)->defaultValue(null),
            'read_chk' => $this->smallInteger(6)->defaultValue(0),
            'save_flg' => $this->smallInteger(6)->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_logs_scout_subset_id', 'send_mail_logs_scout_subset', ['send_mail_logs_scout_subset_id']);

        $this->createTable('send_mail_logs_subset',[
            'send_mail_logs_subset_id' => $this->integer(11)->notNull()->defaultValue(0),
            'send_mail_logs_id' => $this->integer(11)->notNull(),
            'member_id' => $this->integer(11)->notNull(),
            'mail_address' => $this->string(32)->defaultValue(null),
            'read_chk' => $this->smallInteger(6)->defaultValue(0),
            'save_flg' => $this->smallInteger(6)->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_logs_subset_id', 'send_mail_logs_subset', ['send_mail_logs_subset_id']);

        $this->createTable('send_mail_master',[
            'send_mail_master_id' => $this->integer(11)->notNull()->defaultValue(0),
            'next_send_at' => $this->integer(11)->notNull(). ' COMMENT "送信日時"',
            'per_days' => $this->integer(11)->notNull()->defaultValue(7),
            'mail_from_text_pc' => $this->string(255)->notNull()->defaultValue(''),
            'mail_from_address_pc' => $this->string(32)->defaultValue(null),
            'mail_subject_pc' => $this->string(255)->notNull()->defaultValue(''),
            'mail_header_pc' => $this->string(2000)->notNull()->defaultValue('メールヘッダ（1000文字程度）'),
            'mail_footer_pc' => $this->string(2000)->notNull()->defaultValue('メールヘッダ（1000文字程度）'),
            'valid_chk' => $this->smallInteger(6)->defaultValue(0),
            'mail_from_text_mobile' => $this->string(255)->notNull()->defaultValue(''),
            'mail_from_address_mobile' => $this->string(32)->defaultValue(null),
            'mail_subject_mobile' => $this->string(255)->notNull()->defaultValue(''),
            'mail_header_mobile' => $this->string(2000)->notNull()->defaultValue(''),
            'mail_footer_mobile' => $this->string(2000)->notNull()->defaultValue(''),
            'max_job_count_pc' => $this->integer(11)->notNull()->defaultValue(20),
            'max_job_count_mobile' => $this->integer(11)->notNull()->defaultValue(5),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_master_id', 'send_mail_master', ['send_mail_master_id']);

        $this->createTable('send_mail_scout_master',[
            'send_mail_scout_master_id' => $this->integer(11)->notNull()->defaultValue(0),
            'mail_from_text_pc' => $this->text()->notNull(),
            'mail_from_address_pc' => $this->string(32)->defaultValue(null),
            'mail_subject_pc' => $this->text()->notNull(),
            'mail_header_pc' => $this->text()->notNull(),
            'mail_footer_pc' => $this->text()->notNull(),
            'mail_from_text_mobile' => $this->text()->notNull(),
            'mail_from_address_mobile' => $this->string(32)->defaultValue(null),
            'mail_subject_mobile' => $this->text()->notNull(),
            'mail_header_mobile' => $this->text()->notNull(),
            'mail_footer_mobile' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('pk_send_mail_scout_master_id', 'send_mail_scout_master', ['send_mail_scout_master_id']);

        $this->createTable('wage_master',[
            'id' => $this->primaryKey(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'wage_type_master_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルwage_type_masterのカラムid"',
            'wage_value_master_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルwage_value_master_idのカラムid"',
            'valid_chk' => $this->smallInteger(1)->defaultValue(1). ' COMMENT "状態"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createTable('wage_type_master',[
            'id' => $this->primaryKey(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'wage_type_name' => $this->string(255)->notNull(). ' COMMENT "給与種別名"',
            'valid_chk' => $this->smallInteger(1)->defaultValue(1). ' COMMENT "状態"',
            'sort' => $this->integer(11)->defaultValue(0). ' COMMENT "表示順"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createTable('wage_value_master',[
            'id' => $this->primaryKey(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'wage_value_name' => $this->string(255)->notNull(). ' COMMENT "給与種別名"',
            'valid_chk' => $this->smallInteger(1)->defaultValue(1). ' COMMENT "状態"',
            'sort' => $this->integer(11)->defaultValue(0). ' COMMENT "表示順"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
    }
}
