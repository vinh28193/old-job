<?php

use yii\db\Migration;

class m160414_091553_change_name_job_type_small_cd_table extends Migration
{
public function safeUp()
{
    $this->renameColumn('job_type_small_cd', 'job_type_small_cd', 'job_type_small_no');
    $this->renameColumn('job_type_small', 'job_type_small_cd_id', 'job_type_small_id');
    $this->renameTable('job_type_small', 'job_type');
    $this->renameTable('job_type_small_cd', 'job_type_small');
    $this->renameColumn('job_type_small', 'job_type_big_cd_id', 'job_type_big_id');
    $this->renameColumn('access_log', 'job_type_small_cds', 'job_type_small_id');
    
}

public function safeDown()
{
    $this->renameColumn('job_type_small', 'job_type_big_cd_id', 'job_type_big_id');
    $this->renameTable('job_type_small', 'job_type_small_cd');
    $this->renameTable('job_type', 'job_type_small');
    $this->renameColumn('job_type_small', 'job_type_small_id', 'job_type_small_cd_id');
    $this->renameColumn('access_log', 'job_type_small_id', 'job_type_small_cds');
    $this->renameColumn('job_type_small_cd', 'job_type_small_no', 'job_type_small_cd');
}
}