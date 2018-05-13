<?php

use yii\db\Migration;

class m170413_045702_update_header_footer_records_in_manage_menu_main extends Migration
{
    public function safeUp()
    {
        $this->update('manage_menu_main',
            ['href' => '/manage/secure/settings/header-footer-html/update'],
            ['href' => '/manage/secure/settings/header-footer/update']
        );
    }

    public function safeDown()
    {
        $this->update('manage_menu_main',
            ['href' => '/manage/secure/settings/header-footer/update'],
            ['href' => '/manage/secure/settings/header-footer-html/update']
        );
    }
}
