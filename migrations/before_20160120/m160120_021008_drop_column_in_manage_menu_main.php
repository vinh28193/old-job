<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160120_021008_drop_column_in_manage_menu_main
 * RBACにより不要になったmanage_menu_mainのカラムを削除する
 */
class m160120_021008_drop_column_in_manage_menu_main extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('manage_menu_main', 'corp_available');
        $this->dropColumn('manage_menu_main', 'client_available');
    }

    public function safeDown()
    {
        $this->addColumn('manage_menu_main', 'corp_available', 'SMALLINT(6) DEFAULT \'0\' NOT NULL COMMENT \'代理店閲覧メニュー\'');
        $this->addColumn('manage_menu_main', 'client_available', 'SMALLINT(6) DEFAULT \'0\' NOT NULL COMMENT \'掲載企業閲覧メニュー\'');
        $this->update('manage_menu_main', ['corp_available' => 1], ['manage_menu_main_id' => [1, 3, 4, 6, 18, 19, 45, 47, 58, 59, 61, 62, 63, 71, 79]]);
        $this->update('manage_menu_main', ['client_available' => 1], ['manage_menu_main_id' => [1, 4, 6, 18, 19, 45, 47, 58, 59, 61, 62, 71, 79]]);
    }
}
