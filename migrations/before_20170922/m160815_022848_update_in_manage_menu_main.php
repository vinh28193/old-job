<?php

use yii\db\Migration;

/**
 * Class m160815_022848_update_in_manage_menu_main
 * ギャラリー機能を掲載企業で使えるようにした（代理店管理者は、
 * (modules/manage/views/layouts/_side-menu.php)にべたで制限するようにしている。
 */
class m160815_022848_update_in_manage_menu_main extends Migration
{
    public function safeUp()
    {
        $this->update('manage_menu_main',['permitted_role' => 'client_admin'], ['manage_menu_category_id' => '4']);
    }

    public function safeDown()
    {
        $this->update('manage_menu_main',['permitted_role' => 'owner_admin'], ['manage_menu_category_id' => '4']);
    }
}
