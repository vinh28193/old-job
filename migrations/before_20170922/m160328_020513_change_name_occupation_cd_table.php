<?php

use yii\db\Migration;

class m160328_020513_change_name_occupation_cd_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('occupation_cd', 'occupation_cd', 'occupation_no');
        $this->renameTable('occupation_cd', 'occupation');
        $this->renameColumn('application_master', 'occupation_cd', 'occupation_id');
        $this->renameColumn('job_occupation', 'occupation_cd_id', 'occupation_id');
        $this->renameColumn('member_master', 'occupation_cd', 'occupation_id');
    }

    public function safeDown()
    {
        $this->renameTable('occupation', 'occupation_cd');
        $this->renameColumn('application_master', 'occupation_id', 'occupation_cd');
        $this->renameColumn('job_occupation', 'occupation_id', 'occupation_cd_id');
        $this->renameColumn('member_master', 'occupation_id', 'occupation_cd');
        $this->renameColumn('occupation_cd', 'occupation_no', 'occupation_cd');
    }
}