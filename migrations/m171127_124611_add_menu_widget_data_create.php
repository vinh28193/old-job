<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m171127_124611_add_menu_widget_data_create
 */
class m171127_124611_add_menu_widget_data_create extends Migration
{
    const MENU_TITLE = 'ウィジェットデータ登録';
    const NUMBER_OF_CATEGORIES = 4;

    /**
     * Up
     */
    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_id' => 13 * ($tenant['tenant_id'] - 1) + self::NUMBER_OF_CATEGORIES,
                'title' => self::MENU_TITLE,
                'href' => '/manage/secure/widget-data/create',
                'valid_chk' => 0,
                'sort' => 4,
                'icon_key' => 'wrench',
                'permitted_role' => 'owner_admin',
                'exception' => '',
            ]);
        }
    }

    /**
     * Down
     */
    public function safeDown()
    {
        $this->delete('manage_menu_main', [
            'title' => self::MENU_TITLE,
        ]);
    }
}
