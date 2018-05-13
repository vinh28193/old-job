<?php

namespace models\manage;

use app\models\manage\HotJob;
use tests\codeception\unit\JmTestCase;
use Yii;

class HotJobTest extends JmTestCase
{
    /**
     * テーブルテスト
     */
    public function testTableName()
    {
        verify(HotJob::tableName())->equals('hot_job');
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new HotJob();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必要チェック', function () {
            $model = new HotJob();

            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('disp_amount'))->true();
            verify($model->hasErrors('disp_type_ids'))->true();
            verify($model->hasErrors('text1_length'))->true();
            verify($model->hasErrors('text2_length'))->true();
            verify($model->hasErrors('text3_length'))->true();
            verify($model->hasErrors('text4_length'))->true();
        });

        $this->specify('数字チェック', function () {
            $model = new HotJob();
            $model->load([
                $model->formName() => [
                    'tenant_id' => '文字列',
                    'disp_amount' => '文字列',
                    'text1_length' => '文字列',
                    'text2_length' => '文字列',
                    'text3_length' => '文字列',
                    'text4_length' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('disp_amount'))->true();
            verify($model->hasErrors('text1_length'))->true();
            verify($model->hasErrors('text2_length'))->true();
            verify($model->hasErrors('text3_length'))->true();
            verify($model->hasErrors('text4_length'))->true();
        });

        $this->specify('文字列チェック', function () {
            $model = new HotJob();
            $model->load([
                $model->formName() => [
                    'title' => 1,
                    'text1' => 1,
                    'text2' => 1,
                    'text3' => 1,
                    'text4' => 1,
                    'disp_type_ids' => 1,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('text1'))->true();
            verify($model->hasErrors('text2'))->true();
            verify($model->hasErrors('text3'))->true();
            verify($model->hasErrors('text4'))->true();
            verify($model->hasErrors('disp_type_ids'))->true();
        });

        $this->specify('文字列最大値チェック', function () {
            $model = new HotJob();
            $model->load([
                $model->formName() => [
                    'title' => str_repeat('a', 41),
                    'text1' => str_repeat('a', 31),
                    'text2' => str_repeat('a', 31),
                    'text3' => str_repeat('a', 31),
                    'text4' => str_repeat('a', 31),
                    'disp_type_ids' => str_repeat('a', 31),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('text1'))->true();
            verify($model->hasErrors('text2'))->true();
            verify($model->hasErrors('text3'))->true();
            verify($model->hasErrors('text4'))->true();
            verify($model->hasErrors('disp_type_ids'))->true();
        });

        $this->specify('boolチェック', function () {
            $model = new HotJob();
            $model->load([
                $model->formName() => [
                    'valid_chk' => '文字列',
                    ]
            ]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new HotJob();
            $model->load([
                $model->formName() => [
                'tenant_id' => 1,
                'valid_chk' => HotJob::VALID,
                'title' => str_repeat('a', 40),
                'disp_amount' => 1,
                'disp_type_ids' => str_repeat('a', 30),
                'text1' => str_repeat('a', 30),
                'text2' => str_repeat('a', 30),
                'text3' => str_repeat('a', 30),
                'text4' => str_repeat('a', 30),
                'text1_length' => 1,
                'text2_length' => 1,
                'text3_length' => 1,
                'text4_length' => 1,
                    ]
            ]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->false();
            verify($model->hasErrors('valid_chk'))->false();
            verify($model->hasErrors('title'))->false();
            verify($model->hasErrors('disp_amount'))->false();
            verify($model->hasErrors('disp_type_ids'))->false();
            verify($model->hasErrors('text1'))->false();
            verify($model->hasErrors('text2'))->false();
            verify($model->hasErrors('text3'))->false();
            verify($model->hasErrors('text4'))->false();
            verify($model->hasErrors('text1_length'))->false();
            verify($model->hasErrors('text2_length'))->false();
            verify($model->hasErrors('text3_length'))->false();
            verify($model->hasErrors('text4_length'))->false();
        });
    }

    /**
     * getDispTypeNameのtest
     */
    public function testGetDispTypeName()
    {
        $model = new HotJob();
        verify($model->getDispTypeName())->equals([
            1 => "２基本タイプ",
            2 => "２ビジュアルタイプ",
            3 => "２デラックスタイプ",
        ]);
    }

    /**
     * getExplodeDispTypeのtest
     */
    public function testGetExplodeDispType()
    {
        $model = new HotJob();
        verify($model->getExplodeDispType())->equals([
            0 => "1",
            1 => "2",
            2 => "3",
        ]);
    }

    /**
     * getValidChkLabelsのtest
     */
    public function testGetValidChkLabels()
    {
        $model = new HotJob();
        verify($model->getValidChkLabels()[HotJob::VALID])->equals(Yii::t('app', '有効'));
        verify($model->getValidChkLabels()[HotJob::INVALID])->equals(Yii::t('app', '無効'));
    }
}