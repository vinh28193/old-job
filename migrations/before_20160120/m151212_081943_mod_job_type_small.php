<?php

use yii\db\Schema;
use yii\db\Migration;

class m151212_081943_mod_job_type_small extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE job_type_small CHANGE COLUMN `id` `id` int(10) unsigned NOT NULL;');
        $this->execute('ALTER TABLE job_type_small DROP PRIMARY KEY;');
        $this->addPrimaryKey('pk_job_type_small', 'job_type_small', ['id', 'tenant_id']);
    }

    public function down()
    {
        $this->execute('ALTER TABLE job_type_small CHANGE COLUMN `id` `id` int(10) unsigned NOT NULL;');
        $this->execute('ALTER TABLE job_type_small DROP PRIMARY KEY;');
        $this->addPrimaryKey('pk_job_type_small', 'job_type_small', ['id']);
    }

}
