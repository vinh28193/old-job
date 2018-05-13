<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use app\models\manage\ManageMenuMain;
use tests\codeception\fixtures\ManageMenuMainFixture;

/**
 * Class ManageMenuMainTest
 * @package models\manage
 */
class ManageMenuMainTest extends JmTestCase
{
    /**
     * 一応
     */
    public function testTableName()
    {
        $model = new ManageMenuMain();
        verify($model->tableName())->equals('manage_menu_main');
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new ManageMenuMain();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {

        $this->specify('必須チェック', function () {
            $model = new ManageMenuMain();
            $model->load([$model->formName() => [
                'manage_menu_main_id' => null,
                'title' => null,
                'href' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('manage_menu_main_id'))->true();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('href'))->true();

        });

        $this->specify('数字チェック', function () {
            $model = new ManageMenuMain();
            $model->load([$model->formName() => [
                'manage_menu_main_id' => '文字列',
                'manage_menu_category_id' => '文字列',
                'valid_chk' => '文字列',
                'sort' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('manage_menu_main_id'))->true();
            verify($model->hasErrors('manage_menu_category_id'))->true();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('sort'))->true();
        });

        $this->specify('文字列チェック', function () {
            $model = new ManageMenuMain();
            $model->load([$model->formName() => [
                'icon_key' => (int)1,
                'title' => (int)1,
                'href' => (int)1,
                'permitted_role' => (int)1,
                'exception' => (int)1,
            ]]);
            $model->validate();
            verify($model->hasErrors('icon_key'))->true();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('href'))->true();
            verify($model->hasErrors('permitted_role'))->true();
            verify($model->hasErrors('exception'))->true();
        });

        $this->specify('文字列の最大', function () {
            $model = new ManageMenuMain();
            $model->load([$model->formName() => [
                'icon_key' => str_repeat('a', 256),
                'title' => str_repeat('a', 256),
                'href' => str_repeat('a', 256),
                'permitted_role' => str_repeat('a', 256),
                'exception' => str_repeat('a', 256),
            ]]);
            $model->validate();
            verify($model->hasErrors('icon_key'))->true();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('href'))->true();
            verify($model->hasErrors('permitted_role'))->true();
            verify($model->hasErrors('exception'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new ManageMenuMain();
            $model->load([$model->formName() => [
                'tenant_id' => 1,
                'manage_menu_main_id' => 1,
                'manage_menu_category_id' => 1,
                'title' => str_repeat('a', 255),
                'href' => str_repeat('a', 255),
                'valid_chk' => 1,
                'sort' => 1,
                'icon_key' => str_repeat('a', 255),
                'permitted_role' => str_repeat('a', 255),
                'exception' => str_repeat('a', 255),
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * findFromRouteのtest
     */
    public function testFindFromRoute()
    {
        $record = self::getFixtureInstance('manage_menu_main')->data()[$this->id(2, 'manage_menu_main')];
        $model = ManageMenuMain::findFromRoute($record['href']);
        verify($model->attributes)->equals($record);
        $model = ManageMenuMain::findFromRoute($record['href'] . '/');
        verify($model->attributes)->equals($record);
        $model = ManageMenuMain::findFromRoute(ltrim($record['href']));
        verify($model->attributes)->equals($record);
    }

}