<?php

namespace models\manage;

use app\models\manage\ApplicationColumnSet;
use app\models\manage\ApplicationColumnSubset;
use app\models\manage\BaseColumnSet;
use Codeception\Specify;
use tests\codeception\fixtures\ApplicationColumnSetFixture;
use tests\codeception\unit\fixtures\ApplicationColumnSubsetFixture;
use Yii;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;

/**
 * @property ApplicationColumnSetFixture $application_column_set
 * @property ApplicationColumnSubsetFixture $application_column_subset
 */
class ApplicationColumnSetTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(ApplicationColumnSet::tableName())->equals('application_column_set');
    }

    /**
     * rulesテスト
     * 他は基底でtestしているのでmax_lengthのみtest
     * commonMaxLengthRuleも検証
     */
    public function testRules()
    {
        $this->specify('application_noチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'application_no']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('corpLabelチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'corpLabel']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('clientLabelチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'clientLabel']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('fullNameチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'fullName']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('fullNameKanaチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'fullNameKana']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('sexチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'sex']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('birth_dateチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'birth_date']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('pref_idチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'pref_id']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('addressチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'address']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('tel_noチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'tel_no']);
            $this->checkMaxLength($model, 30);
        });
        $this->specify('mail_addressチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'mail_address']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('occupation_idチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'occupation_id']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('self_prチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'self_pr']);
            $this->checkMaxLength($model, 2000);
        });
        $this->specify('carrier_typeチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'carrier_type']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('created_atチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'created_at']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('application_status_idチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'application_status_id']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('optionチェック', function () {
            for ($i = 100; $i <= 109; $i++) {
                $model = ApplicationColumnSet::findOne(['column_name' => "option" . $i]);
                $model->data_type = BaseColumnSet::DATA_TYPE_TEXT;
                $this->checkMaxLength($model, 2000);
                $model->data_type = BaseColumnSet::DATA_TYPE_NUMBER;
                $this->checkMaxLength($model, '99999999999999999999999999999999999999999999999999');
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
        $this->specify('column_explainチェック', function () {
            $model = ApplicationColumnSet::findOne(['column_name' => 'fullName']);
            $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_FULLNAME);
            $model = ApplicationColumnSet::findOne(['column_name' => 'fullNameKana']);
            $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_FULLNAME);
            $model = ApplicationColumnSet::findOne(['column_name' => 'tel_no']);
            $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_TELMAIL);
            $model = ApplicationColumnSet::findOne(['column_name' => 'mail_address']);
            $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_TELMAIL);
            $model = ApplicationColumnSet::findOne(['column_name' => 'self_pr']);
            $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_OTHER);
            for ($i = 100; $i <= 109; $i++) {
                $model = ApplicationColumnSet::findOne(['column_name' => "option" . $i]);
                $model->data_type = BaseColumnSet::DATA_TYPE_TEXT;
                $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_OTHER);
                $model->data_type = BaseColumnSet::DATA_TYPE_NUMBER;
                $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_OTHER);
                $model->data_type = BaseColumnSet::DATA_TYPE_MAIL;
                $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_OTHER);
                $model->data_type = BaseColumnSet::DATA_TYPE_URL;
                $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_OTHER);
                $model->data_type = BaseColumnSet::DATA_TYPE_CHECK;
                $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_OTHER);
                $model->data_type = BaseColumnSet::DATA_TYPE_RADIO;
                $this->checkMaxLengthColumnExplain($model, ApplicationColumnSet::MAX_LENGTH_OTHER);
            }
        });
    }

    /**
     * max_lengthを入力できない項目は必須チェックがかからないはず
     * @param ApplicationColumnSet $model
     */
    protected function isEmptyArrowed(ApplicationColumnSet $model)
    {
        $model->max_length = null;
        $model->setScenarioByAttributes();
        $model->validate();
        verify($model->hasErrors('fullName'))->false();
    }

    /**
     * 最大値チェックと必須チェックのチェック
     * @param ApplicationColumnSet $model
     * @param $max
     */
    protected function checkMaxLength(ApplicationColumnSet $model, $max)
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
     * 最大値チェック
     * @param ApplicationColumnSet $model
     * @param int $max
     */
    private function checkMaxLengthColumnExplain(ApplicationColumnSet $model, $max)
    {
        $model->setScenarioByAttributes();

        $value = str_repeat("a", $max);
        $model->column_explain = $value;

        //最大文字数確認
        switch ($model->column_name) {
            case'fullName':
            case'fullNameKana':
                $model->columnExplainSei = $value;
                $model->columnExplainMei = $value;
                $model->validate();
                verify($model->hasErrors('columnExplainSei'))->false();
                verify($model->hasErrors('columnExplainMei'))->false();

                //最大文字数 + 1 確認
                $value = $value . 'a';
                $model->columnExplainSei = $value;
                $model->columnExplainMei = $value;
                $model->validate();
                verify($model->hasErrors('columnExplainSei'))->true();
                verify($model->hasErrors('columnExplainMei'))->true();
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
                if ($model->data_type === BaseColumnSet::DATA_TYPE_TEXT || $model->data_type === BaseColumnSet::DATA_TYPE_NUMBER ||
                    $model->data_type === BaseColumnSet::DATA_TYPE_MAIL || $model->data_type === BaseColumnSet::DATA_TYPE_URL
                ) {
                    $model->validate();
                    verify($model->hasErrors('column_explain'))->false();
                    //最大文字数 + 1 確認
                    $value = $value . "a";
                    $model->column_explain = $value;
                    $model->validate();
                    verify($model->hasErrors('column_explain'))->true();
                };
                break;
            default:
                $model->validate();
                verify($model->hasErrors('column_explain'))->false();
                //最大文字数 + 1 確認
                $value = $value . 'a';
                $model->column_explain = $value;
                $model->validate();
                verify($model->hasErrors('column_explain'))->true();
                break;
        }
    }

    /**
     * getSubsetのtest
     */
    public function testGetSubset()
    {
        $allRecords = self::getFixtureInstance('application_column_set')->data();
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });

        foreach ($tenantRecords as $record) {
            $model = ApplicationColumnSet::findOne(['column_name' => $record['column_name']]);
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
                    verify($model->getSubset() instanceof ApplicationColumnSubset)->true();
                    break;
                default:
                    verify($model->getSubset())->false();
                    break;
            }
        }
    }

    /**
     * setScenarioByAttributesのtest
     */
    public function testSetScenarioByAttributes()
    {
        $allRecords = self::getFixtureInstance('application_column_set')->data();
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });

        foreach ($tenantRecords as $record) {
            $model = ApplicationColumnSet::findOne(['column_name' => $record['column_name']]);
            $model->setScenarioByAttributes();
            switch ($model->column_name) {
                case'tel_no':
                    verify($model->scenario)->equals(ApplicationColumnSet::SCENARIO_TEL_NO);
                    break;
                case'mail_address':
                    verify($model->scenario)->equals(ApplicationColumnSet::SCENARIO_MAIL);
                    break;
                case'self_pr':
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
                    verify($model->scenario)->equals(ApplicationColumnSet::SCENARIO_OPTION);
                    break;
                default:
                    verify($model->scenario)->equals(ApplicationColumnSet::SCENARIO_DEFAULT);
                    break;
            }
        }
    }

    /**
     * getSubsetListのtest
     */
    public function testGetSubsetList()
    {
        $allRecords = self::getFixtureInstance('application_column_set')->data();
        $allSubsetRecords = self::getFixtureInstance('application_column_subset')->data();
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });
        foreach ($tenantRecords as $record) {
            $model = ApplicationColumnSet::findOne(['column_name' => $record['column_name']]);
            $targetRecords = array_filter($allSubsetRecords, function ($subsetRecord) use ($record) {
                return $subsetRecord['column_name'] == $record['column_name'] && $subsetRecord['tenant_id'] == Yii::$app->tenant->id;
            });
            verify($model->getSubsetList())->count(count($targetRecords));
        }
    }

    /**
     * 下記メソッドをまとめてtest
     * getColumnExplainSei
     * getColumnExplainMei
     * setColumnExplainSei
     * setColumnExplainMei
     */
    public function testGetColumnExplainSeiMei()
    {
        $model = new ApplicationColumnSet();
        $model->column_explain = 'sei mei';
        verify($model->columnExplainSei)->equals('sei');
        verify($model->columnExplainMei)->equals('mei');

        $model->columnExplainSei = ' sei Sei ';
        $model->columnExplainMei = ' mei Mei ';

        verify($model->columnExplainSei)->equals('seiSei');
        verify($model->columnExplainMei)->equals('meiMei');
    }

    /**
     * beforeSaveのtest
     */
    public function testBeforeSave()
    {
        $dataTypes = [
            BaseColumnSet::DATA_TYPE_TEXT,
            BaseColumnSet::DATA_TYPE_NUMBER,
            BaseColumnSet::DATA_TYPE_MAIL,
            BaseColumnSet::DATA_TYPE_CHECK,
            BaseColumnSet::DATA_TYPE_RADIO,
            BaseColumnSet::DATA_TYPE_DATE,
            BaseColumnSet::DATA_TYPE_DROP_DOWN,
            BaseColumnSet::DATA_TYPE_URL,
        ];
        /** @var ApplicationColumnSet[] $models */
        $models = ApplicationColumnSet::find()->all();
        // column_name毎に検証
        foreach ($models as $model) {
            $model->columnExplainSei = '姓';
            $model->columnExplainMei = '名';
            // dataType毎に検証
            foreach ($dataTypes as $dataType) {
                $model->column_explain = 'sei mei';
                $model->data_type = $dataType;
                // このモデルでの新規作成はありえないので更新で検証
                $model->beforeSave(false);
                switch ($model->column_name) {
                    // ２つの項目説明文入力入力がある場合の検証
                    case 'fullName':
                    case 'fullNameKana':
                        verify($model->column_explain)->equals('姓 名');
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
                        if ($dataType == BaseColumnSet::DATA_TYPE_CHECK || $dataType == BaseColumnSet::DATA_TYPE_RADIO) {
                            // 項目説明文入力が不要な場合の検証
                            verify($model->column_explain)->equals('');
                        } else {
                            verify($model->column_explain)->equals('sei mei');
                        }
                        break;
                    default:
                        verify($model->column_explain)->equals('sei mei');
                        break;
                }
            }
        }
    }
}
