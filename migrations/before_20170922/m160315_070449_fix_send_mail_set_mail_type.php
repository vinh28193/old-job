<?php

use yii\db\Migration;

/**
 * send_mail_setの仕事メール転送id=1のmail_type修正
 * @author Yukinori Nakamura<y_nakamura@id-frontier.jp>
 */
class m160315_070449_fix_send_mail_set_mail_type extends Migration
{
    public function safeUp()
    {
        $this->update('send_mail_set', ['mail_type' => 'JOB_TRANSFER_MAIL', 'mail_name' => '仕事転送'], ['id' => 1]);
    }

    public function safeDown()
    {
        $this->update('send_mail_set', ['mail_type' => 'APPLY_MAIL', 'mail_name' => '応募通知'], ['id' => 1]);
    }
}
