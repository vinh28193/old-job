<?php

use yii\db\Migration;

class m160413_093141_change_name_station_cd_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('station_cd', 'station_cd', 'station_no');
        $this->renameTable('station_cd', 'station');
        $this->renameColumn('job_station_info', 'station_cd', 'station_id');
    }

    public function safeDown()
    {
        $this->renameTable('station', 'station_cd');
        $this->renameColumn('job_station_info', 'station_id', 'station_cd');
        $this->renameColumn('station_cd', 'station_no', 'station_cd');
    }
}
