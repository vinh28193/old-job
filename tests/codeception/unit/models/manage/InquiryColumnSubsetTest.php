<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 6/10/2016
 * Time: 11:22 AM
 */

namespace models\manage;
use tests\codeception\unit\JmTestCase;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\manage\InquiryColumnSubset;
use tests\codeception\fixtures\InquiryColumnSubsetFixture;


class InquiryColumnSubsetTest extends JmTestCase
{
    /**
     * @inheritdoc
     */
    public function testTableName()
    {
        verify(InquiryColumnSubset::tableName())->equals('inquiry_column_subset');
    }

    /**
     * 要素のテスト
     */
    public function testAttributeLabels()
    {
        $model = new InquiryColumnSubset();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * rulesテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new InquiryColumnSubset();
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
            $model = new InquiryColumnSubset();
            $model->load([$model->formName() => [
                'tenant_id' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
        });

        $this->specify('最大ストリング', function () {
            Yii::$app->request->setBodyParams([]);
            $model = new InquiryColumnSubset();
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
                'InquiryColumnSubset' => [
                    0 => [
                        'subset_name' => 'テスト',
                    ],
                    1 => [
                        'subset_name' => 'テスト',
                    ],
                ]
            ]);
            $model = new InquiryColumnSubset();
            $model->load([$model->formName() => [
                'subset_name' => 'テスト',
            ]]);
            $model->validate();
            verify($model->hasErrors('subset_name'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new InquiryColumnSubset();
            $model->load([$model->formName() => [
                'tenant_id' => 1,
                'column_name' => str_repeat('a', 30),
                'subset_name' => str_repeat('a', 255),
            ]]);
            verify($model->validate())->true();
        });
    }

    public function testRelations()
    {
        $inquiryColumnSubsetFixtures = self::getFixtureInstance('inquiry_column_subset')->data();
        $key = rand($this->id(1, 'inquiry_column_subset'),InquiryColumnSubsetFixture::RECORDS_PER_TENANT);
        $inquiryColumnSubsetId = ArrayHelper::getValue($inquiryColumnSubsetFixtures[$key],'id');
        $inquiryColumnSubset = InquiryColumnSubset::findOne($inquiryColumnSubsetId);
        $this->specify('setItemとのrelation', function() use ($inquiryColumnSubset) {
            verify($inquiryColumnSubset->setItem)->notEmpty();
        });
    }
}
