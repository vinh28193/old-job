<?php

namespace models\manage;

use app\models\manage\AdminColumnSet;
use app\models\manage\ApplicationColumnSet;
use app\models\manage\BaseColumnSet;
use app\models\manage\ClientColumnSet;
use app\models\manage\ClientDisp;
use app\models\manage\DispType;
use app\models\manage\JobColumnSet;
use tests\codeception\unit\JmTestCase;

/**
 * @group job_relations
 */
class ClientColumnSetTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(ClientColumnSet::tableName())->equals('client_column_set');
    }

    /**
     * rulesテスト
     * 他は基底でtestしているのでmax_lengthのみtest
     * commonMaxLengthRuleも検証
     */
    public function testRules()
    {
        $this->specify('client_noチェック', function () {
            $model = ClientColumnSet::findOne(['column_name' => 'client_no']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('corp_master_idチェック', function () {
            $model = ClientColumnSet::findOne(['column_name' => 'corp_master_id']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('client_nameチェック', function () {
            $model = ClientColumnSet::findOne(['column_name' => 'client_name']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('client_name_kanaチェック', function () {
            $model = ClientColumnSet::findOne(['column_name' => 'client_name_kana']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('addressチェック', function () {
            $model = ClientColumnSet::findOne(['column_name' => 'address']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('tanto_nameチェック', function () {
            $model = ClientColumnSet::findOne(['column_name' => 'tanto_name']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('tel_noチェック', function () {
            $model = ClientColumnSet::findOne(['column_name' => 'tel_no']);
            $this->checkMaxLength($model, 30);
        });
        $this->specify('client_business_outlineチェック', function () {
            $model = ClientColumnSet::findOne(['column_name' => 'client_business_outline']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('client_corporate_urlチェック', function () {
            $model = ClientColumnSet::findOne(['column_name' => 'client_corporate_url']);
            $this->checkStaticMaxLength($model, 2000);
        });
        $this->specify('optionチェック', function () {
            for ($i = 100; $i <= 109; $i++) {
                $model = ClientColumnSet::findOne(['column_name' => 'option' . $i]);
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

        $this->specify('freeword_search_flgチェック', function () {
            $model = new JobColumnSet();
            $model->freeword_search_flg = 10;
            $model->validate();
            verify($model->hasErrors('freeword_search_flg'))->true();
            $model->freeword_search_flg = JobColumnSet::FREEWORD_SEARCH_FLG;
            $model->validate();
            verify($model->hasErrors('freeword_search_flg'))->false();
        });
    }

    /**
     * max_lengthを入力できない項目は必須チェックがかからないはず
     * @param ClientColumnSet $model
     */
    protected function isEmptyArrowed(ClientColumnSet $model)
    {
        $model->max_length = null;
        $model->setScenarioByAttributes();
        $model->validate();
        verify($model->hasErrors('fullName'))->false();
    }

    /**
     * 最大値チェックと必須チェックのチェック
     * @param ClientColumnSet $model
     * @param $max
     */
    protected function checkMaxLength(ClientColumnSet $model, $max)
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
     * max_lengthが固定なcolumn_name用
     * max_lengthのnull無し
     * 最大値チェックと必須チェックのチェック
     * @param ClientColumnSet $model
     * @param $max
     */
    protected function checkStaticMaxLength(ClientColumnSet $model, $max)
    {
        $model->setScenarioByAttributes();

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
        $model = ClientColumnSet::findOne(['column_name' => 'client_name']);
        $model->label = '掲載企業名変更同期テスト';
        $model->save();

        $syncModel = AdminColumnSet::findOne(['column_name' => 'client_master_id']);
        verify($syncModel->label)->equals('掲載企業名変更同期テスト');

        $syncModel = JobColumnSet::findOne(['column_name' => 'client_master_id']);
        verify($syncModel->label)->equals('掲載企業名変更同期テスト');

        $syncModel = ApplicationColumnSet::findOne(['column_name' => 'clientLabel']);
        verify($syncModel->label)->equals('掲載企業名変更同期テスト');
    }

    /**
     * getColumnNameWithFormatのtest
     */
    public function testGetColumnNameWithFormat()
    {
        // URL検証
        $model = new ClientColumnSet();
        $model->column_name = 'testUrl';
        $model->data_type = ClientColumnSet::DATA_TYPE_URL;
        verify($model->columnNameWithFormat)->equals('testUrl:newWindowUrl');
        // Date検証
        $model = new ClientColumnSet();
        $model->column_name = 'testDate';
        $model->data_type = ClientColumnSet::DATA_TYPE_DATE;
        verify($model->columnNameWithFormat)->equals('testDate:date');
        // URL検証
        $model = new ClientColumnSet();
        $model->column_name = 'testOthers';
        $types = [
            ClientColumnSet::DATA_TYPE_CHECK,
            ClientColumnSet::DATA_TYPE_DROP_DOWN,
            ClientColumnSet::DATA_TYPE_MAIL,
            ClientColumnSet::DATA_TYPE_NUMBER,
            ClientColumnSet::DATA_TYPE_RADIO,
            ClientColumnSet::DATA_TYPE_TEXT,
        ];
        foreach ($types as $type) {
            $model->data_type = $type;
            verify($model->columnNameWithFormat)->equals('testOthers');
        }
    }

    /**
     * getClientDispのtest
     */
    public function testGetClientDisp()
    {
        $dispTypes = DispType::find()->where(['valid_chk' => DispType::VALID])->all();
        foreach ($dispTypes as $dispType) {
            $dispTypeId = $dispType->id;
            ClientColumnSet::setDispTypeId($dispTypeId);

            /** @var ClientColumnSet[] $clientColumnSets */
            $clientColumnSets = ClientColumnSet::find()->all();
            $displayItems = ClientDisp::find()->select('column_name')->where(['disp_type_id' => $dispTypeId])->column();

            foreach ($clientColumnSets as $clientColumnSet) {
                if (in_array($clientColumnSet->column_name, $displayItems)) {
                    // 表示項目な時
                    verify($clientColumnSet->clientDisp)->isInstanceOf(ClientDisp::className());
                    verify($clientColumnSet->clientDisp->disp_type_id)->equals($dispTypeId);
                } else {
                    verify($clientColumnSet->clientDisp)->null();
                }
            }
        }
    }
}
