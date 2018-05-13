<?php

namespace models\manage\searchkey;

use app\models\manage\searchkey\JobTypeBig;
use tests\codeception\unit\fixtures\JobTypeBigFixture;
use tests\codeception\unit\JmTestCase;

class JobTypeBigTest extends JmTestCase
{
    /**
     * 登録前処理テスト
     */
    public function testBeforeSave()
    {
        $model = new JobTypeBig();
        $model->beforeSave(true);
        verify($model->job_type_big_no)->equals(JobTypeBigFixture::RECORDS_PER_TENANT + 1);
    }

    /**
     * attribute labels test
     */
    public function testAttributeLabels()
    {
        $model = new JobTypeBig();
        verify($model->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
        $this->specify('カテゴリ名空時の検証', function() {
            $model = new JobTypeBig();
            $model->load(['JobTypeBig' => [
                'job_type_big_name' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('job_type_big_name'))->true();
        });
        $this->specify('カテゴリ名最大文字数の検証', function() {
            $model = new JobTypeBig();
            $model->load(['JobTypeBig' => [
                'job_type_big_name' => str_repeat('1',51),
            ]]);
            $model->validate();
            verify($model->hasErrors('job_type_big_name'))->true();
        });
        $this->specify('表示順空時の検証', function() {
            $model = new JobTypeBig();
            $model->load(['JobTypeBig' => [
                'sort' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('sort'))->true();
        });
        $this->specify('公開状況空時の検証', function() {
            $model = new JobTypeBig();
            $model->load(['JobTypeBig' => [
                'valid_chk' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('公開状況数字外の検証', function() {
            $model = new JobTypeBig();
            $model->load(['JobTypeBig' => [
                'valid_chk' => 'aaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('正しい値', function() {
            $model = new JobTypeBig();
            $model->load(['JobTypeBig' => [
                'job_type_big_name' => '文字列',
                'sort' => 1,
                'valid_chk' => 1,
                'job_type_big_no' => 1,
                'job_type_category_id' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

    public function testJobTypeBigList()
    {
        //カテゴリ1のみテストを行う
        $JobTypeBigList = JobTypeBig::getJobTypeBigList(1);
        foreach ($JobTypeBigList as $id => $categoryName) {
            $target = JobTypeBig::findOne($id);
            //fixtureのカテゴリ名とfunctionから取得したカテゴリ名を比較
            verify($categoryName)->equals($target->job_type_big_name);
        }
    }

}