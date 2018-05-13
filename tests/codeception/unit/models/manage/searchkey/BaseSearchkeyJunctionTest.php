<?php

namespace models\manage;

use app\models\manage\searchkey\BaseSearchKeyJunction;
use app\models\manage\searchkey\JobDist;
use app\models\manage\searchkey\JobPref;
use app\models\manage\searchkey\JobType;
use app\models\manage\searchkey\JobWage;
use tests\codeception\unit\fixtures\JobDistFixture;
use tests\codeception\unit\fixtures\JobPrefFixture;
use tests\codeception\unit\fixtures\JobTypeFixture;
use tests\codeception\unit\fixtures\JobWageFixture;
use tests\codeception\unit\JmTestCase;
use app\models\manage\JobMaster;
use Yii;

/**
 * @property JobDistFixture $job_dist
 * @property JobPrefFixture $job_pref
 * @property JobWageFixture $job_wage
 * @property JobTypeFixture $job_type
 */
class BaseSearchKeyJunctionTest extends JmTestCase
{
    /**
     * 要素テスト
     * 代表してJobDistで検証
     */
    public function testAttributeLabels()
    {
        $model = new JobDist();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * ルールテスト
     * 代表してJobDistで検証
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new JobDist();
            $model->load(['tenant_id' => '文字列'], '');
            $model->validate();
            verify($model->hasErrors('itemIds'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new JobDist();
            $model->load([
                'itemIds' => [1, 2, 3],
                'tenant_id' => 1,
            ], '');
            $model->validate();
            verify($model->hasErrors('itemIds'))->false();
            verify($model->itemIds)->equals([1, 2, 3]);
            verify($model->hasErrors('tenant_id'))->false();
            verify($model->tenant_id)->equals(1);
        });
    }

    /**
     * saveテスト
     * こちらは念のため全modelで検証
     */
    public function testSave()
    {
        $this->specify('JobDist登録検証', function () {
            $this->verifySave(new JobDist());
        });
        $this->specify('JobPref登録検証', function () {
            $this->verifySave(new JobPref());
        });
        $this->specify('JobWage登録検証', function () {
            $this->verifySave(new JobWage());
        });
        $this->specify('JobType登録検証', function () {
            $this->verifySave(new JobType());
        });
        $this->specify('JobSearchkeyItem登録検証', function () {
            for ($i = 1; $i <= 20; $i++) {
                $modelName = 'app\models\manage\searchkey\JobSearchkeyItem' . $i;
                $model = Yii::createObject($modelName);
                $this->verifySave($model);
            }
        });
    }

    public function verifySave(BaseSearchKeyJunction $model)
    {
        $model->load([
            'job_master_id' => 1,
            'tenant_id' => Yii::$app->tenant->id,
            'itemIds' => [1, 2, 3],
        ], '');
        $model->save(false);
        $maxId = $model->find()->select('max(id)')->from($model::tableName())->scalar();
        $models = $model->findAll(['id' => [$maxId, $maxId - 1, $maxId - 2]]);
        foreach ($model->itemIds as $i => $itemId) {
            verify($models[$i]->{$model->itemForeignKey})->equals($itemId);
            verify($models[$i]->job_master_id)->equals(1);
        }
    }
}