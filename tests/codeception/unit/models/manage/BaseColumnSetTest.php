<?php

namespace models\manage;

use app\models\manage\AdminColumnSet;
use app\models\manage\BaseColumnSet;
use app\models\manage\ClientColumnSet;
use app\models\manage\CorpColumnSet;
use app\models\manage\JobColumnSet;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * @group job_relations
 */
class BaseColumnSetTest extends JmTestCase
{
    /**
     * ラベル設定テスト
     * 代表してCorpColumnSetで検証
     */
    public function testAttributeLabels()
    {
        $model = new CorpColumnSet();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * rulesテスト
     * 代表してCorpColumnSetで検証
     * max_lengthの検証は継承先モデルで検証
     * commonMaxLengthRuleもそこで検証
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new CorpColumnSet();
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('column_no'))->true();
            verify($model->hasErrors('column_name'))->true();
            verify($model->hasErrors('label'))->true();
            verify($model->hasErrors('data_type'))->true();
            verify($model->hasErrors('is_in_list'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new CorpColumnSet();
            $model->load([
                'tenant_id' => '文字列',
                'column_no' => [1, 2, 3],
                'max_length' => '文字列',
            ], '');
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('column_no'))->true();
            verify($model->hasErrors('max_length'))->true();
        });
        $this->specify('boolチェック', function () {
            $model = new CorpColumnSet();
            $model->load([
                'is_must' => '文字列',
                'is_in_list' => 99,
                'is_in_search' => [1, 2, 3],
                'valid_chk' => '文字列',
            ], '');
            $model->validate();
            verify($model->hasErrors('is_must'))->true();
            verify($model->hasErrors('is_in_list'))->true();
            verify($model->hasErrors('is_in_search'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('文字列チェック', function () {
            $model = new CorpColumnSet();
            $model->load([
                'column_name' => 1,
                'label' => 2,
                'data_type' => [1, 2, 3],
            ], '');
            $model->validate();
            verify($model->hasErrors('column_name'))->true();
            verify($model->hasErrors('label'))->true();
            verify($model->hasErrors('data_type'))->true();
        });
        $this->specify('最大値チェック', function () {
            $model = new CorpColumnSet();
            $model->load([
                'column_name' => str_repeat('a', 31),
                'label' => str_repeat('a', 256),
                'data_type' => str_repeat('a', 11),
            ], '');
            $model->validate();
            verify($model->hasErrors('column_name'))->true();
            verify($model->hasErrors('label'))->true();
            verify($model->hasErrors('data_type'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new CorpColumnSet();
            $model->load([
                'column_name' => str_repeat('a', 30),
                'label' => str_repeat('a', 255),
                'data_type' => str_repeat('a', 10),
                'is_must' => BaseColumnSet::MUST,
                'is_in_list' => BaseColumnSet::IN_LIST,
                'is_in_search' => BaseColumnSet::IN_SEARCH,
                'valid_chk' => BaseColumnSet::VALID,
            ], '');
            $model->validate();
            verify($model->hasErrors('column_name'))->false();
            verify($model->hasErrors('label'))->false();
            verify($model->hasErrors('data_type'))->false();
            verify($model->hasErrors('is_must'))->false();
            verify($model->hasErrors('is_in_list'))->false();
            verify($model->hasErrors('is_in_search'))->false();
            verify($model->hasErrors('valid_chk'))->false();
        });
    }

    /**
     * isOptionのテスト
     * 代表してCorpColumnSetで検証
     */
    public function testIsOption()
    {
        $model = new CorpColumnSet();
        $model->column_name = 'option001';
        verify($model->isOption())->true();
        $model->column_name = 'column_name';
        verify($model->isOption())->false();
    }

    /**
     * getTypeArrayのテスト
     * getDefaultTypeArrayとgetOptionTypeArrayもこれで通る
     * 代表してCorpColumnSetとJobColumnSetで検証
     */
    public function testGetTypeArray()
    {
        $model = new CorpColumnSet();
        $model->column_name = 'option001';
        verify(count($model->typeArray))->equals(4);
        $model->column_name = 'column_name';
        verify(count($model->typeArray))->equals(4);
        $model = new JobColumnSet();
        $model->column_name = 'option001';
        verify(count($model->typeArray))->equals(6);
        $model->column_name = 'column_name';
        verify(count($model->typeArray))->equals(4);
    }

    /**
     * getIsMustArrayとgetAdminDisplayArrayとgetValidArrayのテスト
     */
    public function testRadioArray()
    {
        verify(count(BaseColumnSet::getIsMustArray()))->equals(2);
        verify(count(BaseColumnSet::getIsInListArray()))->equals(2);
        verify(count(BaseColumnSet::getValidArray()))->equals(2);
    }

    /**
     * getSubsetのtest
     * 代表してAdminColumnSetで検証
     * Admin,Client,Corpがそのまま使っている
     */
    public function testGetSubset()
    {
        $allRecords = ArrayHelper::toArray($this->getFixture('application_master'));
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });

        foreach ($tenantRecords as $record) {
            $model = AdminColumnSet::findOne(['column_name' => $record['column_name']]);
            verify($model->getSubset())->false();
        }
    }


    /**
     * setScenarioByAttributesのtest
     * 代表してClientColumnSetで検証
     * このメソッドかTEL定数をオーバーライドすると個別テストが必要になる
     * Admin,Client,Corpがそのまま使っている
     */
    public function testSetScenarioByAttributes()
    {
        $allRecords = ArrayHelper::toArray($this->getFixture('application_column_set'));
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });

        foreach ($tenantRecords as $record) {
            $model = ClientColumnSet::findOne(['column_name' => $record['column_name']]);
            $model->setScenarioByAttributes();
            switch ($model->column_name) {
                case'tel_no':
                    verify($model->scenario)->equals(ClientColumnSet::SCENARIO_TEL_NO);
                    break;
                case'option100':
                case'option101':
                case'option102':
                case'option103':
                case'option104':
                case'option105':
                case'option106':
                case'option107':
                case'option108':
                case'option109':
                    verify($model->scenario)->equals(ClientColumnSet::SCENARIO_OPTION);
                    break;
                default:
                    verify($model->scenario)->equals(ClientColumnSet::SCENARIO_DEFAULT);
                    break;
            }
        }
    }
    // getWhenClientJsはunit testで検証不可能
}