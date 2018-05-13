<?php

namespace models\manage;

use app\models\manage\searchkey\JobTypeSmall;
use tests\codeception\unit\fixtures\JobTypeSmallFixture;
use tests\codeception\unit\JmTestCase;
use yii\base\Exception;

/**
 * @group job_relations
 */
class JobTypeSmallTest extends JmTestCase
{
    /**
     * 登録前処理テスト
     */
    public function testBeforeSave()
    {
        $model = new JobTypeSmall();
        $model->beforeSave(true);
        verify($model->job_type_small_no)->equals(JobTypeSmallFixture::RECORDS_PER_TENANT + 1);
    }

    /**
     * attribute labels test
     */
    public function testAttributeLabels()
    {
        $model = new JobTypeSmall();
        verify($model->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
        $this->specify('カテゴリ名空時の検証', function() {
            $model = new JobTypeSmall();
            $model->load(['JobTypeSmall' => [
                'job_type_small_name' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('job_type_small_name'))->true();
        });
        $this->specify('カテゴリ名最大文字数の検証', function() {
            $model = new JobTypeSmall();
            $model->load(['JobTypeSmall' => [
                'job_type_small_name' => str_repeat('1',51),
            ]]);
            $model->validate();
            verify($model->hasErrors('job_type_small_name'))->true();
        });
        $this->specify('表示順空時の検証', function() {
            $model = new JobTypeSmall();
            $model->load(['JobTypeSmall' => [
                'sort' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('sort'))->true();
        });
        $this->specify('公開状況空時の検証', function() {
            $model = new JobTypeSmall();
            $model->load(['JobTypeSmall' => [
                'valid_chk' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('公開状況数字外の検証', function() {
            $model = new JobTypeSmall();
            $model->load(['JobTypeSmall' => [
                'valid_chk' => 'aaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('正しい値', function() {
            $model = new JobTypeSmall();
            $model->load(['JobTypeSmall' => [
                'job_type_small_name' => '文字列',
                'sort' => 1,
                'valid_chk' => 1,
                'job_type_big_id' => 1,
                'job_type_small_no' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

}