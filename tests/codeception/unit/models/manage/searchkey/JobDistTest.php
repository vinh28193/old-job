<?php

namespace models\manage\searchkey;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\manage\searchkey\JobDist;
use tests\codeception\unit\JmTestCase;
use tests\codeception\fixtures\DistFixture;
use tests\codeception\unit\fixtures\JobDistFixture;
use tests\codeception\unit\fixtures\PrefFixture;

/**
 * @group job_relations
 */
class JobDistTest extends JmTestCase
{
    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new JobDist();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new JobDist();
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('dist_id'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new JobDist();
            $model->load([
                'tenant_id' => '文字列',
                'job_master_id' => '文字列',
                'dist_id' => '文字列',
            ], '');
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('dist_id'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new JobDist();
            $model->load([
                'tenant_id' => 1,
                'job_master_id' => 1,
                'dist_id' => 1,
                'itemIds' => [1, 2, 3],
            ], '');
            verify($model->validate())->true();
        });
    }

    /**
     * getPrefIdsのtest
     */
    public function testGetPrefIds()
    {
        $itemIds = [1, 2, 3, 4, 5, 200, 400];
        $distData = require(__DIR__ . '../../../../fixtures/data/dist.php');
        $prefData = require(__DIR__ . '../../../../fixtures/data/pref.php');

        $distFixtures = array_filter($distData, function ($dist) use ($itemIds) {
            return ArrayHelper::isIn($dist['id'], $itemIds);
        });

        $prefNos = ArrayHelper::getColumn(array_values($distFixtures), function ($element) {
            return $element['pref_no'];
        });
        $prefNos = array_unique($prefNos);

        $prefs = array_filter($prefData, function ($pref) use ($prefNos) {
            return ArrayHelper::isIn($pref['pref_no'], $prefNos) && $pref['tenant_id'] == Yii::$app->tenant->id;
        });
        $prefIds = ArrayHelper::getColumn($prefs, 'id');

        $model = new JobDist();
        $model->itemIds = $itemIds;

        verify(array_values($model->prefIds))->equals(array_values($prefIds));
    }
}