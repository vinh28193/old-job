<?php

use yii\db\Migration;

/**
 * Class m160824_085128_alter_column_in_job_master
 * job_masterとjob_master_backupのカラム名をdisp_type_noから
 * disp_type_sortに変更
 */
class m160824_085128_alter_column_in_job_master extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('job_master', 'disp_type_no', 'disp_type_sort');
        $this->addColumn('job_master_backup', 'disp_type_sort', $this->integer(11)->notNull() . ' COMMENT "おすすめ順"');
    }

    public function safeDown()
    {
        $this->dropColumn('job_master_backup', 'disp_type_sort');
        $this->renameColumn('job_master', 'disp_type_sort', 'disp_type_no');
    }
}
