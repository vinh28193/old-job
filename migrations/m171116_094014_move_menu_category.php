<?php

use app\models\manage\ManageMenuCategory;
use app\models\manage\ManageMenuMain;
use yii\db\Migration;

/**
 * Class m171116_094014_move_menu_category
 */
class m171116_094014_move_menu_category extends Migration
{
    /**
     * @var string
     */
    private $_topLayoutUrl = '/manage/secure/widget/index';
    /**
     * @var int
     */
    private $_topLayoutOld = 4;
    /**
     * @var int
     */
    private $_topLayoutNew = 13;

    /**
     * @var string
     */
    private $_widgetListUrl = '/manage/secure/widget-data/list';
    /**
     * @var string
     */
    private $_widgetAddUrl = '/manage/secure/widget-data/create';
    /**
     * @var string
     */
    private $_mainVisualUrl = '/manage/secure/main-visual/form';


    /**
     * Up
     */
    public function safeUp()
    {
        // テナントリストの取得
        /** @var \proseeds\models\Tenant[] $tenants */
        $tenants = \proseeds\models\Tenant::find()->all();
        // TOPレイアウトの移動
        foreach ($tenants as $tenant) {
            /** @var ManageMenuCategory $menuCategory */
            $menuCategory = ManageMenuCategory::find()->where([
                'tenant_id' => $tenant->tenant_id,
                'manage_menu_category_no' => $this->_topLayoutNew,
            ])->one();

            /** @var ManageMenuMain $menu */
            $menu = ManageMenuMain::find()->where([
                'tenant_id' => $tenant->tenant_id,
                'href' => $this->_topLayoutUrl
            ])->one();
            $menu->manage_menu_category_id = $menuCategory->id;
            if (!$menu->save()) {
                return false;
            }
        }

        ManageMenuCategory::updateAll([
            'title' => 'コンテンツ設定',
        ], [
            'manage_menu_category_no' => $this->_topLayoutOld,
        ]);
        ManageMenuMain::updateAll([
            'title' => 'ウィジェットデータ設定・編集',
        ], [
            'href' => $this->_widgetListUrl,
        ]);
        ManageMenuMain::updateAll([
            'title' => 'メインビジュアル設定・編集',
            'icon_key' => 'picture',
            'href' => $this->_mainVisualUrl,
            'sort' => 1,
        ], [
            'href' => $this->_widgetAddUrl,
        ]);

        return true;
    }

    /**
     * Down
     */
    public function safeDown()
    {
        // テナントリストの取得
        /** @var \proseeds\models\Tenant[] $tenants */
        $tenants = \proseeds\models\Tenant::find()->all();
        // TOPレイアウトの移動
        foreach ($tenants as $tenant) {
            /** @var ManageMenuCategory $menuCategory */
            $menuCategory = ManageMenuCategory::find()->where([
                'tenant_id' => $tenant->tenant_id,
                'manage_menu_category_no' => $this->_topLayoutOld,
            ])->one();

            /** @var ManageMenuMain $menu */
            $menu = ManageMenuMain::find()->where([
                'tenant_id' => $tenant->tenant_id,
                'href' => $this->_topLayoutUrl,
            ])->one();
            $menu->manage_menu_category_id = $menuCategory->id;
            if (!$menu->save()) {
                return false;
            }
        }

        ManageMenuCategory::updateAll([
            'title' => 'ウィジェットデータ一覧',
        ], [
            'manage_menu_category_no' => $this->_topLayoutOld,
        ]);
        ManageMenuMain::updateAll([
            'title' => 'ウィジェットデータ一覧',
            'href' => $this->_widgetAddUrl,
        ], [
            'href' => $this->_mainVisualUrl,
        ]);
        ManageMenuMain::updateAll([
            'href' => $this->_widgetAddUrl,
            'icon_key' => 'plus',
            'sort' => 3,
        ], [
            'href' => $this->_mainVisualUrl,
        ]);

        return true;
    }
}
