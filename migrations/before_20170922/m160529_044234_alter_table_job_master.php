<?php

use yii\db\Migration;

class m160529_044234_alter_table_job_master extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE `job_master` ROW_FORMAT=DYNAMIC;');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE `job_master` ROW_FORMAT=COMPACT;');
    }
}