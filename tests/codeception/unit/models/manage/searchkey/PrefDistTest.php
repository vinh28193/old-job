<?php

namespace models\manage;


use Yii;
use Codeception\Specify;
use yii\helpers\ArrayHelper;
use tests\codeception\unit\JmTestCase;
use app\models\manage\searchkey\PrefDist;
use tests\codeception\unit\fixtures\PrefDistFixture;

/**
 * @group job_relations
 */
class PrefDistTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(PrefDist::tableName())->equals('pref_dist');
    }

    /**
     * attributeLabelsテスト
     */
    public function testAttributeLabels()
    {
        $prefDist = new PrefDist();
        verify($prefDist->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new PrefDist();
            $model->load([$model->formName() => [
                'pref_dist_master_id' => null,
                'dist_id' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_dist_master_id'))->true();
            verify($model->hasErrors('dist_id'))->true();
        });

        $this->specify('数字チェック', function () {
            $model = new PrefDist();
            $model->load([$model->formName() => [
                'pref_dist_master_id' => '文字列',
                'dist_id' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_dist_master_id'))->true();
            verify($model->hasErrors('dist_id'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new PrefDist();
            $model->load([$model->formName() => [
                'tenant_id' => 1,
                'pref_dist_master_id' => 1,
                'dist_id' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }
}