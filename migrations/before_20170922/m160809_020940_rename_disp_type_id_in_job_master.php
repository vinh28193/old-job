<?php

use yii\db\Migration;

/** 
 * Class m160809_020940_rename_disp_type_id_in_job_master
 * job_masterテーブルのdisp_type_idをdisp_type_noに変更する
 */
class m160809_020940_rename_disp_type_id_in_job_master extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('job_master', 'disp_type_id', 'disp_type_no');
    }

    public function safeDown()
    {
        $this->renameColumn('job_master', 'disp_type_no', 'disp_type_id');
    }
}
