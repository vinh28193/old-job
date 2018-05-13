<?php

namespace app\models\manage;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "inquiry_column_set".
 * @property string $placeholder
 * @property array|bool $subsetList
 * @property InquiryColumnSubset|null $subset
 */
class InquiryColumnSet extends BaseColumnSet
{
    /** labelが固定なレコードのcolumn_name */
    const STATIC_LABEL = [
        'postal_code',
        'address',
        'tel_no',
        'fax_no',
        'mail_address',
    ];
    /** data_typeが固定なレコードのcolumn_name */
    const STATIC_DATA_TYPE = [
        'company_name',
        'post_name',
        'tanto_name',
        'job_type',
        'postal_code',
        'address',
        'tel_no',
        'fax_no',
        'mail_address',
    ];
    /** max_lengthが固定なレコードのcolumn_name BaseColumnSetで使用しているので注意 */
    const STATIC_MAX_LENGTH = [
        'postal_code',
    ];
    /** is_mustが固定なレコードのcolumn_name（exceptions以外必須固定） */
    const STATIC_IS_MUST = [
        'mail_address',
    ];
    /** is_in_listが固定なレコードのcolumn_name（リスト除外固定） */
    const STATIC_IS_IN_LIST = [];
    /** is_in_searchが固定なレコードのcolumn_name（検索対象外固定） */
    const STATIC_IS_IN_SEARCH = [];
    /** valid_chkが固定のレコードのcolumn_name（有効固定） */
    const STATIC_VALID_CHK = [
        'mail_address',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inquiry_column_set';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->relationClassName = InquiryColumnSubset::className();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), $this->commonMaxLengthRule());
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $this->is_in_list = self::NOT_IN_LIST;
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'column_name' => Yii::t('app', 'inquiry_masterのカラム'),
        ]);
    }

    /**
     * placeholderを出力する
     * todo 項目説明文機能が掲載問い合わせに適用され次第column_explainと統合、削除
     * @return string
     */
    public function getPlaceholder()
    {
        if ($this->data_type == self::DATA_TYPE_NUMBER) {
            return Yii::t('app', '最大値:{max}', ['max' => $this->max_length]);
        }
        return Yii::t('app', '最大{max}文字', ['max' => $this->max_length]);
    }

    /**
     * 掲載問い合わせエラー対策
     * todo 項目説明文機能が掲載問い合わせに適用され次第placeholderと統合、削除
     * @return string
     */
    public function getColumn_explain()
    {
        if ($this->data_type == self::DATA_TYPE_NUMBER) {
            return Yii::t('app', '最大値:{max}', ['max' => $this->max_length]);
        }
        return Yii::t('app', '最大{max}文字', ['max' => $this->max_length]);
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
     * @return bool|InquiryColumnSubset
     */
    public function getSubset()
    {
        return $this->isOption() ? new InquiryColumnSubset() : false;
    }
}
