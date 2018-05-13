<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/30
 * Time: 20:39
 */

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 各ColumnSet基底モデル
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $column_no
 * @property string $column_name
 * @property string $label
 * @property string $data_type
 * @property integer $max_length
 * @property integer $is_must
 * @property integer $is_in_list
 * @property integer $is_in_search
 * @property integer $valid_chk
 *
 * @property array defaultTypeArray
 * @property array $optionTypeArray
 * @property array $typeArray
 * @property ApplicationColumnSubset[]|JobColumnSubset[]|null $subsetItems
 */
class BaseColumnSet extends BaseModel
{
    /** データ種類 */
    const DATA_TYPE_TEXT = 'テキスト';
    const DATA_TYPE_NUMBER = '数字';
    const DATA_TYPE_MAIL = 'メールアドレス';
    const DATA_TYPE_CHECK = 'チェックボックス';
    const DATA_TYPE_RADIO = 'ラジオボタン';
    const DATA_TYPE_DATE = '日付';
    const DATA_TYPE_DROP_DOWN = 'プルダウン';
    const DATA_TYPE_URL = 'URL';
    /** シナリオ */
    const SCENARIO_TEL_NO = 'tel_no';
    const SCENARIO_OPTION = 'option';
    /** 電話番号が入るcolumn_nameのデフォルト値 */
    const TEL = ['tel_no'];
    /** FAX番号が入るcolumn_nameのデフォルト値 */
    const FAX = ['fax_no'];
    /** 継承先で宣言されていない時のエラー回避 */
    const STATIC_LABEL = [];
    const STATIC_IS_IN_LIST = [];
    const STATIC_IS_IN_SEARCH = [];
    const STATIC_MAX_LENGTH = [];
    const STATIC_COLUMN_EXPLAIN = [];
    const FULL_NAME = [];
    /** 状態 - 必須or任意 */
    const MUST = 1;
    const NOT_MUST = 0;
    /** 状態 - 表示or非表示(一覧表示) */
    const IN_LIST = 1;
    const NOT_IN_LIST = 0;
    /** 状態 - 表示or非表示(キーワード検索) */
    const IN_SEARCH = 1;
    const NOT_IN_SEARCH = 0;
    /** 状態 - 有効or無効 */
    const VALID = 1;
    const INVALID = 0;
    /** 状態 - 対象or対象外 */
    const FREEWORD_SEARCH_FLG = 1;
    const NOT_FREEWORD_SEARCH_FLG = 0;

