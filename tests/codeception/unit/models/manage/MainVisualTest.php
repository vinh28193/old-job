<?php

namespace models\manage;

use app\models\manage\MainVisual;
use app\models\manage\MainVisualImage;
use app\models\manage\searchkey\Area;
use tests\codeception\unit\JmTestCase;

/**
 * Class MainVisualTest
 * @package models\manage
 */
class MainVisualTest extends JmTestCase
{
    /**
     * テーブルテスト
     */
    public function testTableName()
    {
        verify(MainVisual::tableName())->equals('main_visual');
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new MainVisual();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必要チェック', function () {
            $model = new MainVisual();

            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('type'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('数字チェック', function () {
            $model = new MainVisual();
            $model->load([
                $model->formName() => [
                    'area_id' => '文字列',
                    'valid_chk' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('area_id'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('文字列最大値チェック', function () {
            $model = new MainVisual();
            $model->load([
                $model->formName() => [
                    'type' => str_repeat('a', 17),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('type'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new MainVisual();
            $model->load([
                $model->formName() => [
                    'tenant_id' => 1,
                    'type' => MainVisual::TYPE_SLIDE,
                    'valid_chk' => 0,
                    'memo' => 'テスト',
                ]
            ]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->false();
            verify($model->hasErrors('type'))->false();
            verify($model->hasErrors('valid_chk'))->false();
            verify($model->hasErrors('memo'))->false();
        });

    }

    /**
     * 種別チェック
     */
    public function testTypes()
    {
        verify(MainVisual::types())->equals([
            MainVisual::TYPE_SLIDE => 'スライドショー',
            MainVisual::TYPE_BANNER => 'バナー',
        ]);
    }

    /**
     * エリアチェック
     */
    public function testArea()
    {
        $model = new MainVisual();
        $area = new Area();
        $area->load([
            $area->formName() => [
                'area_name' => 'テストエリア',
                'valid_chk' => 1,
                'area_no' => 1,
            ]
        ]);

        $model->populateRelation('area', $area);

        verify($model->area->area_name == 'テストエリア')->true();
        verify($model->area->valid_chk == 1)->true();
        verify($model->area->area_no == 1)->true();
    }

    /**
     * 画像のチェック
     */
    public function testGetImages()
    {
        $model = new MainVisual();
        $image = new MainVisualImage();
        $image->load([
            $image->formName() => [
                'tenant_id' => 1,
                'main_visual_id' => 1,
                'file_name' => 'mai_visual.jpg',
                'file_name_sp' => 'mai_visual_sp.jpg',
                'url' => 'http://google.co.jp',
                'url_sp' => 'http://google.co.jp',
                'content' => 'ALTテキスト',
                'sort' => 1,
                'valid_chk' => 1,
            ]
        ]);

        $model->populateRelation('images', [0 => $image]);

        verify($model->images[0]->tenant_id == 1)->true();
        verify($model->images[0]->main_visual_id == 1)->true();
        verify($model->images[0]->file_name == 'mai_visual.jpg')->true();
        verify($model->images[0]->file_name_sp == 'mai_visual_sp.jpg')->true();
        verify($model->images[0]->url == 'http://google.co.jp')->true();
        verify($model->images[0]->url_sp == 'http://google.co.jp')->true();
        verify($model->images[0]->content == 'ALTテキスト')->true();
        verify($model->images[0]->sort == 1)->true();
        verify($model->images[0]->valid_chk == 1)->true();
    }
}