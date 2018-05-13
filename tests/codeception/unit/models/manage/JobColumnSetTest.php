<?php

namespace models\manage;

use app\models\manage\ApplicationColumnSet;
use app\models\manage\BaseColumnSet;
use app\models\manage\DispType;
use app\models\manage\JobColumnSet;
use app\models\manage\JobColumnSubset;
use app\models\manage\JobMaster;
use app\models\manage\ListDisp;
use app\models\manage\MainDisp;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * @group job_relations
 */
class JobColumnSetTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(JobColumnSet::tableName())->equals('job_column_set');
    }

    /**
     * rulesテスト
     * 他は基底でtestしているのでmax_lengthのみtest
     * commonMaxLengthRuleも検証
     *  268 assertions
     */
    public function testRules()
    {
        $this->specify('job_noチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'job_no']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('job_search_numberチェック', function () {
            $this->checkCommonItem('job_search_number');
        });
        $this->specify('corpLabelチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'corpLabel']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('client_charge_plan_idチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'client_charge_plan_id']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('client_master_idチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'client_master_id']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('disp_start_dateチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'disp_start_date']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('disp_end_dateチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'disp_end_date']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('corp_name_dispチェック', function () {
            $this->checkCommonItem('corp_name_disp');
        });
        $this->specify('job_prチェック', function () {
            $this->checkCommonItem('job_pr');
        });
        $this->specify('main_copyチェック', function () {
            $this->checkCommonItem('main_copy');
        });
        $this->specify('job_pr2チェック', function () {
            $this->checkCommonItem('job_pr2');
        });
        $this->specify('main_copy2チェック', function () {
            $this->checkCommonItem('main_copy2');
        });
        $this->specify('job_type_textチェック', function () {
            $this->checkCommonItem('job_type_text');
        });
        $this->specify('work_placeチェック', function () {
            $this->checkCommonItem('work_place');
        });
        $this->specify('stationチェック', function () {
            $this->checkCommonItem('station');
        });
        $this->specify('map_urlチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'map_url']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('wage_textチェック', function () {
            $this->checkCommonItem('wage_text');
        });
        $this->specify('transportチェック', function () {
            $this->checkCommonItem('transport');
        });
        $this->specify('work_periodチェック', function () {
            $this->checkCommonItem('work_period');
        });
        $this->specify('work_time_textチェック', function () {
            $this->checkCommonItem('work_time_text');
        });
        $this->specify('requirementチェック', function () {
            $this->checkCommonItem('requirement');
        });
        $this->specify('conditionsチェック', function () {
            $this->checkCommonItem('conditions');
        });
        $this->specify('holidaysチェック', function () {
            $this->checkCommonItem('holidays');
        });
        $this->specify('job_commentチェック', function () {
            $this->checkCommonItem('job_comment');
        });
        $this->specify('applicationチェック', function () {
            $this->checkCommonItem('application');
        });
        $this->specify('application_tel_1チェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'application_tel_1']);
            $this->checkMaxLength($model, 30);
        });
        $this->specify('application_tel_2チェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'application_tel_2']);
            $this->checkMaxLength($model, 30);
        });
        $this->specify('application_placeチェック', function () {
            $this->checkCommonItem('application_place');
        });
        $this->specify('application_staff_nameチェック', function () {
            $this->checkCommonItem('application_staff_name');
        });
        $this->specify('application_mailチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'application_mail']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('agent_nameチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'agent_name']);
            $this->checkMaxLength($model, 255);
        });
        $this->specify('mail_bodyチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'mail_body']);
            $this->isEmptyArrowed($model);
        });
        $this->specify('optionチェック', function () {
            for ($i = 100; $i <= 109; $i++) {
                $model = JobColumnSet::findOne(['column_name' => 'option' . $i]);
                $this->checkCommonItem('option' . $i);
                $model->data_type = BaseColumnSet::DATA_TYPE_CHECK;
                $this->isEmptyArrowed($model);
                $model->data_type = BaseColumnSet::DATA_TYPE_RADIO;
                $this->isEmptyArrowed($model);
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

        $this->specify('column_explainチェック', function () {
            $model = JobColumnSet::findOne(['column_name' => 'job_search_number']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'corp_name_disp']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'job_pr']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'main_copy']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'job_pr2']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'main_copy2']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'job_type_text']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'work_place']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'station']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'map_url']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'wage_text']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'transport']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'work_period']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'work_time_text']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'requirement']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'conditions']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'holidays']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'job_comment']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'application']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'application_tel_1']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'application_tel_2']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'application_place']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'application_staff_name']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'application_mail']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'agent_name']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            $model = JobColumnSet::findOne(['column_name' => 'mail_body']);
            $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            for ($i = 100; $i <= 109; $i++) {
                $model = JobColumnSet::findOne(['column_name' => 'option' . $i]);
                $model->data_type = BaseColumnSet::DATA_TYPE_TEXT;
                $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
                $model->data_type = BaseColumnSet::DATA_TYPE_NUMBER;
                $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
                $model->data_type = BaseColumnSet::DATA_TYPE_MAIL;
                $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
                $model->data_type = BaseColumnSet::DATA_TYPE_URL;
                $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
                $model->data_type = BaseColumnSet::DATA_TYPE_CHECK;
                $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
                $model->data_type = BaseColumnSet::DATA_TYPE_RADIO;
                $this->checkMaxLengthColumnExplain($model, JobColumnSet::MAX_LENGTH_EXPLAIN);
            }
        });
    }

    /**
     * max_lengthを入力できない項目は必須チェックがかからないはず
     * @param JobColumnSet $model
     */
    protected function isEmptyArrowed(JobColumnSet $model)
    {
        $model->max_length = null;
        $model->setScenarioByAttributes();
        $model->validate();
        verify($model->hasErrors('fullName'))->false();
    }

    /**
     * 最大値チェックと必須チェックのチェック
     * @param JobColumnSet $model
     * @param $max
     */
    protected function checkMaxLength(JobColumnSet $model, $max)
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
     * @param JobColumnSet $model
     * @param int $max
     */
    private function checkMaxLengthColumnExplain(JobColumnSet $model, $max)
    {
        $model->setScenarioByAttributes();

        // チェック用文字列作成
        $value = str_repeat('a', $max);
        $model->column_explain = $value;

        //最大文字数確認
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
                if ($model->data_type === BaseColumnSet::DATA_TYPE_TEXT || $model->data_type === BaseColumnSet::DATA_TYPE_NUMBER ||
                    $model->data_type === BaseColumnSet::DATA_TYPE_MAIL || $model->data_type === BaseColumnSet::DATA_TYPE_URL
                ) {
                    // 1000文字チェック
                    $model->validate();
                    verify($model->hasErrors('column_explain'))->false();
                    // 1001文字チェック
                    $model->column_explain = $value . 'a';
                    $model->validate();
                    verify($model->hasErrors('column_explain'))->true();
                    // NULLチェック
                    $model->column_explain = null;
                    $model->validate();
                    verify($model->hasErrors('column_explain'))->false();
                }
                break;
            default:
                // 1000文字チェック
                $model->validate();
                verify($model->hasErrors('column_explain'))->false();
                // 1001文字チェック
                $model->column_explain = $value . 'a';
                $model->validate();
                verify($model->hasErrors('column_explain'))->true();
                // NULLチェック
                $model->column_explain = null;
                $model->validate();
                verify($model->hasErrors('column_explain'))->false();
                break;
        }
    }

    /**
     * オプション項目ではないテキスト型の一般項目チェック
     * @param $columnName
     */
    protected function checkCommonItem($columnName)
    {
        $model = JobColumnSet::findOne(['column_name' => $columnName]);
        $model->data_type = BaseColumnSet::DATA_TYPE_TEXT;
        $this->checkMaxLength($model, 2000);
        $model->data_type = BaseColumnSet::DATA_TYPE_NUMBER;
        $this->checkMaxLength($model, '99999999999999999999999999999999999999999999999999');
        $model->data_type = BaseColumnSet::DATA_TYPE_MAIL;
        $this->checkMaxLength($model, 254);
        $model->data_type = BaseColumnSet::DATA_TYPE_URL;
        $this->checkMaxLength($model, 2000);
    }

    /**
     * getSubsetのtest
     * 50 assertions
     */
    public function testGetSubset()
    {
        $allRecords = ArrayHelper::toArray($this->getFixture('job_column_set'));
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });

        foreach ($tenantRecords as $record) {
            $model = JobColumnSet::findOne(['column_name' => $record['column_name']]);
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
                    verify($model->getSubset() instanceof JobColumnSubset)->true();
                    break;
                default:
                    verify($model->getSubset())->false();
                    break;
            }
        }
    }

    /**
     * setScenarioByAttributesのtest
     * 50 assertions
     */
    public function testSetScenarioByAttributes()
    {
        $allRecords = ArrayHelper::toArray($this->getFixture('job_column_set'));
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });

        foreach ($tenantRecords as $record) {
            $model = JobColumnSet::findOne(['column_name' => $record['column_name']]);
            $model->setScenarioByAttributes();
            switch ($model->column_name) {
                case'application_tel_1':
                case'application_tel_2':
                    verify($model->scenario)->equals(JobColumnSet::SCENARIO_TEL_NO);
                    break;
                case'agent_name':
                    verify($model->scenario)->equals(JobColumnSet::SCENARIO_AGENT_NAME);
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
                    verify($model->scenario)->equals(ApplicationColumnSet::SCENARIO_OPTION);
                    break;
                default:
                    verify($model->scenario)->equals(JobColumnSet::SCENARIO_DEFAULT);
                    break;
            }
        }
    }

    /**
     * getSourceのtest
     * 50 assertions
     */
    public function testGetSource()
    {
        $allRecords = ArrayHelper::toArray($this->getFixture('job_column_set'));
        $allSubsetRecords = ArrayHelper::toArray($this->getFixture('job_column_subset'));
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });
        foreach ($tenantRecords as $record) {
            $model = JobColumnSet::findOne(['column_name' => $record['column_name']]);
            $targetRecords = array_filter($allSubsetRecords, function ($subsetRecord) use ($record) {
                return $subsetRecord['column_name'] == $record['column_name'] && $subsetRecord['tenant_id'] == Yii::$app->tenant->id;
            });
            verify($model->getSource())->count(count($targetRecords));
        }
    }

    /**
     * getEditableのtest
     * HTMLを吐き出すため、unitテストはemptyチェックのみとします
     * 49 assertions
     */
    public function testGetEditable()
    {
        $allRecords = ArrayHelper::toArray($this->getFixture('job_column_set'));
        $tenantRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id && $record['column_name'] != 'corpLabel';
        });
        foreach ($tenantRecords as $record) {
            $model = JobColumnSet::findOne(['column_name' => $record['column_name']]);
            $jobMaster = JobMaster::findOne(rand(1, JobMasterFixture::RECORDS_PER_TENANT));
            verify($model->getEditable($jobMaster))->notEmpty();
        }
    }

    /**
     * getColumnNameWithFormatとgetFormattedAttributeWithoutNewLineとcolumnNameWithFormatのtest
     */
    public function testGetColumnNameWithFormat()
    {
        // 個別検証
        $model = JobColumnSet::findOne(['column_name' => 'client_master_id']);
        verify($model->columnNameWithFormat)->equals('clientMaster.client_name');
        $model = JobColumnSet::findOne(['column_name' => 'map_url']);
        verify($model->columnNameWithFormat)->equals('map_url:mapUrl');
        $model = JobColumnSet::findOne(['column_name' => 'disp_start_date']);
        verify($model->columnNameWithFormat)->equals('disp_start_date:date');
        $model = JobColumnSet::findOne(['column_name' => 'disp_end_date']);
        verify($model->columnNameWithFormat)->equals('disp_end_date:date');

        // 汎用検証
        $model = new JobColumnSet(['column_name' => 'col']);

        $model->data_type = JobColumnSet::DATA_TYPE_TEXT;
        verify($model->columnNameWithFormat)->equals('col:jobView');
        verify($model->formattedAttributeWithoutNewLine)->equals('col');

        $model->data_type = JobColumnSet::DATA_TYPE_URL;
        verify($model->columnNameWithFormat)->equals('col:newWindowUrl');
        verify($model->formattedAttributeWithoutNewLine)->equals('col:newWindowUrl');

        $model->data_type = JobColumnSet::DATA_TYPE_NUMBER;
        verify($model->columnNameWithFormat)->equals('col');
        verify($model->formattedAttributeWithoutNewLine)->equals('col');

        $model->data_type = JobColumnSet::DATA_TYPE_MAIL;
        verify($model->columnNameWithFormat)->equals('col');
        verify($model->formattedAttributeWithoutNewLine)->equals('col');

        $model->data_type = JobColumnSet::DATA_TYPE_CHECK;
        verify($model->columnNameWithFormat)->equals('col');
        verify($model->formattedAttributeWithoutNewLine)->equals('col');

        $model->data_type = JobColumnSet::DATA_TYPE_RADIO;
        verify($model->columnNameWithFormat)->equals('col');
        verify($model->formattedAttributeWithoutNewLine)->equals('col');
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
        $models = JobColumnSet::find()->all();
        foreach ($models as $model) {
            // dataType毎に検証
            foreach ($dataTypes as $dataType) {
                $model->data_type = $dataType;
                $model->column_explain = 'aaaaaaaaaa';
                // このモデルでの新規作成はありえないので更新で検証
                $model->beforeSave(false);
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
                        if ($model->data_type === BaseColumnSet::DATA_TYPE_CHECK || $model->data_type === BaseColumnSet::DATA_TYPE_RADIO) {
                            // 項目説明文入力が不要な場合の検証
                            verify($model->column_explain)->equals('');
                        } else {
                            verify($model->column_explain)->equals('aaaaaaaaaa');
                        }
                        break;
                    default:
                        $model->column_explain = 'aaaaaaaaaa';
                        break;
                }
            }
        }
    }

    /**
     * getExplainのtest
     */
    public function testGetExplain()
    {
        $model = new JobColumnSet();
        $model->column_explain = '改行
<script>alert(\'test\');</script>';

        verify($model->explain)->equals('<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>改行<br />
&lt;script&gt;alert(&#039;test&#039;);&lt;/script&gt;');
    }

    /**
     * getMainDispsのtest
     */
    public function testGetMainDisps()
    {
        $mainDisps = MainDisp::find()->all();
        foreach ($mainDisps as $mainDisp) {
            /** @var JobColumnSet $jobColumnSet */
            $jobColumnSet = JobColumnSet::find()->where(['column_name' => $mainDisp['column_name']])->one();

            if ($jobColumnSet) {
                verify($jobColumnSet->mainDisps)->notEmpty();
                foreach ($jobColumnSet->mainDisps as $mainDispCheck) {
                    verify($mainDispCheck)->isInstanceOf(MainDisp::className());
                    verify($mainDispCheck->disp_chk)->equals(1);
                }
            }
        }
    }

    /**
     * getListDispsのtest
     */
    public function testGetListDisps()
    {
        $listDisps = ListDisp::find()->all();
        foreach ($listDisps as $listDisp) {
            $jobColumnSet = JobColumnSet::find()->where(['column_name' => $listDisp['column_name']])->one();
            verify($jobColumnSet->listDisps)->notEmpty();
            foreach ($jobColumnSet->listDisps as $listDispCheck) {
                verify($listDispCheck)->isInstanceOf(ListDisp::className());
            }
        }
    }

    /**
     * getListDispのtest
     */
    public function testGetListDisp()
    {
        $dispTypes = DispType::find()->where(['valid_chk' => DispType::VALID])->all();
        foreach ($dispTypes as $dispType) {
            $dispTypeId = $dispType->id;
            JobColumnSet::setDispTypeId($dispTypeId);
            /** @var JobColumnSet[] $jobColumnSets */
            $jobColumnSets = JobColumnSet::find()->all();
            $displayItems = ListDisp::find()->select('column_name')->where(['disp_type_id' => $dispTypeId])->column();
            foreach ($jobColumnSets as $jobColumnSet) {
                if (in_array($jobColumnSet->column_name, $displayItems)) {
                    // 表示項目な時
                    verify($jobColumnSet->listDisp)->isInstanceOf(ListDisp::className());
                    verify($jobColumnSet->listDisp->disp_type_id)->equals($dispTypeId);
                } else {
                    verify($jobColumnSet->listDisp)->null();
                }
            }
        }
    }

    /**
     * getMainDispのtest
     */
    public function testGetMainDisp()
    {
        $dispTypes = DispType::find()->where(['valid_chk' => DispType::VALID])->all();
        foreach ($dispTypes as $dispType) {
            $dispTypeId = $dispType->id;
            JobColumnSet::setDispTypeId($dispTypeId);
            /** @var JobColumnSet[] $jobColumnSets */
            $jobColumnSets = JobColumnSet::find()->all();
            $displayItems = MainDisp::find()
                ->select('column_name')
                ->where([
                    'disp_type_id' => $dispTypeId,
                    'disp_chk' => MainDisp::FLAG_VALID,
                ])
                ->column();
            foreach ($jobColumnSets as $jobColumnSet) {
                if (in_array($jobColumnSet->column_name, $displayItems)) {
                    // 表示項目な時
                    verify($jobColumnSet->mainDisp)->isInstanceOf(MainDisp::className());
                    verify($jobColumnSet->mainDisp->disp_type_id)->equals($dispTypeId);
                    verify($jobColumnSet->mainDisp->disp_chk)->equals(MainDisp::FLAG_VALID);
                } else {
                    verify($jobColumnSet->mainDisp)->null();
                }
            }
        }
    }
}
