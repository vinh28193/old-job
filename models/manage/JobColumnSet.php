<?php

namespace app\models\manage;

use yii;
use app\common\CustomEditable;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\modules\manage\controllers\secure\CsvHelperController;
use app\common\Helper\JmUtils;

/**
 * This is the model class for table "job_column_set".
 * @property integer $freeword_search_flg
 * @property bool $short_display
 * @property bool $search_result_display
 * @property string $column_explain
 *
 * @property MainDisp[] $mainDisps
 * @property ListDisp[] $listDisps
 * @property ListDisp $listDisp
 * @property MainDisp $mainDisp
 * @property JobColumnSubset[] $subsetItems
 * @property array $source
 * @property string $columnNameWithFormat
 * @property string $formattedAttributeWithoutNewLine
 * @property string $description CSV入力規則画面の説明
 * @property string $explain htmlEncodeして改行反映された項目説明文
 */
class JobColumnSet extends BaseColumnSet
{
    // todo 画像はほぼ全部固定で項目管理にも出さないので、そもそもレコードをなくすかもしれない。
    /** labelが固定なレコードのcolumn_name */
    const STATIC_LABEL = [
        'corpLabel',
        'client_master_id',
    ];
    /** data_typeが固定なレコードのcolumn_name */
    const STATIC_DATA_TYPE = [
        'job_no',
        'corpLabel',
        'client_charge_plan_id',
        'client_master_id',
        'disp_start_date',
        'disp_end_date',
        'map_url',
        'application_tel_1',
        'application_tel_2',
        'application_mail',
        'agent_name',
        'mail_body',
        /*
         * TODO:job_masterの画像ファイル関係のカラムの修正の影響で、この辺修正の必要あるかもしれない。
         * タスク管理表に追加。（https://trello.com/c/7zLa8pqc/690-job-column-set）
         */
        'media_upload_id_1',
        'media_upload_id_2',
        'media_upload_id_3',
        'media_upload_id_4',
        'media_upload_id_5',
        'job_pict_text_3',
        'job_pict_text_4',
        'job_pict_text_5',
    ];
    /** max_lengthが固定なレコードのcolumn_name BaseColumnSetで使用しているので注意 */
    const STATIC_MAX_LENGTH = [
        'job_no',
        'corpLabel',
        'client_charge_plan_id',
        'client_master_id',
        'disp_start_date',
        'disp_end_date',
        'map_url',
        'application_mail', // 254固定（ColumnSetにてセット）
        'mail_body', // 2000固定
    ];
    /** is_mustが固定なレコードのcolumn_name */
    const STATIC_IS_MUST = [
        'job_no',
        'corpLabel',
        'client_charge_plan_id',
        'client_master_id',
        'disp_start_date',
    ];
    /** is_in_listが固定なレコードのcolumn_name */
    const STATIC_IS_IN_LIST = [];
    /** in_searchが固定なレコードのcolumn_name */
    const STATIC_IS_IN_SEARCH = [
        'corpLabel',
        'client_charge_plan_id',
        'client_master_id',
        'disp_start_date',
        'disp_end_date',
    ];
    /** valid_chkが固定なレコードのcolumn_name */
    const STATIC_VALID_CHK = [
        'job_no',
        'corpLabel',
        'client_charge_plan_id',
        'client_master_id',
        'disp_start_date',
        'disp_end_date',
    ];
    /** freeword_search_flgが固定なレコードのcolumn_name */
    const STATIC_FREEWORD_SEARCH_FLG = [
        'corpLabel',
        'client_charge_plan_id',
        'disp_start_date',
        'disp_end_date',
        'map_url',
        'application_mail',
        'mail_body',
    ];
    /** column_explainが固定なレコードのcolumn_name */
    const STATIC_COLUMN_EXPLAIN = [];
    /** 募集要項では入力しないレコードのcolumn_name */
    const NOT_REQUIREMENT = [
        'job_no',
        'corpLabel',
        'client_charge_plan_id',
        'client_master_id',
        'disp_start_date',
        'disp_end_date',
        'application_mail',
        'agent_name',
        'mail_body',
    ];
    /** チェックボックス・ラジオボタンのdata_typeを選択出来るレコードのcolumn_name */
    const OPTION_TYPE = [
        'option100',
        'option101',
        'option102',
        'option103',
        'option104',
        'option105',
        'option106',
        'option107',
        'option108',
        'option109',
    ];
    /** job_masterの対象カラムがtext型ではなくstring型になっているレコードのcolumn_name */
    const STRING = ['agent_name'];
    /** 電話番号が入るcolumn_name */
    const TEL = [
        'application_tel_1',
        'application_tel_2',
    ];
    /** シナリオ */
    const SCENARIO_AGENT_NAME = 'agent_name';

