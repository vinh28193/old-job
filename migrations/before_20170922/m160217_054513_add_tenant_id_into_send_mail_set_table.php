<?php

use yii\db\Schema;
use yii\db\Migration;

class m160217_054513_add_tenant_id_into_send_mail_set_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('send_mail_set', 'tenant_id', 'int(20) NOT NULL COMMENT \'テナント\' AFTER send_mail_set_id');
        $this->update('send_mail_set',['tenant_id'=>1],['send_mail_set_id'=>1]);
        $this->update('send_mail_set',['tenant_id'=>1],['send_mail_set_id'=>2]);
        $this->update('send_mail_set',['tenant_id'=>1],['send_mail_set_id'=>3]);
        $this->update('send_mail_set',['tenant_id'=>1],['send_mail_set_id'=>4]);
        $this->update('send_mail_set',['tenant_id'=>1],['send_mail_set_id'=>5]);
        $this->update('send_mail_set',['tenant_id'=>1],['send_mail_set_id'=>6]);
    }

    public function safeDown()
    {
        $this->dropColumn('send_mail_set', 'tenant_id');
    }
}
