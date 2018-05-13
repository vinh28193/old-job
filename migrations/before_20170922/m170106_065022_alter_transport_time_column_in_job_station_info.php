<?php

use yii\db\Migration;

class m170106_065022_alter_transport_time_column_in_job_station_info extends Migration
{
    public function up()
    {
        $this->alterColumn('job_station_info', 'transport_time', $this->integer(11)->defaultValue(1)->comment('駅からの所要時間'));
    }

    public function down()
    {
        // transport_timeがnullの場合down出来ないため、0を入れている
        $this->update('job_station_info', ['transport_time' => 0,], ['transport_time' => null,]);
        $this->alterColumn('job_station_info', 'transport_time', $this->integer(11)->defaultValue(1)->notNull()->comment('駅からの所要時間'));
    }
}