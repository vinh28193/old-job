<?php

use yii\db\Migration;

class m160727_103048_add_mail_type_id_to_send_mail_set extends Migration
{
    public function up()
    {
        $this->addColumn('send_mail_set', 'mail_type_id', 'TINYINT NOT NULL COMMENT"メールのタイプ"');
    }

    public function down()
    {
        $this->dropColumn('send_mail_set', 'mail_type_id');
    }
}
