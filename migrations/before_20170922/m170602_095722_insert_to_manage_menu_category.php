<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m170602_095722_insert_to_manage_menu_category
 * 大メニュー「アクセス管理」追加
 */
class m170602_095722_insert_to_manage_menu_category extends Migration
{
    const MENU_TITLE = 'アクセス管理';

    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();

        foreach ($tenants as $tenant) {
            $this->insert('manage_menu_category', [
                'tenant_id' => $tenant['tenant_id'],
                'title' => Yii::t('app', self::MENU_TITLE),
                'sort' => 14,
                'icon_key' => 'signal',
                'valid_chk' => 1,
                'manage_menu_category_no' => 14,
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('manage_menu_category', ['manage_menu_category_no' => 14]);
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
