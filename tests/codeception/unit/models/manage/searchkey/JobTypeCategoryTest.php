<?php

namespace models\manage;

use app\models\manage\searchkey\JobTypeCategory;
use tests\codeception\unit\fixtures\JobTypeCategoryFixture;
use tests\codeception\unit\JmTestCase;
use yii\base\Exception;

/**
 * @group job_relations
 */
class JobTypeCategoryTest extends JmTestCase
{
    /**
     * 登録前処理テスト
     */
    public function testBeforeSave()
    {
        $model = new JobTypeCategory();
        $model->beforeSave(true);
        verify($model->job_type_category_cd)->equals(JobTypeCategoryFixture::RECORDS_PER_TENANT + 1);
    }
    
    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new JobTypeCategory();
        verify($model->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
        $this->specify('カテゴリ名空時の検証', function() {
            $model = new JobTypeCategory();
            $model->load(['JobTypeCategory' => [
                'name' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('name'))->true();
        });
        $this->specify('カテゴリ名最大文字数の検証', function() {
            $model = new JobTypeCategory();
            $model->load(['JobTypeCategory' => [
                'name' => str_repeat('1',51),
            ]]);
            $model->validate();
            verify($model->hasErrors('name'))->true();
        });
        $this->specify('表示順空時の検証', function() {
            $model = new JobTypeCategory();
            $model->load(['JobTypeCategory' => [
                'sort' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('sort'))->true();
        });
        $this->specify('公開状況空時の検証', function() {
            $model = new JobTypeCategory();
            $model->load(['JobTypeCategory' => [
                'valid_chk' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('公開状況数字外の検証', function() {
            $model = new JobTypeCategory();
            $model->load(['JobTypeCategory' => [
                'valid_chk' => 'aaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('正しい値', function() {
            $model = new JobTypeCategory();
            $model->load(['JobTypeCategory' => [
                'name' => '文字列',
                'sort' => 1,
                'valid_chk' => 1,
                'job_type_category_cd' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

    public function testJobTypeCategoryList()
    {
        //カテゴリ1のみテストを行う
        $JobTypeCategoryList = JobTypeCategory::getJobTypeCategoryList(1);
        foreach ($JobTypeCategoryList as $id => $categoryName) {
            $target = JobTypeCategory::findOne($id);
            //fixtureのカテゴリ名とfunctionから取得したカテゴリ名を比較
            verify($categoryName)->equals($target->name);
        }
    }

}
