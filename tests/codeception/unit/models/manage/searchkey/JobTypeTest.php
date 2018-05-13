<?php
namespace models\manage\searchkey;

use tests\codeception\unit\JmTestCase;
use app\models\manage\searchkey\JobType;

class JobTypeTest extends JmTestCase
{
    public function testAttributeLabels()
    {
        $model = new JobType();
        verify(is_array($model->attributeLabels()))->true();
    }

    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new JobType();
            $model->load([$model->formName() => [
                'job_master_id' => '文字列',
                'job_type_small_id' => '文字列',               
            ]]);
            $model->validate();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('job_type_small_id'))->true();
        });

        $this->specify('必須チェック', function () {
            $model = new JobType();
            $model->load([$model->formName() => [
                'job_master_id' => null,
                'job_type_small_id' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('job_type_small_id'))->true();
        });
        $this->specify('正しいチェック', function () {
            $model = new JobType();
            $model->load([$model->formName() => [
                'id'=> 1,
                'job_master_id'=> 1,
                'job_type_small_id'=> 1,
            ]]);
            verify($model->validate())->true();
        });
    }
}