    /** column_explainの最大入力文字数 **/
    const MAX_LENGTH_EXPLAIN = 1000;

    /** list_dispに入ることの出来ないcolumn_name */
    const NOT_AVAILABLE_LIST_DISP_ITEMS = [
        'job_no',
        'corpLabel',
        'client_charge_plan_id',
        'client_master_id',
        'disp_start_date',
        'disp_end_date',
        'application_tel_1',
        'application_tel_2',
        'application_mail',
        'agent_name',
        'mail_body',
        'media_upload_id_1',
        'media_upload_id_2',
        'media_upload_id_3',
        'media_upload_id_4',
        'media_upload_id_5',
        'job_pict_text_3',
        'job_pict_text_4',
        'job_pict_text_5',
    ];

    /** list_dispに入ることの出来ないcolumn_name */
    const NOT_AVAILABLE_MAIN_DISP_ITEMS = [
        'job_no',
        'corpLabel',
        'client_charge_plan_id',
        'client_master_id',
        'disp_start_date',
        'disp_end_date',
        'application_tel_1',
        'application_tel_2',
        'application_mail',
        'agent_name',
        'mail_body',
    ];

    public static $dispTypeId;

    public function init()
    {
        parent::init();
        $this->relationClassName = JobColumnSubset::className();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_column_set';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['freeword_search_flg', 'boolean'],
            // 対象カラムがstring型の場合(agent_nameのみ該当)
            ['max_length', 'integer', 'max' => 255, 'on' => self::SCENARIO_AGENT_NAME],
            // 対象カラムがtext型の場合（テキスト属性とURL属性がある）
            [
                'max_length',
                'integer',
                'max' => 2000,
                'when' => function ($model) {
                    return
                        $model->data_type == BaseColumnSet::DATA_TYPE_TEXT ||
                        $model->data_type == BaseColumnSet::DATA_TYPE_URL;
                },
                'whenClient' => $this->getWhenClientJs([
                    BaseColumnSet::DATA_TYPE_TEXT,
                    BaseColumnSet::DATA_TYPE_URL,
                ]),
            ],
            [
                'column_explain',
                'string',
                'max' => self::MAX_LENGTH_EXPLAIN,
                'when' => function ($model) {
                    return
                        $model->data_type == self::DATA_TYPE_TEXT ||
                        $model->data_type == self::DATA_TYPE_URL ||
                        $model->data_type == self::DATA_TYPE_NUMBER ||
                        $model->data_type == self::DATA_TYPE_MAIL;
                },
                'whenClient' => $this->getWhenClientJs([
                    self::DATA_TYPE_TEXT,
                    self::DATA_TYPE_URL,
                    self::DATA_TYPE_NUMBER,
                    self::DATA_TYPE_MAIL,
                    null,
                ]),
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'freeword_search_flg' => Yii::t('app', 'フリーワード検索'),
            'valid_chk' => Yii::t('app', '項目使用状況'),
        ]);
    }

    /**
     * optionのdataType
     * @return array
     */
    public function getOptionTypeArray()
    {
        return [
            self::DATA_TYPE_CHECK => Yii::t('app', 'チェックボックス'),
            self::DATA_TYPE_RADIO => Yii::t('app', 'プルダウン'),
        ];
    }

    /**
     * subsetを持ちうる項目かどうか
     * @return bool|JobColumnSubset
     */
    public function getSubset()
    {
        return $this->isOption() ? new JobColumnSubset() : false;
    }

    /**
     * attributeを元にシナリオをセットする
     */
    public function setScenarioByAttributes()
    {
        if (ArrayHelper::isIn($this->column_name, self::TEL)) {
            $this->scenario = self::SCENARIO_TEL_NO;
        } elseif ($this->column_name == 'agent_name') {
            $this->scenario = self::SCENARIO_AGENT_NAME;
        }
    }

    /**
     * Disp relation
     * @return ActiveQuery
     */
    public function getMainDisps()
    {
        return $this->hasMany(MainDisp::className(), ['column_name' => 'column_name', 'tenant_id' => 'tenant_id'])->where(['disp_chk' => 1]);
    }

    /**
     * ListDisp relation
     * @return ActiveQuery
     */
    public function getListDisps()
    {
        return $this->hasMany(ListDisp::className(), ['column_name' => 'column_name', 'tenant_id' => 'tenant_id']);
    }

    /**
     * dispTypeId別ListDisp relation
     * @return ActiveQuery
     */
    public function getListDisp()
    {
        return $this->hasOne(ListDisp::className(), ['column_name' => 'column_name'])->onCondition(['disp_type_id' => self::$dispTypeId]);
    }

    /**
     * dispTypeId別MainDisp relation
     * @return ActiveQuery
     */
    public function getMainDisp()
    {
        return $this->hasOne(MainDisp::className(), ['column_name' => 'column_name'])->onCondition(['disp_type_id' => self::$dispTypeId, 'disp_chk' => MainDisp::FLAG_VALID]);
    }

    /**
     * relation用のdispTypeIdをセットする
     * @param $dispTypeId
     */
    public static function setDispTypeId($dispTypeId)
    {
        self::$dispTypeId = $dispTypeId;
    }

    /**
     * @return array
     */
    public function getSource()
    {
        return ArrayHelper::getColumn($this->subsetItems, function (JobColumnSubset $item) {
            return ['value' => $item->subset_name, 'text' => $item->subset_name];
        });
    }

    /**
     * @return array
     */
    public function getFormatTable()
    {
        return array_merge(parent::getFormatTable(), [
            'freeword_search_flg' => self::getFreewordSearchFlgArray() + [null => Yii::t('app', '対象外')],
            'valid_chk' =>self::getValidArray(),
        ]);
    }

    /**
     * 有効無効の配列をオーバーライド
     * @return array
     */
    public static function getValidArray()
    {
        return [
            self::VALID => Yii::t('app', '使用する'),
            self::INVALID => Yii::t('app', '使用しない'),
        ];
    }

    /**
     * @param JobMaster $model
     * @return string
     * @throws \Exception
     */
    public function getEditable(JobMaster $model)
    {
        $display = '';
        $clientOptions['emptytext'] = $this->label;
        $countType = 'character';
        switch ($this->data_type) {
            case BaseColumnSet::DATA_TYPE_CHECK:
                $type = 'checklist';
                $clientOptions['source'] = $this->source;
                $clientOptions['value'] = explode(',', $model->{$this->column_name});
                $clientOptions['tpl'] = '<ul></ul>';
                $display = <<<JS
function(value, sourceData) {
    var html = [],
    checked = $.fn.editableutils.itemsByValue(value, sourceData);
    if(checked.length) {
        $.each(checked, function(i, v) { html.push($.fn.editableutils.escape(v.text)); });
        $(this).html(html.join(', '));
    }else{
        $(this).empty();
    }
}
JS;
                break;
            case BaseColumnSet::DATA_TYPE_RADIO:
                $type = 'select';
                $clientOptions['source'] = $this->source;
                $clientOptions['prepend'] = Yii::t('app', '--選択してください--');
                $clientOptions['value'] = $model->{$this->column_name};
                $clientOptions['inputclass'] = 'form-control';
                $display = <<<JS
function(value, sourceData) {
    if(value) {
        $(this).html(value);
    }else{
        $(this).empty();
    }
}
JS;
                break;
            case BaseColumnSet::DATA_TYPE_TEXT;
                $type = 'textarea';
                $clientOptions['tpl'] = '<textarea style="width:100%;"></textarea>';
                $clientOptions['rows'] = 1;
                $clientOptions['emptytext'] = $this->label;
                break;
            case BaseColumnSet::DATA_TYPE_NUMBER:
                $type = 'text';
                $countType = 'number';
                break;
            default :
                $type = 'text';
                break;
        }

        return CustomEditable::widget([
            'model' => $model,
            'display' => $display,
            'attribute' => $this->column_name,
            'type' => $type,
            'options' => ['id' => 'list-' . $this->column_name, 'style' => 'cursor:pointer'],
            'maxLength' => $this->max_length,
            'clientOptions' => $clientOptions,
            'countType' => $countType,
            'hint' => $this->explain,
        ]);
    }

    /**
     * format情報を添えたcolumn_nameを改行を反映させる形で返す
     * @return string
     */
    public function getColumnNameWithFormat()
    {
        return $this->columnNameWithFormat(true);
    }

    /**
     * format情報を添えたcolumn_nameを改行を反映させない形で返す
     * @return string
     */
    public function getFormattedAttributeWithoutNewLine()
    {
        return $this->columnNameWithFormat(false);
    }

    /**
     * format情報を添えたcolumn_nameを返す
     * 引数によって改行を反映するか否かを切り替える
     * @param bool $availableNewLine
     * @return string
     */
    public function columnNameWithFormat($availableNewLine)
    {
        if ($this->column_name == 'client_master_id') {
            return 'clientMaster.client_name';
        }
        switch ($this->data_type) {
            case BaseColumnSet::DATA_TYPE_URL:
                if ($this->column_name == 'map_url') {
                    return 'map_url:mapUrl';
                } else {
                    return $this->column_name . ':newWindowUrl';
                }
                break;
            case BaseColumnSet::DATA_TYPE_DATE:
                return $this->column_name . ':date';
                break;
            case BaseColumnSet::DATA_TYPE_TEXT:
                return $availableNewLine ? $this->column_name . ':jobView' : $this->column_name;
            default:
                return $this->column_name;
                break;
        }
    }

    /**
     * 求人原稿CSV一括登録用CSV入力規則の説明をArrayで返す（JobColumnSet部分のみ）
     * @return mixed
     */
    public function getDescription()
    {
        $description = '';

        $clientMasterIdLabel = Yii::$app->functionItemSet->client->items['client_no']->label;
        $clientChargePlanIdLabel = Yii::t('app', '{planLabel}No.', ['planLabel' => Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label]);
        if ($this->column_name == 'client_master_id') {
            $label = $clientMasterIdLabel;
        } elseif ($this->column_name == 'client_charge_plan_id') {
            $label = $clientChargePlanIdLabel;
        } else {
            $label = $this->label;
        }

        switch ($this->data_type) {
            case BaseColumnSet::DATA_TYPE_MAIL:
                $description = Yii::t('app', '{MAX_LENGTH}文字以内で、{LABEL}を入力します。一部記号 『( ) < > [ ] : ; ,』は使用できないので、ご注意ください。', ['LABEL' => $this->label, 'MAX_LENGTH' => $this->max_length]);
                break;
            case BaseColumnSet::DATA_TYPE_URL:
                $description = Yii::t('app', '{MAX_LENGTH}文字以内で、{LABEL}をURL形式で入力します。（例：' . yii\helpers\Url::to(['//'], true) . '）', ['LABEL' => $this->label, 'MAX_LENGTH' => $this->max_length]);
                break;
            case BaseColumnSet::DATA_TYPE_DATE:
                if ($this->column_name == 'disp_start_date') {
                    $label .= Yii::t('app', '(必須)');
                    $description = Yii::t('app', 'yyyy/MM/ddの形式で、{LABEL}を入力します。{LABEL}は必須項目です。', ['LABEL' => $this->label]);
                } elseif ($this->column_name == 'disp_end_date') {
                    $description = Yii::t('app', 'yyyy/MM/ddの形式で、{LABEL}を入力します。指定なしの場合は空白にしてください。', ['LABEL' => $this->label]);
                } else {
                    $description = Yii::t('app', 'yyyy/MM/ddの形式で、{LABEL}を入力します。', ['LABEL' => $this->label]);
                }
                break;
            case BaseColumnSet::DATA_TYPE_DROP_DOWN:
                if ($this->column_name == 'client_charge_plan_id') {
                    $label .= Yii::t('app', '(必須)');
                    //実際に入力する値は料金プランNoなので、ラベル名を変更している。
                    $description = $this->rulesText(Yii::t('app', '{clientChargePlanIdLabel}', ['clientChargePlanIdLabel' => $clientChargePlanIdLabel]), ['secure/csv-helper/job', 'helperType' => CsvHelperController::PLAN], '{LABEL}を入力します。{LABEL}は{LINK}を参照してください。{LABEL}は必須項目です。');
                }
                break;
            case BaseColumnSet::DATA_TYPE_NUMBER:
                switch ($this->column_name) {
                    case 'media_upload_id_1':
                    case 'media_upload_id_2':
                    case 'media_upload_id_3':
                    case 'media_upload_id_4':
                    case 'media_upload_id_5':
                        $description = $this->rulesText($this->label, ['secure/media-upload/list'], '{LABEL}の画像ファイルを入力します。{LABEL}の画像ファイルは{LINK}を参照してください。');
                        break;
                    case 'job_no':
                        $description = $this->rulesText($this->label, ['secure/job/list'], '{LABEL}を入力します。新規登録の場合は空白にしてください。{LABEL}は{LINK}を参照してください。');
                        break;
                    case 'client_master_id':
                        $label .= Yii::t('app', '(必須)');
                        //実際に入力する値は掲載企業Noなので、ラベル名を変更している。
                        $description = $this->rulesText($clientMasterIdLabel, ['secure/client/list'], '{LABEL}を入力します。{LABEL}は{LINK}を参照してください。{LABEL}は必須項目です。');
                        break;
                    default:
                        $description = Yii::t('app', '{MAX_LENGTH}以内の半角数字で、{LABEL}を入力します。', ['LABEL' => Html::encode($this->label), 'MAX_LENGTH' => $this->max_length]);
                        break;
                }
                break;
            case BaseColumnSet::DATA_TYPE_TEXT:
                if (in_array($this->column_name, ['tel_no', 'application_tel_1', 'application_tel_2'])) {
                    $description = Yii::t('app', '{MAX_LENGTH}文字以内の半角数字で、{LABEL}を入力します。', ['LABEL' => $this->label, 'MAX_LENGTH' => $this->max_length]);
                } else {
                    $description = Yii::t('app', '{MAX_LENGTH}文字以内で、{LABEL}を入力します。', ['LABEL' => $this->label, 'MAX_LENGTH' => $this->max_length]);
                }
                break;
            case BaseColumnSet::DATA_TYPE_RADIO:
                $subsetItem = '';
                if ($this->subsetItems) {
                    $subsetItem = Html::ul(ArrayHelper::getColumn($this->subsetItems, function (JobColumnSubset $v) {
                        return $v->subset_name;
                    }));
                }
                $description = Yii::t('app', '下記の選択肢から、1つを選んで入力します。{SUBSET_ITEM}', ['LABEL' => $this->label, 'SUBSET_ITEM' => $subsetItem]);
                break;
            case BaseColumnSet::DATA_TYPE_CHECK:
                $subsetItem = '';
                if ($this->subsetItems) {
                    $subsetItem = Html::ul(ArrayHelper::getColumn($this->subsetItems, function (JobColumnSubset $v) {
                        return $v->subset_name;
                    }));
                }
                $description = Yii::t('app', '下記の選択肢から、選んで入力します。複数入力される場合は","(カンマ)で区切って入力してください。{SUBSET_ITEM}', ['LABEL' => $this->label, 'SUBSET_ITEM' => $subsetItem]);
                break;
                break;
            default :
                break;
        }

        $isMust = ArrayHelper::getValue($this, 'is_must');
        if ($isMust === 1) {
            $label .= Yii::t('app', '(必須)');
            $description .= Yii::t('app', '{LABEL}は必須項目です。', ['LABEL' => $this->label]);
        }

        return $description ? [$label, $description] : [];
    }

    /**
     * リンク付きのCSV入力規則の文面に整形する返す。（求人情報項目管理用）
     * @param string $label
     * @param array $url
     * @param string $text
     * @return array
     */
    private function rulesText($label, $url, $text)
    {
        return JmUtils::rulesText($label, $url, $text, $this->column_name);
    }

    /**
     * 登録前処理
     * @param string $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // optionでdata_typeをチェックボックス、ラジオを選択時は項目説明文を空にする
            if (strpos($this->column_name, 'option') !== false &&
                ($this->data_type === self::DATA_TYPE_CHECK || $this->data_type === self::DATA_TYPE_RADIO)
            ) {
                $this->column_explain = '';
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 項目説明文をhtml encodeして改行反映して返す
     * @return string
     */
    public function getExplain()
    {
        if (JmUtils::isEmpty($this->column_explain)) {
            return '';
        }
        return Html::tag('span', null, ['class' => 'glyphicon glyphicon-info-sign', 'aria-hidden' => 'true']) . nl2br(Html::encode($this->column_explain));
    }

    public function isPicItem()
    {
        return strpos($this->column_name, 'media_upload_id_') !== false || strpos($this->column_name, 'job_pict_text_') !== false;
    }
}
