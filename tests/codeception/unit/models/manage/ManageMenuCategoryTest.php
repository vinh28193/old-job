<?php

namespace models\manage;

use app\models\manage\ManageMenuCategory;
use tests\codeception\unit\fixtures\AdminMasterFixture;
use tests\codeception\unit\fixtures\AuthAssignmentFixture;
use tests\codeception\unit\fixtures\AuthItemChildFixture;
use tests\codeception\unit\fixtures\AuthRuleFixture;
use tests\codeception\fixtures\ManageMenuCategoryFixture;
use tests\codeception\fixtures\ManageMenuMainFixture;
use Yii;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;
use yii\rbac\DbManager;

/**
 * @group job_relations
 */
class ManageMenuCategoryTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(ManageMenuCategory::tableName())->equals('manage_menu_category');
    }
    
    /**
     * ラベル設定テスト
     */
    public function testAttributeLabels()
    {
        $model = new ManageMenuCategory();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * rulesテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new ManageMenuCategory();
            $model->validate();
            verify($model->hasErrors('id'))->true();
            verify($model->hasErrors('title'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new ManageMenuCategory();
            $model->load([
                'id' => '文字列',
                'valid_chk' => '文字列',
                'sort' => '文字列',
            ], '');
            $model->validate();
            verify($model->hasErrors('id'))->true();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('sort'))->true();
        });
        $this->specify('文字列チェック', function () {
            $model = new ManageMenuCategory();
            $model->load([
                'title' => 1,
                'icon_key' => 1,
            ], '');
            $model->validate();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('icon_key'))->true();
        });
        $this->specify('最大値チェック', function () {
            $model = new ManageMenuCategory();
            $string = '';
            for ($i = 1; $i <= 256; $i++) {
                $string .= 'a';
            }
            $model->load(['icon_key' => $string], '');
            $model->validate();
            verify($model->hasErrors('icon_key'))->true();
        });
        $this->specify('ユニークチェック', function () {
            $model = new ManageMenuCategory();
            $model->load(['id' => $this->id(1, 'manage_menu_category')], '');
            $model->validate();
            verify($model->hasErrors('id'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new ManageMenuCategory();
            $string = '';
            for ($i = 1; $i <= 255; $i++) {
                $string .= 'a';
            }
            $model->load([
                'id' => 10000,
                'title' => '文字列',
                'valid_chk' => 1,
                'sort' => 1,
                'icon_key' => $string
            ], '');
            verify($model->validate())->true();
        });
    }

    /**
     * getMyMenuのtest（運営元権限）
     */
    public function testGetMyMenuByOwner()
    {
        $this->setIdentity('owner_admin');
        $adminId = $this->getIdentity()->id;
        $auth = new DbManager();
        // 検証値を生成
        $validItems = array_filter(self::getFixtureInstance('manage_menu_main')->data(), function ($record) {
            return $record['valid_chk'] == 1 && $record['tenant_id'] == Yii::$app->tenant->id;
        });
        $validCategories = array_filter(self::getFixtureInstance('manage_menu_category')->data(), function ($record) {
            return $record['valid_chk'] == 1 && $record['tenant_id'] == Yii::$app->tenant->id;
        });
        // 全てのメニューの数をチェック
        $myMenu = ManageMenuCategory::getMyMenu();
        verify($myMenu)->count(count($validCategories));
        verify($this->countChildMenus($myMenu))->equals(count($validItems));
        // 運営元権限のメニューの数をチェック
        $myMenu = ManageMenuCategory::getMyMenu($adminId);
        verify($myMenu)->notEmpty();
        verify($myMenu)->count(count($validCategories));
        // 求人原稿関連の除外権限を割り当てて数をチェック
        $auth->assign($auth->getPermission('jobListException'), $adminId);
        $auth->assign($auth->getPermission('jobCreateException'), $adminId);
        $myMenu = ManageMenuCategory::getMyMenu($adminId);
        verify($myMenu)->notEmpty();
        verify($myMenu)->count(count($validCategories) - 1);
        verify($this->countChildMenus($myMenu))->equals(count($validItems) - 2);

        // レコードを元に戻す
        self::getFixtureInstance('auth_assignment')->load();
    }
    /** getMyMenuのtest（代理店権限） */
    public function testGetMyMenuByCorp()
    {
        $this->setIdentity('corp_admin');
        $adminId = $this->getIdentity()->id;
        $auth = new DbManager();
        // 検証値を生成
        $validItems = array_filter(self::getFixtureInstance('manage_menu_main')->data(), function ($record) {
            return $record['valid_chk'] == 1
            && $record['tenant_id'] == Yii::$app->tenant->id
            && ($record['permitted_role'] == 'corp_admin' || $record['permitted_role'] == 'client_admin');
        });
        $validCategoriesCount = count(ArrayHelper::index($validItems, null, 'manage_menu_category_id'));
        // 代理店権限のメニューの数をチェック
        verify(ManageMenuCategory::getMyMenu($adminId))->count($validCategoriesCount);
        // 求人原稿関連の除外権限を割り当てて数をチェック
        $auth->assign($auth->getPermission('jobListException'), $adminId);
        $auth->assign($auth->getPermission('jobCreateException'), $adminId);
        verify(ManageMenuCategory::getMyMenu($adminId))->count($validCategoriesCount - 1);
        verify($this->countChildMenus(ManageMenuCategory::getMyMenu($adminId)))->equals(count($validItems) - 2);

        // レコードを元に戻す
        self::getFixtureInstance('auth_assignment')->load();
    }
    /** getMyMenuのtest（掲載企業権限） */
    public function testGetMyMenuByClient()
    {
        $this->setIdentity('client_admin');
        $adminId = $this->getIdentity()->id;
        $auth = new DbManager();
        // 検証値を生成
        $validItems = array_filter(self::getFixtureInstance('manage_menu_main')->data(), function ($record) {
            return $record['valid_chk'] == 1 && $record['tenant_id'] == Yii::$app->tenant->id && $record['permitted_role'] == 'client_admin';
        });
        $validCategoriesCount = count(ArrayHelper::index($validItems, null, 'manage_menu_category_id'));
        // 掲載企業権限のメニューの数をチェック
        verify(ManageMenuCategory::getMyMenu($adminId))->count($validCategoriesCount);
        // 求人原稿関連の除外権限を割り当てて数をチェック
        $auth->assign($auth->getPermission('jobListException'), $adminId);
        $auth->assign($auth->getPermission('jobCreateException'), $adminId);
        verify(ManageMenuCategory::getMyMenu($adminId))->count($validCategoriesCount - 1);
        verify($this->countChildMenus(ManageMenuCategory::getMyMenu($adminId)))->equals(count($validItems) - 2);

        // レコードを元に戻す
        self::getFixtureInstance('auth_assignment')->load();
    }

    /**
     * カテゴリーに属するメニューの数を数える
     * @param $models
     * @return int
     */
    public function countChildMenus($models)
    {
        $count = 0;
        foreach ($models as $model) {
            /** @var ManageMenuCategory $model */
            $count += count((array)$model->items);
        }
        return $count;
    }
}