<?php

use yii\db\Schema;
use yii\db\Migration;

class m151215_063453_add_auto_inc_to_job_type_small extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE jm2.job_type_small MODIFY id int NOT NULL AUTO_INCREMENT COMMENT \'ID\';');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE jm2.job_type_small MODIFY id int NOT NULL COMMENT \'ID\';');
    }
}
