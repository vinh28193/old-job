<?php

use yii\db\Migration;

class m161206_060639_drop_unnecessary_tables extends Migration
{
    public function safeUp()
    {
        $this->dropTable('free_contents');
        $this->dropTable('free_contents_photo');
        $this->dropTable('free_contents_text');
        $this->dropTable('free_contents_text_temp');
        $this->dropTable('job_access_disp');
        $this->dropTable('job_application_count');
        $this->dropTable('job_dist_tmp');
        $this->dropTable('job_index_item_set');
        $this->dropTable('job_master_tmp');
        $this->dropTable('job_master_update_tmp');
        $this->dropTable('job_occupation');
        $this->dropTable('job_occupation_tmp');
        $this->dropTable('job_option_column_sub_set');
        $this->dropTable('job_pref_tmp');
        $this->dropTable('job_result_default_sort');
        $this->dropTable('job_station_tmp');
        $this->dropTable('job_study');
        $this->dropTable('job_type_small_tmp');
        $this->dropTable('job_wage_tmp');
    }

    public function safeDown()
    {
        $this->createTable('free_contents',[
            'free_contents_id' => $this->integer(11)->notNull()->defaultValue(0),
            'title' => $this->text()->notNull(),
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'del_chk' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'page_type' =>  $this->smallInteger(6)->notNull()->defaultValue(1),
            'header_img' => $this->text()->notNull(),
            'header_comment' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('free_contents_PKI', 'free_contents', ['free_contents_id']);

        $this->createTable('free_contents_photo',[
            'free_contents_id' => $this->integer(11)->notNull()->defaultValue(0),
            'main_text1' => $this->text()->notNull(),
            'main_text2' => $this->text()->notNull(),
            'main_text3' => $this->text()->notNull(),
            'main_text4' => $this->text()->notNull(),
            'main_text5' => $this->text()->notNull(),
            'main_text6' => $this->text()->notNull(),
            'main_text7' => $this->text()->notNull(),
            'main_text8' => $this->text()->notNull(),
            'main_text9' => $this->text()->notNull(),
            'main_text10' => $this->text()->notNull(),
            'main_text11' => $this->text()->notNull(),
            'main_text12' => $this->text()->notNull(),
            'main_img1' => $this->text()->notNull(),
            'main_img2' => $this->text()->notNull(),
            'main_img3' => $this->text()->notNull(),
            'main_img4' => $this->text()->notNull(),
            'main_img5' => $this->text()->notNull(),
            'main_img6' => $this->text()->notNull(),
            'main_img7' => $this->text()->notNull(),
            'main_img8' => $this->text()->notNull(),
            'main_img9' => $this->text()->notNull(),
            'main_img10' => $this->text()->notNull(),
            'main_img11' => $this->text()->notNull(),
            'main_img12' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('free_contents_photo_PKI', 'free_contents_photo', ['free_contents_id']);

        $this->createTable('free_contents_text',[
            'free_contents_id' => $this->integer(11)->notNull(),
            'main_title' => $this->text()->notNull(),
            'main_text' => $this->text()->notNull(),
            'main_img' => $this->text()->notNull(),
            'main_img_position' =>  $this->smallInteger(6)->defaultValue(null),
            'main_sort' =>  $this->smallInteger(6)->defaultValue(0),
            'free_contents_text_id' => $this->integer(11)->notNull()->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('free_contents_text_PKI', 'free_contents_text', ['free_contents_text_id']);
        $this->createIndex('free_contents_text_free_contents_id_idx', 'free_contents', ['free_contents_id']);

        $this->createTable('free_contents_text_temp',[
            'free_contents_id' => $this->integer(11)->notNull(),
            'main_title' => $this->text()->notNull(),
            'main_text' => $this->text()->notNull(),
            'main_img' => $this->text()->notNull(),
            'main_img_position' =>  $this->smallInteger(6)->defaultValue(null),
            'main_sort' =>  $this->smallInteger(6)->defaultValue(0),
            'free_contents_text_temp_id' => $this->integer(11)->notNull()->defaultValue(0),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('free_contents_text_temp_PKI', 'free_contents_text_temp', ['free_contents_text_temp_id']);

        $this->createTable('job_access_disp',[
            'job_id' => $this->integer(11)->notNull(),
            'access_job_id1' => $this->integer(11)->defaultValue(null),
            'access_job_id2' => $this->integer(11)->defaultValue(null),
            'access_job_id3' => $this->integer(11)->defaultValue(null),
            'access_job_id4' => $this->integer(11)->defaultValue(null),
            'access_job_id5' => $this->integer(11)->defaultValue(null),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createTable('job_application_count',[
            'job_id' => $this->integer(11)->notNull(),
            'application_count' => $this->integer(11)->defaultValue(null),
            'updated_at' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('job_application_count_PKI', 'job_application_count', ['job_id']);

        $this->createTable('job_dist_tmp',[
            'dist_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('job_dist_tmp_PKI', 'job_dist_tmp', ['dist_cd', 'tmp_key', 'tmp_id']);
        $this->createIndex('job_dist_tmp_idx', 'job_dist_tmp', ['tmp_key', 'tmp_id']);

        $this->createTable('job_index_item_set',[
            'job_index_item_set_id' => $this->integer(11)->notNull()->defaultValue(0),
            'new_job_days' => $this->integer(11)->notNull(),
            'job_list_orderby' => $this->text()->notNull(),
            'disp_ranking_top' => $this->text()->notNull(),
            'disp_ranking_list' => $this->text()->notNull(),
            'disp_ranking_disp_type_cds' => $this->text()->notNull(),
            'disp_ranking_list_pict_no' => $this->text()->notNull(),
            'new_icon_disp' => $this->integer(11)->notNull(),
            'hot_job_function_item_id_001' => $this->integer(11)->notNull(),
            'hot_job_function_item_id_002' => $this->integer(11)->notNull(),
            'hot_job_limit_001' => $this->integer(11)->notNull(),
            'hot_job_limit_002' => $this->integer(11)->notNull(),
            'hot_job_limit_mb' => $this->integer(11)->notNull(),
            'hot_limit_pc' => $this->integer(11)->notNull()->defaultValue(12),
            'hot_limit_sm' => $this->integer(11)->notNull()->defaultValue(12),
            'hot_limit_mb' => $this->integer(11)->notNull()->defaultValue(6),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('job_index_item_set_PKI', 'job_index_item_set', ['job_index_item_set_id']);

        $this->createTable('job_master_tmp',[
            'tmp_id' => $this->integer(11)->notNull(),
            'disp_type_cd' => $this->smallInteger(6)->notNull(),
            'client_id' => $this->integer(11)->notNull(),
            'corp_name_disp' => $this->string(255)->notNull()->defaultValue(''),
            'job_pr' => $this->text()->notNull(),
            'main_copy' => $this->text()->notNull(),
            'job_comment' => $this->text()->notNull(),
            'job_pict_0' => $this->text()->notNull(),
            'job_pict_1' => $this->text()->notNull(),
            'job_pict_2' => $this->text()->notNull(),
            'job_pict_3' => $this->text()->notNull(),
            'job_type_text' => $this->text()->notNull(),
            'work_place' => $this->text()->notNull(),
            'station' => $this->text()->notNull(),
            'transport' => $this->text()->notNull(),
            'wage_text' => $this->text()->notNull(),
            'requirement' => $this->text()->notNull(),
            'conditions' => $this->text()->notNull(),
            'holidays' => $this->text()->notNull(),
            'work_period' => $this->text()->notNull(),
            'work_time_text' => $this->text()->notNull(),
            'application' => $this->text()->notNull(),
            'application_tel_1' => $this->text()->notNull(),
            'application_tel_2' => $this->text()->notNull(),
            'application_tel_3' => $this->text()->notNull(),
            'application_mail' => $this->text()->notNull(),
            'application_place' => $this->text()->notNull(),
            'application_staff_name' => $this->text()->notNull(),
            'agent_name' => $this->text()->notNull(),
            'disp_start_date' => $this->integer(11)->notNull(),
            'disp_end_date' => $this->integer(11)->defaultValue(null). ' COMMENT "掲載終了日"',
            'created_at' => $this->integer(11)->notNull(). ' COMMENT "登録日時"',
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'del_chk' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'job_search_number' => $this->text()->notNull(),
            'ranking_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'job_test_process' => $this->text()->notNull(),
            'job_pict_text_1' => $this->text()->notNull(),
            'job_pict_text_2' => $this->text()->notNull(),
            'job_pict_text_3' => $this->text()->notNull(),
            'hot_chk' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'agent_mail' => $this->text()->notNull(),
            'map_url' => $this->text()->notNull(),
            'info_mail_chk' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'corp_mail' => $this->text()->notNull(),
            'mail_body' => $this->text()->notNull(),
            'updated_at' => $this->integer(11)->notNull(). ' COMMENT "更新日時"',
            'job_pict_text_4' => $this->text()->notNull(),
            'job_pict_4' => $this->text()->notNull(),
            'job_pr2' => $this->text()->notNull(),
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
            'job_pict_text_0' => $this->string(255)->notNull()->defaultValue(''),
            'icon_mb_gif' => $this->text()->notNull(),
            'icon_mb_png' => $this->text()->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'main_copy2' => $this->text()->notNull(),
            'application_count' => $this->integer(11)->defaultValue(null),
            'icon_mb_jpg' => $this->text()->notNull(),
            'oiwai_price' => $this->integer(11)->defaultValue(null),
            'map_url_mb' => $this->text()->notNull(),
            'job_id' => $this->integer(11)->defaultValue(null),
            'import_site_job_id' => $this->integer(11)->defaultValue(null),
            'client_charge_type' => $this->text()->notNull(),
            'client_charge_plan_id' => $this->integer(11)->defaultValue(0),
            'medium_application_pc_url' => $this->text()->notNull(),
            'medium_application_mb_url' => $this->text()->notNull(),
            'medium_application_sm_url' => $this->text()->notNull(),
            'manager_memo' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('job_master_tmp_PKI', 'job_master_tmp', ['tmp_id', 'tmp_key']);

        $this->createTable('job_master_update_tmp',[
            'tmp_id' => $this->integer(11)->notNull(),
            'job_id' => $this->integer(11)->notNull(),
            'disp_type_cd' => $this->smallInteger(6)->notNull(),
            'client_id' => $this->integer(11)->notNull(),
            'corp_name_disp' => $this->text()->notNull(),
            'job_pr' => $this->text()->notNull(),
            'main_copy' => $this->text()->notNull(),
            'job_comment' => $this->text()->notNull(),
            'job_pict_0' => $this->text()->notNull(),
            'job_pict_1' => $this->text()->notNull(),
            'job_pict_2' => $this->text()->notNull(),
            'job_pict_3' => $this->text()->notNull(),
            'job_type_text' => $this->text()->notNull(),
            'work_place' => $this->text()->notNull(),
            'station' => $this->text()->notNull(),
            'transport' => $this->text()->notNull(),
            'wage_text' => $this->text()->notNull(),
            'requirement' => $this->text()->notNull(),
            'conditions' => $this->text()->notNull(),
            'holidays' => $this->text()->notNull(),
            'work_period' => $this->text()->notNull(),
            'work_time_text' => $this->text()->notNull(),
            'application' => $this->text()->notNull(),
            'application_tel_1' => $this->text()->notNull(),
            'application_tel_2' => $this->text()->notNull(),
            'application_tel_3' => $this->text()->notNull(),
            'application_mail' => $this->text()->notNull(),
            'application_place' => $this->text()->notNull(),
            'application_staff_name' => $this->text()->notNull(),
            'agent_name' => $this->text()->notNull(),
            'disp_start_date' => $this->integer(11)->notNull(),
            'disp_end_date' => $this->integer(11)->defaultValue(null). ' COMMENT "掲載終了日"',
            'created_at' => $this->integer(11)->notNull(). ' COMMENT "登録日時"',
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'del_chk' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'job_search_number' => $this->text()->notNull(),
            'ranking_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'job_test_process' => $this->text()->notNull(),
            'job_pict_text_1' => $this->text()->notNull(),
            'job_pict_text_2' => $this->text()->notNull(),
            'job_pict_text_3' => $this->text()->notNull(),
            'agent_mail' => $this->text()->notNull(),
            'map_url' => $this->text()->notNull(),
            'info_mail_chk' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'corp_mail' => $this->text()->notNull(),
            'mail_body' => $this->text()->notNull(),
            'updated_at' => $this->integer(11)->notNull(). ' COMMENT "更新日時"',
            'job_pict_text_4' => $this->text()->notNull(),
            'job_pict_4' => $this->text()->notNull(),
            'job_pr2' => $this->text()->notNull(),
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
            'job_pict_text_0' => $this->string(255)->notNull(),
            'icon_mb_gif' => $this->text()->notNull(),
            'icon_mb_png' => $this->text()->notNull(),
            'main_copy2' => $this->text()->notNull(),
            'application_count' => $this->integer(11)->defaultValue(null),
            'icon_mb_jpg' => $this->text()->notNull(),
            'oiwai_price' => $this->integer(11)->defaultValue(null),
            'map_url_mb' => $this->text()->notNull(),
            'client_charge_type' => $this->text()->notNull(),
            'client_charge_plan_id' => $this->integer(11)->defaultValue(0),
            'manager_memo' => $this->text()->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createTable('job_occupation',[
            'id' => $this->primaryKey(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'job_master_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルjob_masterのカラムid"',
            'occupation_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルoccupation_cdのカラムid"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="仕事-属性関連"');
        $this->createIndex('idx_job_occupation_job_master_id', 'job_occupation', ['job_master_id']);

        $this->createTable('job_occupation_tmp',[
            'occupation_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('job_occupation_tmp_PKI', 'job_occupation_tmp', ['occupation_cd', 'tmp_key', 'tmp_id']);
        $this->createIndex('job_occupation_tmp_idx', 'job_occupation_tmp', ['tmp_key', 'tmp_id']);

        $this->createTable('job_option_column_sub_set',[
            'id' => $this->primaryKey(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'column_name' => $this->string(30)->notNull(). ' COMMENT "job_masterのカラム"',
            'name' => $this->string(255)->notNull(). ' COMMENT "オプション項目名"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="求人情報オプションサブ項目管理"');
        $this->createIndex('idx_job_option_column_sub_set', 'job_option_column_sub_set', ['id']);
        $this->createIndex('idx_job_option_column_sub_set_1_tenant_id', 'job_option_column_sub_set', ['tenant_id']);

        $this->createTable('job_pref_tmp',[
            'pref_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('job_pref_tmp_PKI', 'job_pref_tmp', ['pref_cd', 'tmp_key', 'tmp_id']);
        $this->createIndex('job_pref_tmp_idx', 'job_pref_tmp', ['tmp_key', 'tmp_id']);

        $this->createTable('job_result_default_sort',[
            'id' => $this->primaryKey(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'priority' => $this->smallInteger(4)->notNull(). ' COMMENT "優先順位"',
            'item' => $this->string(255)->notNull(). ' COMMENT "項目名(disp_type：掲載タイプ、oiwai：お祝い金、disp_start：掲載開始日、update_time：更新日時)"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="検索結果一覧画面デフォルト並び順"');
        $this->createIndex('idx_job_result_default_sort_1_tenant_id', 'job_result_default_sort', ['id']);

        $this->createTable('job_station_tmp',[
            'station_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('job_station_tmp_PKI', 'job_station_tmp', ['station_cd', 'tmp_key', 'tmp_id']);
        $this->createIndex('job_station_tmp_idx', 'job_station_tmp', ['tmp_key', 'tmp_id']);

        $this->createTable('job_study',[
            'job_id' => $this->integer(11)->notNull(),
            'study_cd' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('job_study_PKI', 'job_study', ['job_id', 'study_cd']);

        $this->createTable('job_type_small_tmp',[
            'job_type_small_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('job_type_small_tmp_PKI', 'job_type_small_tmp', ['job_type_small_cd', 'tmp_key', 'tmp_id']);
        $this->createIndex('job_type_small_tmp_idx', 'job_type_small_tmp', ['tmp_key', 'tmp_id']);

        $this->createTable('job_wage_tmp',[
            'wage_master_id' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('job_wage_tmp_PKI', 'job_wage_tmp', ['wage_master_id', 'tmp_key', 'tmp_id']);
        $this->createIndex('job_wage_tmp_idx', 'job_wage_tmp', ['tmp_key', 'tmp_id']);
    }
}
