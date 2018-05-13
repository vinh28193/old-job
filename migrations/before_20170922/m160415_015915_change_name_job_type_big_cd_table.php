<?php

use yii\db\Migration;

class m160415_015915_change_name_job_type_big_cd_table extends Migration
{
public function safeUp()
{
    $this->renameColumn('job_type_big_cd', 'job_type_big_cd', 'job_type_big_no');
    $this->renameTable('job_type_big_cd', 'job_type_big');
    $this->renameColumn('access_jobtype_ranking', 'job_type_big_cd', 'job_type_big_id');
    $this->renameColumn('client_charge_plan_job_type_big', 'job_type_big_cd', 'job_type_big_id');
    $this->renameColumn('access_log', 'job_type_big_cds', 'job_type_big_id');
}

 public function safeDown()
{
    $this->renameTable('job_type_big', 'job_type_big_cd');
    $this->renameColumn('access_jobtype_ranking', 'job_type_big_id', 'job_type_big_cd');
    $this->renameColumn('client_charge_plan_job_type_big', 'job_type_big_id', 'job_type_big_cd');
    $this->renameColumn('access_log', 'job_type_big_id', 'job_type_big_cds');
    $this->renameColumn('job_type_big_cd', 'job_type_big_no', 'job_type_big_cd');
}
}
