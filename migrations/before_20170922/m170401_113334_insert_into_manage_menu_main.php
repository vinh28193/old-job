<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m170401_113334_insert_into_manage_menu_main
 * 管理画面 > サイト設定 > 初期設定 の項目に カスタムフィールド設定 追加
 */
class m170401_113334_insert_into_manage_menu_main extends Migration
{
    const NUMBER_OF_CATEGORIES = 13;
    const MENU_TITLE = 'カスタムフィールド設定';
    const CUSTOM_FIELD_HREF = '/manage/secure/settings/custom-field/list';

    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_id' => 13 + ($tenant['tenant_id'] - 1) * self::NUMBER_OF_CATEGORIES,
                'title' => self::MENU_TITLE,
                'href' => self::CUSTOM_FIELD_HREF,
                'valid_chk' => 1,
                'sort' => 9,
                'icon_key' => 'wrench',
                'permitted_role' => 'owner_admin',
                'exception' => '',
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('manage_menu_main', ['href' => self::CUSTOM_FIELD_HREF]);
    }
}
