<?php

use yii\db\Migration;
use yii\db\Query;

class m170106_033942_insert_search_key_setting_into_manage_menu_main extends Migration
{
    const MENU_TITLE = '検索キー設定';
    const NUMBER_OF_CATEGORIES = 13;

    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_id' => 13 * ($tenant['tenant_id'] - 1) + self::NUMBER_OF_CATEGORIES,
                'title' => self::MENU_TITLE,
                'href' => '/manage/secure/settings/searchkey/list',
                'valid_chk' => 1,
                'sort' => 7,
                'icon_key' => 'wrench',
                'permitted_role' => 'owner_admin',
                'exception' => '',
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('manage_menu_main', [
            'title' => self::MENU_TITLE,
        ]);
    }
}
