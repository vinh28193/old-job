<?php

namespace app\models\manage;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_column_set".
 * @property integer $freeword_search_flg
 * @property array $SubsetNameList
 * @property string $columnNameWithFormat
 * @property ClientDisp $clientDisp
 */
class ClientColumnSet extends BaseColumnSet
{
    /** labelが固定なレコードのcolumn_name */
    const STATIC_LABEL = [
        'corp_master_id',
    ];
    /** data_typeが固定なレコードのcolumn_name */
    const STATIC_DATA_TYPE = [
        'client_no',
        'corp_master_id',
        'client_name',
        'client_name_kana',
        'address',
        'tanto_name',
        'tel_no',
        'client_business_outline',
        'client_corporate_url',
    ];
    /** max_lengthが固定なレコードのcolumn_name BaseColumnSetで使用しているので注意 */
    const STATIC_MAX_LENGTH = [
        'client_no',
        'corp_master_id',
        'client_corporate_url',
    ];
    /** is_mustが固定なレコードのcolumn_name */
    const STATIC_IS_MUST = [
        'client_no',
        'corp_master_id',
        'client_name',
    ];
    /** is_in_listが固定なレコードのcolumn_name（リスト除外固定） */
    const STATIC_IS_IN_LIST = [];
    // in_searchが固定なレコードのcolumn_name
    const STATIC_IS_IN_SEARCH = [];
    // valid_chkが固定なレコードのcolumn_name
    const STATIC_VALID_CHK = [
        'client_no',
        'corp_master_id',
        'client_name',
    ];
    /** freeword_search_flgが固定なレコードのcolumn_name */
    const STATIC_FREEWORD_SEARCH_FLG = [
//        'client_no',
        'corp_master_id',
    ];

    /**
     * シナリオ
     * SCENARIO_TEL_NO : 電話番号
     * SCENARIO_OPTION ：オプション項目とPR
     * SCENARIO_DEFAULT：上記以外
     */
    const SCENARIO_CLIENT_URL = 'client_corporate_url';

    /** client_dispに入ることの出来ないcolumn_name */
    const NOT_AVAILABLE_CLIENT_DISP_ITEMS = [
        'client_no',
        'corp_master_id',
    ];

    public static $dispTypeId;

    /** 状態 - 有効or無効 */
    const VALID = 1;
    const INVALID = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_column_set';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['freeword_search_flg', 'boolean'],
            ['max_length', 'integer', 'max' => 255, 'on' => self::SCENARIO_DEFAULT],
            // テキストもしくはURL属性なoption項目
            [
                'max_length',
                'integer',
                'max' => 2000,
                'on' => [self::SCENARIO_CLIENT_URL, BaseColumnSet::SCENARIO_OPTION],
                'when' => function ($model, $attribute) {
                    return $model->data_type == self::DATA_TYPE_TEXT || $model->data_type == self::DATA_TYPE_URL;
                },
                'whenClient' => $this->getWhenClientJs([self::DATA_TYPE_TEXT, self::DATA_TYPE_URL, null]),
            ],
        ]);
    }

    public function setScenarioByAttributes()
    {
        if (ArrayHelper::isIn($this->column_name, self::TEL)) {
            $this->scenario = self::SCENARIO_TEL_NO;
        } elseif ($this->column_name == 'client_corporate_url') {
            $this->scenario = self::SCENARIO_CLIENT_URL;
        } elseif (strpos($this->column_name, 'option') !== false) {
            $this->scenario = self::SCENARIO_OPTION;
        }
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
     * @return array
     */
    public function getFormatTable()
    {
        return array_merge(parent::getFormatTable(), [
            'freeword_search_flg' => self::getFreewordSearchFlgArray() + [null => Yii::t('app', '対象外')],
            'valid_chk' => self::getValidArray(),
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
     * 掲載企業ラベルを同期させる
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->column_name == 'client_name') {
            $adminColumnSet = AdminColumnSet::findOne(['column_name' => 'client_master_id']);
            $adminColumnSet->label = $this->label;
            $adminColumnSet->save();

            $jobColumnSet = JobColumnSet::findOne(['column_name' => 'client_master_id']);
            $jobColumnSet->label = $this->label;
            $jobColumnSet->save();

            $applicationColumnSet = ApplicationColumnSet::findOne(['column_name' => 'clientLabel']);
            $applicationColumnSet->label = $this->label;
            $applicationColumnSet->save();
        }
    }

    /**
     * format情報を添えたcolumn_nameを返す
     * @return string
     */
    public function getColumnNameWithFormat()
    {
        switch ($this->data_type) {
            case BaseColumnSet::DATA_TYPE_URL:
                return $this->column_name . ':newWindowUrl';
                break;
            case BaseColumnSet::DATA_TYPE_DATE:
                return $this->column_name . ':date';
                break;
            default:
                return $this->column_name;
                break;
        }
    }

    /**
     * dispTypeId別ClientDisp relation
     * @return ActiveQuery
     */
    public function getClientDisp()
    {
        return $this->hasOne(ClientDisp::className(), ['column_name' => 'column_name'])->onCondition(['disp_type_id' => self::$dispTypeId]);
    }

    /**
     * relation用のdispTypeIdをセットする
     * @param $dispTypeId
     */
    public static function setDispTypeId($dispTypeId)
    {
        self::$dispTypeId = $dispTypeId;
    }
}
