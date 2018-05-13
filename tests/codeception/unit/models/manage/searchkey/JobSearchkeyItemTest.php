<?php

namespace models\manage;

use app\models\manage\searchkey\JobSearchkeyItem1;
use Codeception\Specify;
use tests\codeception\unit\JmTestCase;

class JobSearchkeyItemTest extends JmTestCase
{
    /**
     * 要素テスト
     * 代表してJobSearchkeyItem1で検証
     */
    public function testAttributeLabels()
    {
        $model = new JobSearchkeyItem1();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * ルールテスト
     * 代表してJobDistで検証
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new JobSearchkeyItem1();
            $model->validate();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('searchkey_item_id'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new JobSearchkeyItem1();
            $model->load([
                'job_master_id' => '文字列',
                'searchkey_item_id' => '文字列',
            ], '');
            $model->validate();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('searchkey_item_id'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new JobSearchkeyItem1();
            $model->load([
                'job_master_id' => 11,
                'searchkey_item_id' => 22,
            ], '');
            $model->validate();
            verify($model->hasErrors('job_master_id'))->false();
            verify($model->job_master_id)->equals(11);
            verify($model->hasErrors('searchkey_item_id'))->false();
            verify($model->searchkey_item_id)->equals(22);
        });
    }

    /**
     * @return mixed
     */
    public function testGetItemModelName()
    {
        for ($i = 1; $i <= 20; $i++) {
            $modelName = 'app\models\manage\searchkey\JobSearchkeyItem' . $i;
            $itemModelName = 'app\models\manage\searchkey\SearchkeyItem' . $i;
            $model = \Yii::createObject($modelName);
            verify($model->itemModelName)->equals($itemModelName);
        }
    }
}