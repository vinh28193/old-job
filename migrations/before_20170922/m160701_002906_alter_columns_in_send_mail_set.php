<?php

use yii\db\Migration;

class m160701_002906_alter_columns_in_send_mail_set extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('send_mail_set', 'id', $this->integer(11)->notNull() . ' COMMENT"主キーID"');
        $this->alterColumn('send_mail_set', 'tenant_id', $this->integer(11)->notNull() . ' COMMENT"テナントID"');
        $this->alterColumn('send_mail_set', 'from_name', $this->string(255)->notNull() . ' COMMENT"差出人名"');
        $this->alterColumn('send_mail_set', 'from_address', $this->string(255)->notNull() . ' COMMENT"差出人メールアドレス"');
        $this->alterColumn('send_mail_set', 'subject', $this->string(255)->notNull() . ' COMMENT"件名"');
        $this->alterColumn('send_mail_set', 'contents', $this->string(2000)->notNull() . ' COMMENT"メール文面"');
        $this->alterColumn('send_mail_set', 'default_contents', $this->string(4000)->notNull() . ' COMMENT"メール文面（初期値）"');
        $this->alterColumn('send_mail_set', 'mail_sign', $this->string(1000)->notNull() . ' COMMENT"署名"');
        $this->alterColumn('send_mail_set', 'mail_to', 'TINYINT(3) UNSIGNED NOT NULL COMMENT"対象者"');
        $this->alterColumn('send_mail_set', 'valid_chk', $this->boolean()->notNull()->defaultValue(1) . ' COMMENT"状態"');
        $this->alterColumn('send_mail_set', 'mail_name', $this->string(20)->notNull() . ' COMMENT"メール名称"');
        $this->alterColumn('send_mail_set', 'sort', 'TINYINT UNSIGNED DEFAULT 0 COMMENT"表示順"');
        $this->alterColumn('send_mail_set', 'mail_type', $this->string(20)->notNull() . ' COMMENT"メール種別"');
    }

    public function safeDown()
    {
        $this->alterColumn('send_mail_set', 'id', $this->bigInteger()->notNull() . ' COMMENT"主キーID"');
        $this->alterColumn('send_mail_set', 'tenant_id', $this->integer(20)->notNull() . ' COMMENT"テナントID"');
        $this->alterColumn('send_mail_set', 'from_name', $this->string(200)->notNull() . ' COMMENT"差出人名"');
        $this->alterColumn('send_mail_set', 'from_address', $this->string(1000)->notNull() . ' COMMENT"差出人メールアドレス"');
        $this->alterColumn('send_mail_set', 'subject', $this->string(1000)->notNull() . ' COMMENT"件名"');
        $this->alterColumn('send_mail_set', 'contents', $this->text()->notNull() . ' COMMENT"メール文面"');
        $this->alterColumn('send_mail_set', 'default_contents', $this->text()->notNull() . ' COMMENT"メール文面（初期値）"');
        $this->alterColumn('send_mail_set', 'mail_sign', $this->text()->notNull() . ' COMMENT"署名"');
        $this->alterColumn('send_mail_set', 'mail_to', 'TINYINT(3) UNSIGNED COMMENT"対象者"');
        $this->alterColumn('send_mail_set', 'valid_chk', 'TINYINT COMMENT"状態"');
        $this->alterColumn('send_mail_set', 'mail_name', $this->string(200)->notNull() . ' COMMENT"メール名称"');
        $this->alterColumn('send_mail_set', 'sort', $this->smallInteger(6)->notNull() . ' COMMENT"表示順"');
        $this->alterColumn('send_mail_set', 'mail_type', $this->string(20) . ' COMMENT"メール種別"');
    }
}