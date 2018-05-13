<?php

namespace models\manage;

use app\models\manage\AdminColumnSet;
use app\models\manage\BaseColumnSet;
use tests\codeception\fixtures\AdminColumnSetFixture;
use tests\codeception\unit\JmTestCase;

/**
 * @group job_relations
 */
class AdminColumnSetTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(AdminColumnSet::tableName())->equals('admin_column_set');
    }

    /**
     * rulesテスト
     * 他は基底でtestしているのでmax_lengthのみtest
     * passwordとexceptionは必須チェックがかからないことを検証
     * commonMaxLengthRuleも検証
     */
    public function testRules()
    {
        $this->specify('admin_noチェック', function () {
            $model = AdminColumnSet::findOne(['column_name' => 'admin_no']);
            $this->isEmptyAllowed($model);
        });
        $this->specify('corp_master_idチェック', function () {
            $model = AdminColumnSet::findOne(['column_name' => 'corp_master_id']);
            $this->isEmptyAllowed($model);
        });
        $this->specify('client_master_idチェック', function () {
            $model = AdminColumnSet::findOne(['column_name' => 'client_master_id']);
            $this->isEmptyAllowed($model);
        });
        $this->specify('fullNameチェック', function () {
            $model = AdminColumnSet::findOne(['column_name' => 'fullName']);
            $this->isEmptyAllowed($model);
        });
        $this->specify('login_idチェック', function () {
            $model = AdminColumnSet::findOne(['column_name' => 'login_id']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('passwordチェック', function () {
            $model = AdminColumnSet::findOne(['column_name' => 'password']);
            $this->checkMaxLength($model, 255);
            $model->is_in_list = null;
            $model->validate();
            verify($model->hasErrors('is_in_list'))->false();
        });
        $this->specify('tel_noチェック', function () {
            $model = AdminColumnSet::findOne(['column_name' => 'tel_no']);
            $this->checkMaxLength($model, 30);
        });
        $this->specify('exceptionsチェック', function () {
            $model = AdminColumnSet::findOne(['column_name' => 'exceptions']);
            $this->isEmptyAllowed($model);
            $model->is_in_list = null;
            $model->validate();
            verify($model->hasErrors('is_in_list'))->false();
        });
        $this->specify('mail_addressチェック', function () {
            $model = AdminColumnSet::findOne(['column_name' => 'mail_address']);
            $this->isEmptyAllowed($model);
        });
        $this->specify('optionチェック', function () {
            for ($i = 100; $i <= 109; $i++) {
                $model = AdminColumnSet::findOne(['column_name' =>  'option' . $i]);
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
     * @param AdminColumnSet $model
     */
    protected function isEmptyAllowed(AdminColumnSet $model)
    {
        $model->max_length = null;
        $model->setScenarioByAttributes();
        $model->validate();
        verify($model->hasErrors('fullName'))->false();
    }

    /**
     * 最大値チェックと必須チェックのチェック
     * @param AdminColumnSet $model
     * @param $max
     */
    protected function checkMaxLength(AdminColumnSet $model, $max)
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
}