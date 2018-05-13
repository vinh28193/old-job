<?php

namespace models\manage\searchkey;

use app\models\manage\searchkey\WageCategory;
use tests\codeception\unit\JmTestCase;

class WageCategoryTest extends JmTestCase
{
    /**
     * attribute labels test
     */
    public function testAttributeLabels()
    {
        $model = new WageCategory();
        verify($model->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
            $this->specify('カテゴリ名空時の検証', function() {
                $model = new WageCategory();
                $model->load(['WageCategory' => [
                    'wage_category_name' => '',
                ]]);
                $model->validate();
                verify($model->hasErrors('wage_category_name'))->true();
            });
            $this->specify('カテゴリ名最大文字数の検証', function() {
                $model = new WageCategory();
                $model->load(['WageCategory' => [
                    'wage_category_name' => str_repeat('1',51),
                ]]);
                $model->validate();
                verify($model->hasErrors('wage_category_name'))->true();
            });
            $this->specify('表示順空時の検証', function() {
                $model = new WageCategory();
                $model->load(['WageCategory' => [
                    'sort' => '',
                ]]);
                $model->validate();
                verify($model->hasErrors('sort'))->true();
            });
            $this->specify('公開状況空時の検証', function() {
                $model = new WageCategory();
                $model->load(['WageCategory' => [
                    'valid_chk' => null,
                ]]);
                $model->validate();
                verify($model->hasErrors('valid_chk'))->true();
            });
            $this->specify('公開状況数字外の検証', function() {
                $model = new WageCategory();
                $model->load(['WageCategory' => [
                    'valid_chk' => 'aaa',
                ]]);
                $model->validate();
                verify($model->hasErrors('valid_chk'))->true();
            });
            $this->specify('正しい値', function() {
                $model = new WageCategory();
                $model->load(['WageCategory' => [
                    'wage_category_name' => '文字列',
                    'sort' => 1,
                    'valid_chk' => 1,
                ]]);
                verify($model->validate())->true();
            });
    }

    public function testWageCategoryList()
    {
        //カテゴリ1のみテストを行う
        $WageCategoryList = WageCategory::getWageCategoryList(1);
        foreach ($WageCategoryList as $id => $categoryName) {
            $target = WageCategory::findOne($id);
            //fixtureのカテゴリ名とfunctionから取得したカテゴリ名を比較
            verify($target['wage_category_name'])->equals($categoryName);
        }
    }

}