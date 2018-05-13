<?php

namespace app\models\manage;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "application_column_set".
 * @property integer $is_sync
 * @property integer $sync_target
 * @property ApplicationColumnSubset[] $subsetItems
 * @property string column_explain
 * @property string columnExplainSei
 * @property string columnExplainMei
 */
class ApplicationColumnSet extends BaseColumnSet
{
    // todo 「閲覧状況（column_no = 17）」は今のところ使われておらず、なくなる可能性あり
    // is_syncとsync_targetは全て固定
    /** labelが固定なレコードのcolumn_name */
    const STATIC_LABEL = [
        'corpLabel',
        'clientLabel',
    ];

    /** data_typeが固定なレコードのcolumn_name */
    const STATIC_DATA_TYPE = [
        'application_no',
        'corpLabel',
        'clientLabel',
        'fullName',
        'fullNameKana',
        'sex',
        'birth_date',
        'pref_id',
        'address',
        'tel_no',
        'mail_address',
        'occupation_id',
        'self_pr',
        'carrier_type',
        'created_at',
        'application_status_id',
    ];
    /** max_lengthが固定なレコードのcolumn_name BaseColumnSetで使用しているので注意 */
    const STATIC_MAX_LENGTH = [
        'application_no',
        'corpLabel',
        'clientLabel',
        'sex',
        'birth_date',
        'pref_id',
        'mail_address',
        'occupation_id',
        'carrier_type',
        'created_at',
        'application_status_id',
    ];
    /** max_lengthが最大2000であるレコードのcolumn_name */
    const TEXT = ['self_pr',];
    /** is_mustが固定なレコードのcolumn_name */
    const STATIC_IS_MUST = [
        'application_no',
        'corpLabel',
        'clientLabel',
        'fullName',
        'mail_address', // 254固定（ColumnSetにてセット）
        'carrier_type',
        'created_at',
        'application_status_id',
    ];
    /** is_in_listが固定なレコードのcolumn_name（リスト除外固定） */
    const STATIC_IS_IN_LIST = [];
    /** in_searchが固定なレコードのcolumn_name */
    const STATIC_IS_IN_SEARCH = [
        'corpLabel',
        'clientLabel',
        'sex',
        'birth_date',
        'pref_id',
        'carrier_type',
        'created_at',
        'application_status_id',
    ];
    /** valid_chkが固定なレコードのcolumn_name */
    const STATIC_VALID_CHK = [
        'application_no',
        'corpLabel',
        'clientLabel',
        'fullName',
        'mail_address',
        'carrier_type',
        'created_at',
        'application_status_id',
    ];
    /** column_explainが固定なレコードのcolumn_name */
    const STATIC_COLUMN_EXPLAIN = [
        'application_no',
        'corpLabel',
        'clientLabel',
        'sex',
        'birth_date',
        'pref_id',
        'occupation_id',
        'carrier_type',
        'created_at',
        'application_status_id',
    ];
    /** 登録画面で使用しない項目 */
    const ITEMS_NOT_REGISTERED = [
        'corpLabel',
        'clientLabel',
        'application_no',
        'carrier_type',
        'created_at',
        'application_status_id',
    ];
    /** 詳細画面で使用しない項目 */
    const ITEMS_NOT_DETAIL = [
        'corpLabel',
        'clientLabel',
        'application_status_id',
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
    /** 項目説明文を２つ表示するcolumn_name **/
    const FULL_NAME = [
        'fullName',
        'fullNameKana',
    ];
    /** 応募画面の入力がtextareaではなくtextになっているレコードのcolumn_name */
    const STRING = ['tel_no', 'mail_address'];

    /**
     * シナリオ
     * SCENARIO_OPTION ：オプション項目とPR
     * SCENARIO_TEL    ：電話番号
     * SCENARIO_MAIL   ：メールアドレス
     * SCENARIO_DEFAULT：上記以外
     */
    const SCENARIO_MAIL = 'mail_address';

    /** column_explainの最大入力文字数 **/
    const MAX_LENGTH_FULLNAME = 9;
    const MAX_LENGTH_TELMAIL = 27;
    const MAX_LENGTH_OTHER = 180;

    private $_columnExplainSei;
    private $_columnExplainMei;

    public function init()
    {
        parent::init();
        $this->relationClassName = ApplicationColumnSubset::className();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'application_column_set';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), $this->commonMaxLengthRule(), [
            ['is_sync', 'boolean'],
            ['sync_target', 'number'],
            [
                ['columnExplainSei', 'columnExplainMei'],
                'string',
                'max' => self::MAX_LENGTH_FULLNAME,
                'when' => function (self $model) {
                    return $model->isFullName();
                },
            ],
            // 名前の項目説明文
            [['columnExplainSei', 'columnExplainMei'], 'trim'],
            // 電話番号とメールアドレスの項目説明文
            ['column_explain', 'string', 'max' => self::MAX_LENGTH_TELMAIL, 'on' => [self::SCENARIO_TEL_NO, self::SCENARIO_MAIL]],
            // その他項目の項目説明文
            ['column_explain', 'string', 'max' => self::MAX_LENGTH_OTHER, 'on' => self::SCENARIO_DEFAULT],
            // オプション項目の項目説明文
            [
                'column_explain',
                'string',
                'max' => self::MAX_LENGTH_OTHER,
                'on' => self::SCENARIO_OPTION,
                'when' => function ($model, $attribute) {
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
            'is_sync' => Yii::t('app', '連携フラグ'),
            'sync_target' => Yii::t('app', '連携対象ID'),
            'columnExplainSei' => Yii::t('app', '項目説明文(氏)'),
            'columnExplainMei' => Yii::t('app', '項目説明文(名)'),
        ]);
    }

    /**
     * subsetリスト取得
     * @return boolean|array
     */
    public function getSubsetList()
    {
        if (isset($this->subsetItems)) {
            $ret = [];
            foreach ($this->subsetItems as $subset) {
                $ret[$subset->id] = $subset->subset_name;
            }
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * subsetを持ちうる項目かどうか
     * @return bool|ApplicationColumnSubset
     */
    public function getSubset()
    {
        return $this->isOption() ? new ApplicationColumnSubset() : false;
    }

    /**
     * attributeを元にシナリオをセットする
     */
    public function setScenarioByAttributes()
    {
        if (ArrayHelper::isIn($this->column_name, self::TEL)) {
            $this->scenario = self::SCENARIO_TEL_NO;
        } elseif ($this->column_name == 'mail_address') {
            $this->scenario = self::SCENARIO_MAIL;
        } elseif (strpos($this->column_name, 'option') !== false || ArrayHelper::isIn($this->column_name, self::TEXT)) {
            // option項目とPRの時はオプションシナリオ（max_lengthとcolumn_explainの検証がoptionとprで一致するため）
            $this->scenario = self::SCENARIO_OPTION;
        }
    }

    /**
     * column_explain_sei 項目説明文（氏）設定
     * @param string $value 項目説明文（氏）
     */
    public function setColumnExplainSei($value)
    {
        //空白を除去して設定
        $this->_columnExplainSei = str_replace([' ', '　'], '', $value);
        // column_explainを再設定
        // ここで設定しないと入力値を空白にした場合、空白が反映されないため
        if ($this->isFullName()) {
            $name = $this->_columnExplainSei . ' ' . $this->_columnExplainMei;
            $this->column_explain = $name;
        }
    }

    /**
     * column_explain_sei 項目説明文（氏）取得
     * @return string
     */
    public function getColumnExplainSei()
    {
        if ($this->_columnExplainSei) {
            return $this->_columnExplainSei;
        }
        //column_explainを空白で分割したうちの前者を名として項目説明文（氏）として取得
        return $this->column_explain ? ArrayHelper::getValue(explode(' ', $this->column_explain), 0) : null;
    }

    /**
     * column_explain_mei 項目説明文（名）設定
     * @param string $value 項目説明文（名）
     */
    public function setColumnExplainMei($value)
    {
        //空白を除去して設定
        $this->_columnExplainMei = str_replace([' ', '　'], '', $value);
    }

    /**
     * column_explain_mei 項目説明文（名）取得
     * @return string
     */
    public function getColumnExplainMei()
    {
        if ($this->_columnExplainMei) {
            return $this->_columnExplainMei;
        }
        //column_explainを空白で分割したうちの後者を名として項目説明文（名）として取得
        return $this->column_explain ? ArrayHelper::getValue(explode(' ', $this->column_explain), 1) : null;
    }

    /**
     * 登録前処理
     * @param boolean $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            //氏名関連の情報をcolumn_explainに登録する際、分割処理を行う
            if ($this->isFullName()) {
                $name = $this->_columnExplainSei . ' ' . $this->_columnExplainMei;
                $this->column_explain = $name;
            }
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
     * 氏名関連かどうか
     * @return boolean
     */
    private function isFullName()
    {
        return ArrayHelper::isIn($this->column_name, self::FULL_NAME);
    }
}
