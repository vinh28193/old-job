<?php

use yii\db\Migration;

class m160413_081016_change_name_dist_cd_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('dist_cd', 'dist_cd', 'dist_no');
        $this->renameTable('dist_cd', 'dist');
        $this->renameColumn('job_dist', 'dist_cd_id', 'dist_id');
        $this->renameColumn('pref_dist', 'dist_cd_id', 'dist_id');
    }

    public function safeDown()
    {
        $this->renameTable('dist', 'dist_cd');
        $this->renameColumn('job_dist', 'dist_id', 'dist_cd_id');
        $this->renameColumn('pref_dist', 'dist_id', 'dist_cd_id');
        $this->renameColumn('dist_cd', 'dist_no', 'dist_cd');
    }
}
