<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use Yii;
use yii\web\Request;
use yii\helpers\ArrayHelper;
use app\models\manage\ApplicationColumnSubset;
use tests\codeception\unit\fixtures\ApplicationColumnSubsetFixture;

class ApplicationColumnSubsetTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(ApplicationColumnSubset::tableName())->equals('application_column_subset');
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new ApplicationColumnSubset();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * rulesテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new ApplicationColumnSubset();
            $model->load([$model->formName() => [
                'tenant_id' => null,
                'column_name' => null,
                'subset_name' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('column_name'))->true();
            verify($model->hasErrors('subset_name'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new ApplicationColumnSubset();
            $model->load([$model->formName() => [
                'tenant_id' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
        });
        $this->specify('最大ストリング', function () {
            Yii::$app->request->setBodyParams([]);
            $model = new ApplicationColumnSubset();
            $model->load([$model->formName() => [
                'column_name' => str_repeat('a', 31),
                'subset_name' => str_repeat('a', 256),
            ]]);
            $model->validate();
            verify($model->hasErrors('column_name'))->true();
            verify($model->hasErrors('subset_name'))->true();
        });
        $this->specify('重複チェック', function () {
            Yii::$app->request->setBodyParams([
                'ApplicationColumnSubset' => [
                    0 => [
                        'subset_name' => 'テスト',
                    ],
                    1 => [
                        'subset_name' => 'テスト',
                    ],
                ]
            ]);
            $model = new ApplicationColumnSubset();
            $model->load([$model->formName() => [
                'subset_name' => 'テスト',
            ]]);
            $model->validate();
            verify($model->hasErrors('subset_name'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new ApplicationColumnSubset();
            $model->load([$model->formName() => [
                'tenant_id' => 1,
                'column_name' => str_repeat('a', 30),
                'subset_name' => str_repeat('a', 255),
            ]]);
            verify($model->validate())->true();
        });
    }
}