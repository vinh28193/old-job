<?php
namespace models\manage;

use Yii;
use app\models\manage\InquiryColumnSet;
use app\models\manage\InquiryColumnSubset;
use app\models\manage\BaseColumnSet;
use tests\codeception\unit\JmTestCase;
use tests\codeception\fixtures\InquiryColumnSetFixture;
use tests\codeception\fixtures\InquiryColumnSubsetFixture;

/**
 * @property InquiryColumnSetFixture $inquiry_column_set
 * @property InquiryColumnSubsetFixture $inquiry_column_subset
 */
class InquiryColumnSetTest extends JmTestCase
{
    public function testTableName()
    {
        verify(InquiryColumnSet::tableName())->equals('inquiry_column_set');
    }

    /**
     * rulesテスト
     * 他は基底でtestしているのでmax_lengthのみtest
     * commonMaxLengthRuleも検証
     */
    public function testRules()
    {
        $this->specify('company_nameチェック', function () {
            $model = InquiryColumnSet::findOne(['column_name' => 'company_name']);
            $this->checkMaxLength($model, 255);
        });

        $this->specify('post_nameチェック', function () {
            $model = InquiryColumnSet::findOne(['column_name' => 'post_name']);
            $this->checkMaxLength($model, 255);
        });

        $this->specify('tanto_nameチェック', function () {
            $model = InquiryColumnSet::findOne(['column_name' => 'tanto_name']);
            $this->checkMaxLength($model, 255);
        });

        $this->specify('job_typeチェック', function () {
            $model = InquiryColumnSet::findOne(['column_name' => 'job_type']);
            $this->checkMaxLength($model, 255);
        });

        $this->specify('postal_codeチェック', function () {
            $model = InquiryColumnSet::findOne(['column_name' => 'postal_code']);
            $this->isEmptyArrowed($model);
        });

        $this->specify('addressチェック', function () {
            $model = InquiryColumnSet::findOne(['column_name' => 'address']);
            $this->checkMaxLength($model, 255);
        });

        $this->specify('tel_noチェック', function () {
            $model = InquiryColumnSet::findOne(['column_name' => 'tel_no']);
            $this->checkMaxLength($model, 30);
        });

        $this->specify('fax_noチェック', function () {
            $model = InquiryColumnSet::findOne(['column_name' => 'fax_no']);
            $this->checkMaxLength($model, 30);
        });

        $this->specify('mail_addressチェック', function () {
            $model = InquiryColumnSet::findOne(['column_name' => 'mail_address']);
            $this->checkMaxLength($model, 254);
        });

        $this->specify('optionチェック', function () {
            for ($i = 100; $i <= 109; $i++) {
                $model = InquiryColumnSet::findOne(['column_name' => "option" . $i]);
                $model->data_type = BaseColumnSet::DATA_TYPE_TEXT;
                $this->checkMaxLength($model, 2000);
                $model->data_type = BaseColumnSet::DATA_TYPE_NUMBER;
                $this->checkMaxLength($model, str_repeat(9,50));
                $model->data_type = BaseColumnSet::DATA_TYPE_MAIL;
                $this->checkMaxLength($model, 254);
                $model->data_type = BaseColumnSet::DATA_TYPE_URL;
                $this->checkMaxLength($model, 2000);
                $model->data_type = BaseColumnSet::DATA_TYPE_CHECK;
                $this->isEmptyArrowed($model);
                $model->data_type = BaseColumnSet::DATA_TYPE_RADIO;
                $this->isEmptyArrowed($model);
            }
        });
    }

    /**
     * max_lengthを入力できない項目は必須チェックがかからないはず
     * @param InquiryColumnSet $model
     */
    protected function isEmptyArrowed(InquiryColumnSet $model)
    {
        $model->max_length = null;
        $model->setScenarioByAttributes();
        $model->validate();
        verify($model->hasErrors('max_length'))->false();
    }

    /**
     * 最大値チェックと必須チェックのチェック
     * @param InquiryColumnSet $model
     * @param $max
     * function will be check max length without requite attribute
     */
    protected function checkMaxLength(InquiryColumnSet $model, $max)
    {
        $model->setScenarioByAttributes();

        $model->max_length = null;
        $model->validate();
        verify($model->hasErrors('max_length'))->true();

        $model->max_length = 0;
        $model->validate();
        verify($model->hasErrors('max_length'))->true();

        $model->max_length = $max;
        $model->validate();
        verify($model->hasErrors('max_length'))->false();

        $model->max_length = $max + 1;
        $model->validate();
        verify($model->hasErrors('max_length'))->true();
    }

    /**
     * placeholderのgetterのtest
     */
    public function testGetPlaceholder()
    {
        $model = new InquiryColumnSet;
        $model->max_length = 200;
        $model->data_type = InquiryColumnSet::DATA_TYPE_NUMBER;
        verify($model->placeholder)->equals(Yii::t('app', '最大値:200'));
        $model->data_type = InquiryColumnSet::DATA_TYPE_TEXT;
        verify($model->placeholder)->equals(Yii::t('app', '最大200文字'));
    }

    /**
     * beforeValidateのtest
     */
    public function testBeforeValidate()
    {
        $model = new InquiryColumnSet;
        $model->beforeValidate();
        verify($model->is_in_list)->equals(BaseColumnSet::NOT_IN_LIST);
    }

    /**
     * getSubsetListのtest
     */
    public function testGetSubsetList()
    {

        $allRecords = self::getFixtureInstance('inquiry_column_set')->data();
        $allSubsetRecords = self::getFixtureInstance('inquiry_column_subset')->data();
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });
        foreach ($tenantRecords as $record) {
            $model = InquiryColumnSet::findOne(['column_name' => $record['column_name']]);
            $targetRecords = array_filter($allSubsetRecords, function ($subsetRecord) use ($record) {
                return $subsetRecord['column_name'] == $record['column_name'] && $subsetRecord['tenant_id'] == Yii::$app->tenant->id;
            });
            verify($model->getSubsetList())->count(count($targetRecords));
        }
    }

    /**
     * getSubsetのtest
     */
    public function testGetSubset()
    {
        $allRecords = self::getFixtureInstance('inquiry_column_subset')->data();
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });

        foreach ($tenantRecords as $record) {
            $model = InquiryColumnSet::findOne(['column_name' => $record['column_name']]);
            switch ($model->column_name) {
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
                    verify($model->getSubset() instanceof InquiryColumnSubset)->true();
                    break;
                default:
                    verify($model->getSubset())->false();
                    break;
            }
        }
    }
}

?>