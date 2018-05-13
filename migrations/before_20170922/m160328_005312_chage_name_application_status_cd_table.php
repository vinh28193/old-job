<?php

use yii\db\Migration;

class m160328_005312_chage_name_application_status_cd_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('application_status_cd', 'application_status_cd', 'application_status_no');
        $this->renameTable('application_status_cd', 'application_status');
        $this->renameColumn('application_master', 'status', 'application_status_id');
    }

    public function safeDown()
    {
        $this->renameTable('application_status', 'application_status_cd');
        $this->renameColumn('application_master', 'application_status_id', 'status');
        $this->renameColumn('application_status_cd', 'application_status_no', 'application_status_cd');
    }
}
