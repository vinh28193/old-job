<?php

namespace models\manage\searchkey;

use app\models\manage\searchkey\Area;
use tests\codeception\unit\JmTestCase;
use yii;

/**
 * Class AreaTest
 * @package models\manage\searchkey
 */
class AreaTest extends JmTestCase
{
    /**
     * attribute labels test
     */
    public function testAttributeLabels()
    {
        $model = new Area();
        verify($model->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
        $this->specify('エリア名空時の検証', function () {
            $model = new Area();
            $model->load([
                'Area' => [
                    'area_name' => '',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('area_name'))->true();
        });
        $this->specify('エリア名最大文字数の検証', function () {
            $model = new Area();
            $model->load([
                'Area' => [
                    'area_name' => str_repeat('1', 51),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('area_name'))->true();
        });
        $this->specify('表示順空時の検証', function () {
            $model = new Area();
            $model->load([
                'Area' => [
                    'sort' => '',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('sort'))->true();
        });
        $this->specify('公開状況空時の検証', function () {
            $model = new Area();
            $model->load([
                'Area' => [
                    'valid_chk' => null,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('公開状況数字外の検証', function () {
            $model = new Area();
            $model->load([
                'Area' => [
                    'valid_chk' => 'aaa',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('エリアURL名空時の検証', function () {
            $model = new Area();
            $model->load([
                'Area' => [
                    'area_dir' => null,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('area_dir'))->true();
        });
        $this->specify('エリアURL名最大文字数の検証', function () {
            $model = new Area();
            $model->load([
                'Area' => [
                    'area_dir' => str_repeat('1', 51),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('area_dir'))->true();
        });
        $this->specify('エリアURL名半角文字の検証', function () {
            $model = new Area();
            $model->load([
                'Area' => [
                    'area_dir' => '全角文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('area_dir'))->true();
        });
        $this->specify('公開状況を全て無効にしようとした場合の検証', function () {
            // データ整形
            $model = $this->changeToOneArea();

            $model->load([
                'Area' => [
                    'valid_chk' => 0,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
            // 書き換えたのを元に戻す
            self::getFixtureInstance('area')->load();
        });
        $this->specify('正しい値', function () {
            $model = new Area();
            $model->load([
                'Area' => [
                    'area_name' => '文字列',
                    'area_dir' => 'test',
                    'sort' => 1,
                    'valid_chk' => 1,
                    'area_no' => 1,
                ],
            ]);
            verify($model->validate())->true();
        });
    }

    /**
     * pref_idのgetとsetをtest
     */
    public function testSetGetPrefId()
    {
        $testId = 1;
        $area = new Area();
        $area->setPrefId($testId);
        verify($area->getPrefId())->equals($testId);
    }

    /**
     * test
     */
    public function testNationwideArea()
    {
        $obj = Area::nationwideArea();
        verify($obj->id)->equals(0);
        verify($obj->area_name)->equals(Yii::t('app', '全国'));
    }

    /**
     * isOneArea()のtest
     * areaのfixtureが、複数エリア有効である必要があります
     */
    public function testIsOneArea()
    {
        //1エリアでない場合、falseを返すことを確認。
        verify(Area::isOneArea())->equals(false);
        // ワンエリア状態に変更
        $this->changeToOneArea();
        //1エリアの場合、trueを返すことを確認。
        verify(Area::isOneArea())->equals(true);
        // 書き換えたのを元に戻す
        self::getFixtureInstance('area')->load();
    }

    /**
     * ワンエリア状態に変える
     * @return Area
     */
    private function changeToOneArea()
    {
        Area::updateAll(['valid_chk' => Area::FLAG_INVALID]);
        /* @var $areaModel Area */
        $areaModel = Area::find()->one();
        $areaModel->valid_chk = 1;
        $areaModel->save(false);
        return $areaModel;
    }
}
