<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m170602_095733_insert_to_manage_menu_main
 * 小メニュー「日別アクセス数集計」「ページ別アクセス数確認」追加
 */
class m170602_095733_insert_to_manage_menu_main extends Migration
{

    const MENU_TITLE1 = '日別アクセス数集計';
    const MENU_TITLE2 = 'ページ別アクセス数確認';
    const NUMBER_OF_CATEGORIES = 14;

    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();

        foreach ($tenants as $tenant) {
            $manageMenuCategory = (new Query)->select('id')->from('manage_menu_category')->where([
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_no' => self::NUMBER_OF_CATEGORIES,
            ])->one();

            // ページ別アクセス数確認
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_id' => $manageMenuCategory['id'],
                'title' => Yii::t('app', self::MENU_TITLE2),
                'href' => '/manage/secure/analysis-page/list',
                'valid_chk' => 1,
                'sort' => 2,
                'icon_key' => 'list',
                'permitted_role' => 'client_admin',
                'exception' => '',
            ]);

            // 日別アクセス数集計
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_id' => $manageMenuCategory['id'],
                'title' => Yii::t('app', self::MENU_TITLE1),
                'href' => '/manage/secure/analysis-daily/list',
                'valid_chk' => 1,
                'sort' => 1,
                'icon_key' => 'list',
                'permitted_role' => 'client_admin',
                'exception' => '',
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('manage_menu_main', ['href' => '/manage/secure/analysis-page/list']);
        $this->delete('manage_menu_main', ['href' => '/manage/secure/analysis-daily/list']);
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
