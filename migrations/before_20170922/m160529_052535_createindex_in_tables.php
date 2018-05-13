<?php

use yii\db\Migration;

class m160529_052535_createindex_in_tables extends Migration
{
    public function safeUp()
    {
        $this->createIndex('idx_application_response_log_1_tenant_id', 'application_response_log', ['tenant_id']);
        $this->createIndex('idx_application_status_1_tenant_id', 'application_status', ['tenant_id']);
        $this->createIndex('idx_area_1_tenant_id', 'area', ['tenant_id']);
        $this->createIndex('idx_client_disp_1_tenant_id', 'client_disp', ['tenant_id']);
        $this->createIndex('idx_disp_type_1_tenant_id', 'disp_type', ['tenant_id']);
        $this->createIndex('idx_dist_1_tenant_id', 'dist', ['tenant_id']);
        $this->createIndex('idx_employment_type_cd_1_tenant_id', 'employment_type_cd', ['tenant_id']);
        $this->createIndex('idx_job_access_recommend_1_tenant_id', 'job_access_recommend', ['tenant_id']);
        $this->createIndex('idx_job_option_column_sub_set_1_tenant_id', 'job_option_column_sub_set', ['tenant_id']);
        $this->createIndex('idx_job_result_default_sort_1_tenant_id', 'job_result_default_sort', ['tenant_id']);
        $this->createIndex('idx_job_short_item_disp_1_tenant_id', 'job_short_item_disp', ['tenant_id']);
        $this->createIndex('idx_job_short_item_disp_result_1_tenant_id', 'job_short_item_disp_result', ['tenant_id']);
        $this->createIndex('idx_job_type_big_1_tenant_id', 'job_type_big', ['tenant_id']);
        $this->createIndex('idx_job_type_category_1_tenant_id', 'job_type_category', ['tenant_id']);
        $this->createIndex('idx_job_type_small_1_tenant_id', 'job_type_small', ['tenant_id']);
        $this->createIndex('idx_list_disp_1_tenant_id', 'list_disp', ['tenant_id']);
        $this->createIndex('idx_main_disp_1_tenant_id', 'main_disp', ['tenant_id']);
        $this->createIndex('idx_media_upload_1_tenant_id', 'media_upload', ['tenant_id']);
        $this->createIndex('idx_merit_category_cd_1_tenant_id', 'merit_category_cd', ['tenant_id']);
        $this->createIndex('idx_merit_cd_1_tenant_id', 'merit_cd', ['tenant_id']);
        $this->createIndex('idx_occupation_1_tenant_id', 'occupation', ['tenant_id']);
        $this->createIndex('idx_option_search_category_cd_1_tenant_id', 'option_search_category_cd', ['tenant_id']);
        $this->createIndex('idx_option_search_cd_1_tenant_id', 'option_search_cd', ['tenant_id']);
        $this->createIndex('idx_policy_1_tenant_id', 'policy', ['tenant_id']);
        $this->createIndex('idx_pref_1_tenant_id', 'pref', ['tenant_id']);
        $this->createIndex('idx_pref_dist_1_tenant_id', 'pref_dist', ['tenant_id']);
        $this->createIndex('idx_pref_dist_master_1_tenant_id', 'pref_dist_master', ['tenant_id']);
        $this->createIndex('idx_send_mail_set_1_tenant_id', 'send_mail_set', ['tenant_id']);
        $this->createIndex('idx_social_button_1_tenant_id', 'social_button', ['tenant_id']);
        $this->createIndex('idx_wage_master_1_tenant_id', 'wage_master', ['tenant_id']);
        $this->createIndex('idx_wage_type_master_1_tenant_id', 'wage_type_master', ['tenant_id']);
        $this->createIndex('idx_wage_value_master_1_tenant_id', 'wage_value_master', ['tenant_id']);
        $this->createIndex('idx_widget_1_tenant_id', 'widget', ['tenant_id']);
        $this->createIndex('idx_widget_data_1_tenant_id', 'widget_data', ['tenant_id']);
        $this->createIndex('idx_widget_layout_1_tenant_id', 'widget_layout', ['tenant_id']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_application_response_log_1_tenant_id', 'application_response_log');
        $this->dropIndex('idx_application_status_1_tenant_id', 'application_status');
        $this->dropIndex('idx_area_1_tenant_id', 'area');
        $this->dropIndex('idx_client_disp_1_tenant_id', 'client_disp');
        $this->dropIndex('idx_disp_type_1_tenant_id', 'disp_type');
        $this->dropIndex('idx_dist_1_tenant_id', 'dist');
        $this->dropIndex('idx_employment_type_cd_1_tenant_id', 'employment_type_cd');
        $this->dropIndex('idx_job_access_recommend_1_tenant_id', 'job_access_recommend');
        $this->dropIndex('idx_job_option_column_sub_set_1_tenant_id', 'job_option_column_sub_set');
        $this->dropIndex('idx_job_result_default_sort_1_tenant_id', 'job_result_default_sort');
        $this->dropIndex('idx_job_short_item_disp_1_tenant_id', 'job_short_item_disp');
        $this->dropIndex('idx_job_short_item_disp_result_1_tenant_id', 'job_short_item_disp_result');
        $this->dropIndex('idx_job_type_big_1_tenant_id', 'job_type_big');
        $this->dropIndex('idx_job_type_category_1_tenant_id', 'job_type_category');
        $this->dropIndex('idx_job_type_small_1_tenant_id', 'job_type_small');
        $this->dropIndex('idx_list_disp_1_tenant_id', 'list_disp');
        $this->dropIndex('idx_main_disp_1_tenant_id', 'main_disp');
        $this->dropIndex('idx_media_upload_1_tenant_id', 'media_upload');
        $this->dropIndex('idx_merit_category_cd_1_tenant_id', 'merit_category_cd');
        $this->dropIndex('idx_merit_cd_1_tenant_id', 'merit_cd');
        $this->dropIndex('idx_occupation_1_tenant_id', 'occupation');
        $this->dropIndex('idx_option_search_category_cd_1_tenant_id', 'option_search_category_cd');
        $this->dropIndex('idx_option_search_cd_1_tenant_id', 'option_search_cd');
        $this->dropIndex('idx_policy_1_tenant_id', 'policy');
        $this->dropIndex('idx_pref_1_tenant_id', 'pref');
        $this->dropIndex('idx_pref_dist_1_tenant_id', 'pref_dist');
        $this->dropIndex('idx_pref_dist_master_1_tenant_id', 'pref_dist_master');
        $this->dropIndex('idx_send_mail_set_1_tenant_id', 'send_mail_set');
        $this->dropIndex('idx_social_button_1_tenant_id', 'social_button');
        $this->dropIndex('idx_wage_master_1_tenant_id', 'wage_master');
        $this->dropIndex('idx_wage_type_master_1_tenant_id', 'wage_type_master');
        $this->dropIndex('idx_wage_value_master_1_tenant_id', 'wage_value_master');
        $this->dropIndex('idx_widget_1_tenant_id', 'widget');
        $this->dropIndex('idx_widget_data_1_tenant_id', 'widget_data');
        $this->dropIndex('idx_widget_layout_1_tenant_id', 'widget_layout');
    }
}