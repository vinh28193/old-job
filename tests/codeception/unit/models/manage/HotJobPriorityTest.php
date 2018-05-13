<?php

namespace models\manage;

use tests\codeception\unit\JmTestCase;
use app\models\manage\HotJobPriority;

class HotJobPriorityPriorityTest extends JmTestCase
{
    /**
     * テーブルテスト
     */
    public function testTableName()
    {
        verify(HotJobPriority::tableName())->equals('hot_job_priority');
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new HotJobPriority();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必要チェック', function () {
            $model = new HotJobPriority();

            $model->validate();
            verify($model->hasErrors('hot_job_id'))->true();
            verify($model->hasErrors('disp_priority'))->true();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('item'))->true();
        });

        $this->specify('数字チェック', function () {
            $model = new HotJobPriority();
            $model->load([
                $model->formName() => [
                    'hot_job_id' => '文字列',
                    'disp_priority' => '文字列',
                    'tenant_id' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('hot_job_id'))->true();
            verify($model->hasErrors('disp_priority'))->true();
            verify($model->hasErrors('tenant_id'))->true();
        });

        $this->specify('文字列チェック', function () {
            $model = new HotJobPriority();
            $model->load([
                $model->formName() => [
                    'item' => 1,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('item'))->true();
        });

        $this->specify('文字列最大値チェック', function () {
            $model = new HotJobPriority();
            $model->load([
                $model->formName() => [
                    'item' => str_repeat('a', 31),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('item'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new HotJobPriority();
            $model->load([
                $model->formName() => [
                    'tenant_id' => 1,
                    'hot_job_id' => 1,
                    'item' => str_repeat('a', 30),
                    'disp_priority' => 1,
                ]
            ]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->false();
            verify($model->hasErrors('hot_job_id'))->false();
            verify($model->hasErrors('item'))->false();
            verify($model->hasErrors('disp_priority'))->false();
        });
    }
}