    /** @var JobColumnSubset|ApplicationColumnSubset relationするclassの名前(実際は文字列) */
    public $relationClassName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        /** @aseColumnSet BaseColumnSetを継承したclassの名前(実際は文字列) */
        $className = $this->className();
        return [
            [['tenant_id', 'column_no', 'column_name', 'label', 'data_type', 'valid_chk'], 'required'],
            ['is_in_list', 'required', 'when' => function ($model, $attribute) {
                return !in_array($model->column_name, static::STATIC_IS_IN_SEARCH);
            }],
            [['tenant_id', 'column_no', 'max_length'], 'integer'],
            [['is_must', 'is_in_list', 'is_in_search', 'valid_chk'], 'boolean'],
            ['column_name', 'string', 'max' => 30],
            ['label', 'string', 'max' => 255],
            ['data_type', 'string', 'max' => 10],
            ['max_length', 'integer', 'min' => 1],
            // max_length共通
            ['max_length', 'required', 'when' => function ($model, $attribute) use ($className) {
                return !ArrayHelper::isIn($model->column_name, $className::STATIC_MAX_LENGTH)
                && ($model->data_type == self::DATA_TYPE_TEXT || $model->data_type == self::DATA_TYPE_NUMBER || $model->data_type == self::DATA_TYPE_MAIL || $model->data_type == self::DATA_TYPE_URL);
            }, 'whenClient' => $this->getWhenClientJs([self::DATA_TYPE_TEXT, self::DATA_TYPE_NUMBER, self::DATA_TYPE_MAIL, self::DATA_TYPE_URL, null])],
            // 電話番号,FAX番号の場合 （FAXはtelと同じ動き、かつ1箇所のみのためSCENARIO_TEL_NOに統合）
            ['max_length', 'integer', 'max' => 30, 'on' => self::SCENARIO_TEL_NO],
            // 対象カラムがメールアドレス属性の場合
            ['max_length', 'integer', 'max' => 254, 'when' => function ($model, $attribute) {
                return $model->data_type == self::DATA_TYPE_MAIL;
            }, 'whenClient' => $this->getWhenClientJs([self::DATA_TYPE_MAIL])],
            // 対象カラムが数字属性の場合
            ['max_length', 'string', 'max' => 50, 'tooLong' => Yii::t('app', '数値上限は50桁以内で入力してください。'), 'when' => function ($model, $attribute) {
                return $model->data_type == self::DATA_TYPE_NUMBER;
            }, 'whenClient' => $this->getWhenClientJs([self::DATA_TYPE_NUMBER])
            ],
        ];
    }

    /**
     * job以外で使われるmax_lengthのrule
     * @return array
     */
    protected function commonMaxLengthRule()
    {
        return [
            // max_length固定ではない、tel_no,fax_no以外のデフォルト項目の場合(max_length固定のものはそもそも入力させない)
            ['max_length', 'integer', 'max' => 255, 'except' => self::SCENARIO_OPTION],
            // テキストもしくはURL属性なoption項目
            ['max_length', 'integer', 'max' => 2000, 'on' => self::SCENARIO_OPTION, 'when' => function ($model, $attribute) {
                return $model->data_type == self::DATA_TYPE_TEXT || $model->data_type == self::DATA_TYPE_URL;
            }, 'whenClient' => $this->getWhenClientJs([self::DATA_TYPE_TEXT, self::DATA_TYPE_URL, null])],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'column_no' => Yii::t('app', 'メニューID'),
            'column_name' => Yii::t('app', '対象テーブルのカラム名'),
            'label' => Yii::t('app', '項目名'),
            'data_type' => Yii::t('app', '入力方法'),
            'max_length' => Yii::t('app', '文字数(数値)上限'),
            'is_must' => Yii::t('app', '入力条件'),
            'is_in_list' => Yii::t('app', '検索一覧表示'),
            'is_in_search' => Yii::t('app', '検索項目表示'),
            'valid_chk' => Yii::t('app', '公開状況'),
            // jobとapplicationのみで使われます
            'column_explain' => Yii::t('app', '項目説明文'),
        ];
    }

    /**
     * subsetリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getSubsetItems()
    {
        if ($className = $this->relationClassName) {
            /** @var BaseModel $className */
            return $this->hasMany($className::className(), ['column_name' => 'column_name', 'tenant_id' => 'tenant_id']);
        }
        return false;
    }

    /**
     * 関連モデルの保存
     * @param $post
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function saveRelationModel($post)
    {
        if (!$this->relationClassName) {
            return true;
        }
        $this->unlinkAll('subsetItems', true);
        /** @var JobColumnSubset $subsetModel */
        $subsetModel = Yii::createObject($this->relationClassName);
        if (($this->data_type == self::DATA_TYPE_CHECK || $this->data_type == self::DATA_TYPE_RADIO) && $params = ArrayHelper::getValue($post, $subsetModel->formName())) {
            foreach ($params as $param) {
                /** @var JobColumnSubset $model */
                $model = Yii::createObject($this->relationClassName);
                $model->subset_name = $param['subset_name'];
                $model->tenant_id = Yii::$app->tenant->id;
                $model->column_name = $this->column_name;
                if (!$model->validate()) {
                    return false;
                }
                $model->save();
            }
        }
        return true;
    }

    /**
     * オプション項目かどうか
     * @return bool
     */
    public function isOption()
    {
        return strpos($this->column_name, 'option') !== false;
    }

    /**
     * 基本のdataType
     * @return array
     */
    public function getDefaultTypeArray()
    {
        return [
            self::DATA_TYPE_TEXT => Yii::t('app', 'テキスト'),
            self::DATA_TYPE_NUMBER => Yii::t('app', '数字'),
            self::DATA_TYPE_MAIL => Yii::t('app', 'メールアドレス'),
            self::DATA_TYPE_URL => Yii::t('app', 'URL'),
        ];
    }

    /**
     * optionのdataType
     * @return array
     */
    public function getOptionTypeArray()
    {
        return [
            self::DATA_TYPE_CHECK => Yii::t('app', 'チェックボックス'),
            self::DATA_TYPE_RADIO => Yii::t('app', 'ラジオボタン'),
        ];
    }

    /**
     * dataTypeの配列を取得
     * @return array
     */
    public function getTypeArray()
    {
        return $this->isOption() && $this->relationClassName ? $this->defaultTypeArray + $this->optionTypeArray : $this->defaultTypeArray;
    }

    /**
     * 必須の配列を取得
     * @return array
     */
    public static function getIsMustArray()
    {
        return [self::MUST => Yii::t('app', '必須'), self::NOT_MUST => Yii::t('app', '任意')];
    }

    /**
     * list表示の配列を取得
     * @return array
     */
    public static function getIsInListArray()
    {
        return [self::IN_LIST => Yii::t('app', '表示'), self::NOT_IN_LIST => Yii::t('app', '非表示')];
    }

    /**
     * キーワード検索の配列を取得
     * @return array
     */
    public static function getIsInSearchArray()
    {
        return [self::IN_SEARCH => Yii::t('app', '表示'), self::NOT_IN_SEARCH => Yii::t('app', '非表示')];
    }

    /**
     * 有効無効の配列を取得
     * @return array
     */
    public static function getValidArray()
    {
        return [self::VALID => Yii::t('app', '有効'), self::INVALID => Yii::t('app', '無効')];
    }

    /**
     * 対象対象外の配列を取得
     * @return array
     */
    public static function getFreewordSearchFlgArray()
    {
        return [self::FREEWORD_SEARCH_FLG => Yii::t('app', '対象'), self::NOT_FREEWORD_SEARCH_FLG => Yii::t('app', '対象外')];
    }

    /**
     * @return array
     */
    public function getFormatTable()
    {
        return [
            'is_must' => self::getIsMustArray() + [null => Yii::t('app', '必須（固定）')],
            'max_length' => [null => Yii::t('app', '設定不要')],
            'is_in_list' => self::getIsInListArray() + [null => Yii::t('app', '非表示（固定）')],
            'is_in_search' => self::getIsInSearchArray() + [null => Yii::t('app', '表示（固定）')],
            'valid_chk' => self::getValidArray(),
        ];
    }

    /**
     * subsetを持ちうる項目かどうか
     * @return bool
     */
    public function getSubset()
    {
        return false;
    }

    /**
     * attributeを元にシナリオをセットする
     * todo 向上 afterFindで処理（acceptanceが整備されたらリファクタリング）
     */
    public function setScenarioByAttributes()
    {
        // 電話番号,FAX番号にシナリオ付与 （FAXはSCENARIO_TEL_NOに統合）
        if (ArrayHelper::isIn($this->column_name, static::TEL) || ArrayHelper::isIn($this->column_name, static::FAX)) {
            $this->scenario = self::SCENARIO_TEL_NO;
        } elseif (strpos($this->column_name, 'option') !== false) {
            $this->scenario = self::SCENARIO_OPTION;
        }
    }

    /**
     * validationを有効にしたいdataTypeを入力すると
     * whenClientに入れるjsを吐き出す
     * @param array $dataTypes
     * @return string
     */
    protected function getWhenClientJs($dataTypes)
    {
        $orConditions = [];
        $formName = $this->formName();
        foreach ($dataTypes as $dataType) {
            if ($dataType === null) {
                // data_typeのinput自体が無い時
                $orConditions[] = "$('[name=\"{$formName}[data_type]\"]')[0] == null;";
            } else {
                // data_typeのinputに特定のdata_typeが入力されている時
                $orConditions[] = "$('[name=\"{$formName}[data_type]\"]:checked').val() === \"{$dataType}\"";
            }
        }
        $conditions = implode(' || ', $orConditions);
        return "function(attribute, value) {
            return {$conditions};
        }";
    }

    /**
     * CR+LF改行が入った時の対処
     * todo 一旦この形で対応するが、この問題は他のヵ所でも起きているのでもっと基底で対応する必要あり
     * @return bool
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->hasAttribute('column_explain')) {
                $this->column_explain = str_replace("\r\n", "\n", $this->column_explain);
            }
            return true;
        }
        return false;
    }
}