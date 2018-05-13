<?php

namespace models\manage;

use app\models\manage\DispType;
use Codeception\Specify;
use tests\codeception\unit\JmTestCase;

class DispTypeTest extends JmTestCase
{
    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new DispType();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new DispType();
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('disp_type_no'))->true();
            verify($model->hasErrors('disp_type_name'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new DispType();
            $model->load([
                'tenant_id' => '文字列',
                'disp_type_no' => '文字列',
                'valid_chk' => '文字列',
            ], '');
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('disp_type_no'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('文字列チェック', function () {
            $model = new  DispType();
            $model->load([
                'disp_type_name' => 1,
            ], '');
            $model->validate();
            verify($model->hasErrors('disp_type_name'))->true();
        });
        $this->specify('最大値チェック', function () {
            $model = new DispType();
            $string = '';
            for ($i = 1; $i <= 256; $i++) {
                $string .= 'a';
            }
            $model->load(['disp_type_name' => $string], '');
            $model->validate();
            verify($model->hasErrors('disp_type_name'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new DispType();
            $string = '';
            for ($i = 1; $i <= 255; $i++) {
                $string .= 'a';
            }
            $model->load([
                'tenant_id' => 10000,
                'disp_type_no' => 127,
                'disp_type_name' => $string,
                'valid_chk' => 1,
            ], '');
            verify($model->validate())->true();
        });
    }
}