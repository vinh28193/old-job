<?php

use yii\db\Migration;
use yii\db\Query;

class m160301_033753_fix_send_mail_set_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('send_mail_set', 'mail_type', 'mail_to');
        $this->alterColumn('send_mail_set', 'mail_to', 'TINYINT UNSIGNED comment \'対象者(0=求職者,1=運営元,2=代理店,3=掲載企業)\' ');
        $this->addColumn('send_mail_set', 'mail_type', $this->string(20)->defaultValue('').' comment \'メール種別名(名称変更不可)\' ');
        $this->update('send_mail_set',['mail_type'=>'APPLY_MAIL'],['id'=>1]);
        $this->update('send_mail_set',['mail_type'=>'ADMIN_MAIL'],['id'=>2]);
        $this->update('send_mail_set',['mail_type'=>'APPLY_MAIL'],['id'=>3]);
        $this->update('send_mail_set',['mail_type'=>'APPLY_MAIL'],['id'=>4]);
        $this->update('send_mail_set',['mail_type'=>'MEMBER_MAIL'],['id'=>5]);
        $this->update('send_mail_set',['mail_type'=>'MEMBER_MAIL'],['id'=>6]);

    }

    public function safeDown()
    {
        $this->dropColumn('send_mail_set', 'mail_type');
        $this->alterColumn('send_mail_set', 'mail_to', 'TINYINT default 0');
    	$this->renameColumn('send_mail_set', 'mail_to', 'mail_type');
    }
}
