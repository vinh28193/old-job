<?php

namespace models\manage;

use app\models\manage\AdminColumnSet;
use app\models\manage\ApplicationColumnSet;
use app\models\manage\BaseColumnSet;
use app\models\manage\ClientColumnSet;
use app\models\manage\CorpColumnSet;
use app\models\manage\JobColumnSet;
use Codeception\Specify;
use tests\codeception\fixtures\AdminColumnSetFixture;
use tests\codeception\fixtures\ApplicationColumnSetFixture;
use tests\codeception\fixtures\ClientColumnSetFixture;
use tests\codeception\fixtures\CorpColumnSetFixture;
use tests\codeception\fixtures\JobColumnSetFixture;
use tests\codeception\unit\JmTestCase;

/**
 * @group job_relations
 */
class CorpColumnSetTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(CorpColumnSet::tableName())->equals('corp_column_set');
    }

    /**
     * rulesテスト
     * 他は基底でtestしているのでmax_lengthのみtest
     * commonMaxLengthRuleも検証
     */
    public function testRules()
    {
        $this->specify('corp_noチェック', function () {
            $model = CorpColumnSet::findOne(['column_name' => 'corp_no']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('corp_nameチェック', function () {
            $model = CorpColumnSet::findOne(['column_name' => 'corp_name']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('tel_noチェック', function () {
            $model = CorpColumnSet::findOne(['column_name' => 'tel_no']);
            $this->checkMaxLength($model, 30);
        });
        $this->specify('tanto_nameチェック', function () {
            $model = CorpColumnSet::findOne(['column_name' => 'tanto_name']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('optionチェック', function () {
            for ($i = 100; $i <= 109; $i++) {
                $model = CorpColumnSet::findOne(['column_name' =>  'option' . $i]);
                $model->data_type = BaseColumnSet::DATA_TYPE_TEXT;
                $this->checkMaxLength($model, 2000);
                $model->data_type = BaseColumnSet::DATA_TYPE_NUMBER;
                $this->checkMaxLength($model, '99999999999999999999999999999999999999999999999999');
                $model->data_type = BaseColumnSet::DATA_TYPE_MAIL;
                $this->checkMaxLength($model, 254);
                $model->data_type = BaseColumnSet::DATA_TYPE_URL;
                $this->checkMaxLength($model, 2000);
            }
        });
    }

    /**
     * max_lengthを入力できない項目は必須チェックがかからないはず
     * @param CorpColumnSet $model
     */
    protected function isEmptyArrowed(CorpColumnSet $model)
    {
        $model->max_length = null;
        $model->setScenarioByAttributes();
        $model->validate();
        verify($model->hasErrors('fullName'))->false();
    }

    /**
     * 最大値チェックと必須チェックのチェック
     * @param CorpColumnSet $model
     * @param $max
     */
    protected function checkMaxLength(CorpColumnSet $model, $max)
    {
        $model->setScenarioByAttributes();
        
        $model->max_length = null;
        $model->validate();
        verify($model->hasErrors('max_length'))->true();

        $model->max_length = 0;
        $model->validate();
        verify($model->hasErrors('max_length'))->true();

        $model->max_length = $max + 1;
        $model->validate();
        verify($model->hasErrors('max_length'))->true();
        
        $model->max_length = $max;
        $model->validate();
        verify($model->hasErrors('max_length'))->false();
    }

    /**
     * afterSaveのtest
     */
    public function testAfterSave()
    {
        $model = CorpColumnSet::findOne(['column_name' => 'corp_name']);
        $model->label = '代理店名変更同期テスト';
        $model->save();
        
        $syncModel = AdminColumnSet::findOne(['column_name' => 'corp_master_id']);
        verify($syncModel->label)->equals('代理店名変更同期テスト');
        
        $syncModel = ClientColumnSet::findOne(['column_name' => 'corp_master_id']);
        verify($syncModel->label)->equals('代理店名変更同期テスト');
        
        $syncModel = JobColumnSet::findOne(['column_name' => 'corpLabel']);
        verify($syncModel->label)->equals('代理店名変更同期テスト');
        
        $syncModel = ApplicationColumnSet::findOne(['column_name' => 'corpLabel']);
        verify($syncModel->label)->equals('代理店名変更同期テスト');

    }
}