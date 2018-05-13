<?php

use yii\db\Migration;

class m160225_084457_rename_id_column_in_send_mail_set_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('send_mail_set', 'send_mail_set_id', 'id');
    }

    public function safeDown()
    {
        $this->renameColumn('send_mail_set', 'id', 'send_mail_set_id');
    }
}
