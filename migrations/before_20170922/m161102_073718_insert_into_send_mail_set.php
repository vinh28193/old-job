<?php

use yii\db\Migration;
use yii\db\Query;

class m161102_073718_insert_into_send_mail_set extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('send_mail_set', 'id', $this->integer(11) . 'AUTO_INCREMENT COMMENT"主キーID"');

        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $this->insert('send_mail_set', [
                'tenant_id' => $tenant['tenant_id'],
                'from_name' => 'JobMaker2',
                'from_address' => 'JobMaker@JobMaker.jp',
                'subject' => '[SITE_NAME]に掲載の問い合わせがありました',
                'contents' => '下記内容で問い合わせがありました

',
                'default_contents' => '不要',
                'mail_sign' => '--------------------------------------------------------
【[SITE_NAME]　運営事務局】
[SITE_URL]
--------------------------------------------------------',
                'mail_to' => 0,
                'valid_chk' => 1,
                'mail_name' => '掲載の問い合わせ',
                'sort' => 9,
                'mail_type' => 'INQUIRY_MAIL',
                'mail_type_id' => 10,
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('manage_menu_main', ['href' => '/manage/secure/option-inquiry/list']);
    }
}
