<?php

use app\models\manage\ManageMenuMain;
use yii\db\Migration;
use yii\db\Query;

class m161102_025932_insert_into_manage_menu_main extends Migration
{
    const MENU_TITLE = '掲載問い合わせ管理';
    const NUMBER_OF_CATEGORIES = 13;

    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_id' => 11 + ($tenant['tenant_id'] - 1) * self::NUMBER_OF_CATEGORIES,
                'manage_menu_main_id' => 80,
                'title' => self::MENU_TITLE,
                'href' => '/manage/secure/option-inquiry/list',
                'valid_chk' => 1,
                'sort' => 8,
                'icon_key' => 'pencil',
                'permitted_role' => 'owner_admin',
                'exception' => 'optionInquiryException',
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('manage_menu_main', ['href' => '/manage/secure/option-inquiry/list']);
    }
}
