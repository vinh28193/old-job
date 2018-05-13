<?php

use yii\db\Migration;

class m160412_060912_change_name_pref_cd_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('pref_cd', 'pref_cd', 'pref_no');
        $this->renameTable('pref_cd', 'pref');
        $this->renameColumn('application_master', 'pref_cd', 'pref_id');
        $this->renameColumn('dist_cd', 'pref_cd_id', 'pref_id');
        $this->renameColumn('pref_dist_master', 'pref_cd_id', 'pref_id');
        $this->renameColumn('job_pref', 'pref_cd', 'pref_id');
        $this->renameColumn('member_resume', 'pref_cd', 'pref_id');
        $this->renameColumn('station_cd', 'pref_cd', 'pref_id');
        $this->renameColumn('access_log', 'pref_cds', 'pref_id');
    }

    public function safeDown()
    {
        $this->renameTable('pref', 'pref_cd');
        $this->renameColumn('application_master', 'pref_id', 'pref_cd');
        $this->renameColumn('dist_cd', 'pref_id', 'pref_cd_id');
        $this->renameColumn('pref_dist_master', 'pref_id', 'pref_cd_id');
        $this->renameColumn('job_pref', 'pref_id', 'pref_cd');
        $this->renameColumn('member_resume', 'pref_id', 'pref_cd');
        $this->renameColumn('station_cd', 'pref_id', 'pref_cd');
        $this->renameColumn('access_log', 'pref_id', 'pref_cds');
        $this->renameColumn('pref_cd', 'pref_no', 'pref_cd');
    }
}
