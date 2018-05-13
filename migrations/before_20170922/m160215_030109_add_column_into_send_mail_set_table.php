<?php

use yii\db\Schema;
use yii\db\Migration;

class m160215_030109_add_column_into_send_mail_set_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('send_mail_set', 'mail_name', $this->string(200)->notNull().' COMMENT \'メール種別\' ');
        $this->addColumn('send_mail_set', 'sort', $this->smallInteger()->notNull()->defaultValue(0).' COMMENT \'表示順\'');
        $this->update('send_mail_set',['mail_name'=>'応募通知','sort'=>1],['send_mail_set_id'=>1]);
        $this->update('send_mail_set',['mail_name'=>'管理者登録通知','sort'=>2],['send_mail_set_id'=>2]);
        $this->update('send_mail_set',['mail_name'=>'応募通知','sort'=>3],['send_mail_set_id'=>3]);
        $this->update('send_mail_set',['mail_name'=>'応募通知','sort'=>4],['send_mail_set_id'=>4]);
        $this->update('send_mail_set',['mail_name'=>'会員登録通知','sort'=>5],['send_mail_set_id'=>5]);
        $this->update('send_mail_set',['mail_name'=>'会員登録通知','sort'=>6],['send_mail_set_id'=>6]);
    }

    public function safeDown()
    {
        $this->dropColumn('send_mail_set', 'mail_name');
        $this->dropColumn('send_mail_set', 'sort');
    }
}
