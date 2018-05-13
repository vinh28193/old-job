<?php

use app\models\manage\ManageMenuCategory;
use yii\db\Migration;
use yii\db\Query;

class m170526_061604_insert_into_manage_menu_main extends Migration
{
    const MENU_LIST_TITLE = '求人詳細画面表示';
    const CATEGORIE_NO = 13;

    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            /** @var ManageMenuCategory $category */
            $category = ManageMenuCategory::find()->where([
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_no' => self::CATEGORIE_NO,
            ])->one();
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenant['tenant_id'],
                'manage_menu_category_id' => $category->id,
                'title' => Yii::t('app', self::MENU_LIST_TITLE),
                'href' => '/manage/secure/settings/display/index',
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
        $this->delete('manage_menu_main', ['href' => '/manage/secure/settings/display/index']);
    }
}
