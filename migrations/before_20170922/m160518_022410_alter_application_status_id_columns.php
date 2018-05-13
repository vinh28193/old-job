<?php

use yii\db\Migration;

class m160518_022410_alter_application_status_id_columns extends Migration
{

    public function safeUp()
    {
        $this->alterColumn('application_master', 'application_status_id', 'TINYINT(4) DEFAULT 0  COMMENT "採用状況"');
        $this->alterColumn('application_master_backup', 'application_status_id', 'TINYINT(4) DEFAULT 0  COMMENT "採用状況"');
    }

    public function safeDown()
    {
        $this->alterColumn('application_master', 'application_status_id', 'TINYINT(4)  COMMENT "採用状況"');
        $this->alterColumn('application_master_backup', 'application_status_id', 'TINYINT(4)  COMMENT "採用状況"');
    }
}