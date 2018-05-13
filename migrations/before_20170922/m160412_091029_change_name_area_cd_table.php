<?php

use yii\db\Migration;

class m160412_091029_change_name_area_cd_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('area_cd', 'area_cd', 'area_no');
        $this->renameTable('area_cd', 'area');
        $this->renameColumn('access_jobtype_ranking', 'area_cd', 'area_id');
        $this->renameColumn('access_merit_ranking', 'area_cd', 'area_id');
        $this->renameColumn('pref', 'area_cd_id', 'area_id');
        $this->renameColumn('client_scout_condition', 'area_cd', 'area_id');
        $this->renameColumn('emergency_master', 'area_cd', 'area_id');
        $this->renameColumn('member_condition', 'area_cd', 'area_id');
        $this->renameColumn('member_master', 'area_cd', 'area_id');
        $this->renameColumn('ranking_for_mobile', 'area_cd', 'area_id');
    }

    public function safeDown()
    {
        $this->renameTable('area', 'area_cd');
        $this->renameColumn('access_jobtype_ranking', 'area_id', 'area_cd');
        $this->renameColumn('access_merit_ranking', 'area_id', 'area_cd');
        $this->renameColumn('pref', 'area_id', 'area_cd_id');
        $this->renameColumn('client_scout_condition', 'area_id', 'area_cd');
        $this->renameColumn('emergency_master', 'area_cd', 'area_id');
        $this->renameColumn('member_condition', 'area_id', 'area_cd');
        $this->renameColumn('member_master', 'area_cd', 'area_id');
        $this->renameColumn('ranking_for_mobile', 'area_id', 'area_cd');
        $this->renameColumn('area_cd', 'area_no', 'area_cd');
    }
}
