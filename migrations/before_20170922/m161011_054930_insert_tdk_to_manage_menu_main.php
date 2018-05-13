<?php

use yii\db\Migration;
use yii\db\Query;

class m161011_054930_insert_tdk_to_manage_menu_main extends Migration
{
    const MENU_TITLE = 'TDK管理';
    const NUMBER_OF_CATEGORIES = 13;

    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_id' => 13 + ($tenant['tenant_id'] - 1) * self::NUMBER_OF_CATEGORIES,
                'manage_menu_main_id' => 1,
                'title' => self::MENU_TITLE,
                'href' => '/manage/secure/settings/tool-master/index',
                'valid_chk' => 1,
                'sort' => 7,
                'icon_key' => 'wrench',
                'permitted_role' => 'owner_admin',
                'exception' => '',
            ]);
        }

        $records = (new Query)->select('id')->from('manage_menu_main')->where(['title' => self::MENU_TITLE])->all();
        foreach ($records as $record) {
            $id = $record['id'];
            $this->update('manage_menu_main',['manage_menu_main_id' => $id], ['id' => $id]);
        }
    }

    public function safeDown()
    {
        $sql = sprintf('DELETE FROM manage_menu_main WHERE title = "%s"', self::MENU_TITLE);
        $this->execute($sql);
    }
}
