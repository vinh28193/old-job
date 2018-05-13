<?php

namespace models\manage;

use app\models\manage\MainVisual;
use app\models\manage\MainVisualImage;
use app\models\manage\WidgetData;
use tests\codeception\unit\JmTestCase;

/**
 * Class MainVisualImageTest
 * @package models\manage
 */
class MainVisualImageTest extends JmTestCase
{
    /**
     * テーブルテスト
     */
    public function testTableName()
    {
        verify(MainVisualImage::tableName())->equals('main_visual_image');
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new MainVisualImage();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必要チェック', function () {
            $model = new MainVisualImage();

            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('main_visual_id'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('数字チェック', function () {
            $model = new MainVisualImage();
            $model->load([
                $model->formName() => [
                    'main_visual_id' => '文字列',
                    'valid_chk' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('main_visual_id'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('文字列最大値チェック', function () {
            $model = new MainVisualImage();
            $model->load([
                $model->formName() => [
                    'url' => str_repeat('a', 257),
                    'url_sp' => str_repeat('a', 257),
                    'file_name' => str_repeat('a', 257),
                    'file_name_sp' => str_repeat('a', 257),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('url'))->true();
            verify($model->hasErrors('url_sp'))->true();
            verify($model->hasErrors('file_name'))->true();
            verify($model->hasErrors('file_name_sp'))->true();
        });

        $this->specify('URL形式チェック', function () {
            $model = new MainVisualImage();
            $model->load([
                $model->formName() => [
                    'url' => str_repeat('a', 32),
                    'url_sp' => str_repeat('a', 32),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('url'))->true();
            verify($model->hasErrors('url_sp'))->true();
        });
    }

    /**
     * ファイルパス取得
     */
    public function testGetFilePath()
    {
        $model = new MainVisualImage();
        $model->load([
            $model->formName() => [
                'file_name' => 'test.jpg',
            ],
        ]);

        verify($model->getFilePath() == WidgetData::DIR_PATH . '/' . 'test.jpg')->true();
    }

    /**
     * ファイルパス取得(SP)
     */
    public function testGetFilePathSp()
    {
        $model = new MainVisualImage();
        $model->load([
            $model->formName() => [
                'file_name_sp' => 'test_sp.jpg',
            ],
        ]);

        verify($model->getFilePathSp() == WidgetData::DIR_PATH . '/' . 'test_sp.jpg')->true();
    }

    /**
     * ファイル名生成
     */
    public function testGenerateFileName()
    {
        $fileName = MainVisualImage::generateFileName('jpg');
        verify(preg_match("/^main_visual_[a-z0-9]{32}\.jpg$/", $fileName) == 1)->true();
    }

    /**
     * 親Model
     */
    public function testGetMainVisual()
    {
        $model = new MainVisualImage();
        $main = new MainVisual();
        $main->load([
            $main->formName() => [
                'tenant_id' => 1,
                'area_id' => 1,
                'type' => MainVisual::TYPE_BANNER,
                'valid_chk' => 1,
            ]
        ]);

        $model->populateRelation('mainVisual', $main);

        verify($model->mainVisual->tenant_id == 1)->true();
        verify($model->mainVisual->area_id == 1)->true();
        verify($model->mainVisual->type == MainVisual::TYPE_BANNER)->true();
        verify($model->mainVisual->valid_chk == 1)->true();
    }
}