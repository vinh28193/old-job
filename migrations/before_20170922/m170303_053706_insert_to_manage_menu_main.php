<?php

use yii\db\Migration;
use yii\db\Query;

class m170303_053706_insert_to_manage_menu_main extends Migration
{
    const MENU_TITLE = 'ヘッダー・フッター設定';
    const NUMBER_OF_CATEGORIES = 13;

    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_id' => 13 + ($tenant['tenant_id'] - 1) * self::NUMBER_OF_CATEGORIES,
                'title' => Yii::t('app', self::MENU_TITLE),
                'href' => '/manage/secure/settings/header-footer/update',
                'valid_chk' => 1,
                'sort' => 8,
                'icon_key' => 'wrench',
                'permitted_role' => 'owner_admin',
                'exception' => '',
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('manage_menu_main', ['href' => '/manage/secure/settings/header-footer/update']);
    }
}
