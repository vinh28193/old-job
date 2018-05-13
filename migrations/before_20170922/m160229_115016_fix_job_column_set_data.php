<?php

use yii\db\Migration;

/**
 * Class m160229_115016_fix_job_column_set_data
 * job_column_setの固定値部分を修正
 */
class m160229_115016_fix_job_column_set_data extends Migration
{

    public function safeUp()
    {
        $this->update('job_column_set', ['column_name' => 'corpLabel'], ['column_name' => 'corpMaster']);
        $this->update('job_column_set', ['column_name' => 'client_master_id'], ['column_name' => 'clientMaster']);
        $this->update('job_column_set', ['data_type' => 'URL'], ['column_name' => 'map_url']);
        $this->update('job_column_set', ['max_length' => 254], ['data_type' => 'メールアドレス']);
        $this->update('job_column_set', ['max_length' => 2000], ['data_type' => 'URL']);
        $this->update('job_column_set', ['max_length' => null], ['column_name' => [
            'job_no',
            'corp_master_id',
            'client_charge_plan_id',
            'client_master_id',
            'disp_start_date',
            'disp_end_date',
        ]]);
        $this->update('job_column_set', ['is_must' => null], ['column_name' => [
            'job_no',
            'corp_master_id',
            'client_charge_plan_id',
            'client_master_id',
            'disp_start_date',
        ]]);
        $this->update('job_column_set', ['is_in_search' => null], ['column_name' => [
            'corp_master_id',
            'client_charge_plan_id',
            'client_master_id',
            'disp_start_date',
        ]]);
        $this->update('job_column_set', ['freeword_search_flg' => null], ['column_name' => [
            'corp_master_id',
            'client_charge_plan_id',
            'disp_start_date',
            'disp_start_date',
            'map_url',
            'application_mail',
            'mail_body',
        ]]);
    }

    public function safeDown()
    {
    }
}
