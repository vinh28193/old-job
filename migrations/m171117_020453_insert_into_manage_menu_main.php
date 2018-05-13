<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m171117_020453_insert_into_manage_menu_main
 */
class m171117_020453_insert_into_manage_menu_main extends Migration
{
    const CATEGORY_NO = 4;
    const BASE_URL = '/manage/secure/free-content/';
    const ROLE = 'owner_admin';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // todo 並び順とアイコンを後で調整
        $this->insertMenu('フリーコンテンツ設定・編集', 'list', 1, 2, 'link');
        $this->insertMenu('フリーコンテンツ登録', 'create', 0, 3, 'link');
        $this->insertMenu('フリーコンテンツ変更', 'update', 0, 4, 'link');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('manage_menu_main', ['href' => '/manage/secure/free-content/list']);
        $this->delete('manage_menu_main', ['href' => '/manage/secure/free-content/create']);
        $this->delete('manage_menu_main', ['href' => '/manage/secure/free-content/update']);
    }

    /**
     * menuを挿入する
     * @param $title
     * @param $href
     * @param $valid
     * @param $sort
     * @param $icon
     */
    public function insertMenu($title, $href, $valid, $sort, $icon)
    {
        $tenantIds = (new Query)->select('tenant_id')->from('tenant')->column();
        /** @var \proseeds\base\console\Tenant $tenantComp */
        $tenantComp = Yii::$app->tenant;

        foreach ($tenantIds as $tenantId) {
            $tenantComp->setTenant($tenantId);
            $categoryId = (new Query)->select('id')->from('manage_menu_category')->where([
                'icon_key' => 'link',
                'tenant_id' => $tenantId,
            ])->scalar();

            // ページ別アクセス数確認
            $this->insert('manage_menu_main', [
                'tenant_id' => $tenantId,
                'manage_menu_category_id' => $categoryId,
                'title' => Yii::t('app', $title),
                'href' => self::BASE_URL . $href,
                'valid_chk' => $valid,
                'sort' => $sort,
                'icon_key' => $icon,
                'permitted_role' => self::ROLE,
                'exception' => '',
            ]);
        }
    }
}
