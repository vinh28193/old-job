<?php
namespace models\manage\searchkey;

use tests\codeception\unit\JmTestCase;
use app\models\manage\searchkey\Dist;

class DistTest extends JmTestCase
{
    /**
     * 一応
     */
    public function testTableName()
    {
        $model = new Dist();
        verify($model->tableName())->equals('dist');
    }

    /**
     * 一応
     */
    public function testAttributeLabels()
    {
        $model = new Dist();
        verify(count($model->attributeLabels()))->notEmpty();
    }

    /**
     * rulesのtest
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new Dist();
            $model->load([$model->formName() => [
                'pref_no' => '文字列',
                'dist_sub_cd' => '文字列',
                'dist_cd' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_no'))->true();
            verify($model->hasErrors('dist_sub_cd'))->true();
            verify($model->hasErrors('dist_cd'))->true();
        });

        $this->specify('必須チェック', function () {
            $model = new Dist();
            $model->load([$model->formName() => [
                'pref_no' => null,
                'dist_name' => null,
                'dist_cd' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_no'))->true();
            verify($model->hasErrors('dist_name'))->true();
            verify($model->hasErrors('dist_cd'))->true();
        });

        $this->specify('文字列の最大', function () {
            $model = new Dist();
            $model->load([$model->formName() => [
                'dist_name' => str_repeat('a', 256),
            ]]);
            $model->validate();
            verify($model->hasErrors('dist_name'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new Dist();
            $model->load([$model->formName() => [
                'pref_no' => '1',
                'dist_name' => str_repeat('a', 255),
                'dist_sub_cd' => '1',
                'dist_cd' => '1',
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * listFindのtest
     * todo このメソッドホンマにこれでいいのか岡田さんに確認
     */
    public function testListFind()
    {
        $params['PrefDistMaster']['pref_id'] = 1;
        $models = Dist::listFind($params);
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify($model->pref_no)->equals(1);
            verify($model->prefDist)->isEmpty();
        }
    }
}