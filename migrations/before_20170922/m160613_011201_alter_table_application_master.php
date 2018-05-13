<?php

use yii\db\Migration;

class m160613_011201_alter_table_application_master extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE `application_master` ROW_FORMAT=DYNAMIC;');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE `application_master` ROW_FORMAT=COMPACT;');
    }
}
