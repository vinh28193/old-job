<?php

use yii\db\Migration;

/**
 * Class m160818_002423_re_update_in_manage_menu_main
 * ギャラリー機能を掲載企業で使えるようなmigration
 * （m160815_022848_update_in_manage_menu_main）の修正
 */
class m160818_002423_re_update_in_manage_menu_main extends Migration
{
    public function safeUp()
    {
        $this->update('manage_menu_main',['permitted_role' => 'owner_admin'], ['manage_menu_category_id' => '4']);
        $this->update('manage_menu_main',['permitted_role' => 'client_admin'], ['like', 'href', '/manage/secure/media-upload']);
    }

    public function safeDown()
    {
        $this->update('manage_menu_main',['permitted_role' => 'owner_admin'], ['like', 'href', '/manage/secure/media-upload']);
        $this->update('manage_menu_main',['permitted_role' => 'client_admin'], ['manage_menu_category_id' => '4']);
    }
}