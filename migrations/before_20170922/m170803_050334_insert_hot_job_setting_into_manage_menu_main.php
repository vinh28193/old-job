<?php

use yii\db\Migration;
use yii\db\Query;

class m170803_050334_insert_hot_job_setting_into_manage_menu_main extends Migration
{
    const MENU_TITLE = '注目情報設定';
    const NUMBER_OF_CATEGORIES = 13;

    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_id' => $tenant['tenant_id'] * self::NUMBER_OF_CATEGORIES,
                'title' => Yii::t('app', self::MENU_TITLE),
                'href' => '/manage/secure/settings/hot-job/update',
                'valid_chk' => 1,
                'sort' => 10,
                'icon_key' => 'wrench',
                'permitted_role' => 'owner_admin',
                'exception' => '',
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('manage_menu_main', ['href' => '/manage/secure/settings/hot-job/update']);
    }

}
