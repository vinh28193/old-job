<?php
namespace models\manage\searchkey;

use Yii;
use Codeception\Specify;
use yii\helpers\ArrayHelper;
use tests\codeception\unit\JmTestCase;
use app\models\manage\searchkey\JobWage;
use tests\codeception\unit\fixtures\JobWageFixture;

class JobWageTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(JobWage::tableName())->equals('job_wage');
    }

    /**
     * attributeLabelsテスト
     */
    public function testAttributeLabels()
    {
        $model = new JobWage();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new JobWage();
            $model->load([$model->formName() => [
                'job_master_id' => null,
                'wage_item_id' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('wage_item_id'))->true();
        });

        $this->specify('数字チェック', function () {
            $model = new JobWage();
            $model->load([$model->formName() => [
                'job_master_id' => '文字列',
                'wage_item_id' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('wage_item_id'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new JobWage();
            $model->load([$model->formName() => [
                'job_master_id' => 1,
                'wage_item_id' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

}