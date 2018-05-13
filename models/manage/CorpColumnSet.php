<?php

namespace app\models\manage;

use Yii;

/**
 * This is the model class for table "corp_column_set".
 * @property array $SubsetNameList
 */
class CorpColumnSet extends BaseColumnSet
{
    /** data_typeが固定なレコードのcolumn_name */
    const STATIC_DATA_TYPE = [
        'corp_no',
        'corp_name',
        'tel_no',
        'tanto_name',
    ];
    /** max_lengthが固定なレコードのcolumn_name BaseColumnSetで使用しているので注意 */
    const STATIC_MAX_LENGTH = [
        'corp_no',
    ];
    /** is_mustが固定なレコードのcolumn_name */
    const STATIC_IS_MUST = [
        'corp_no',
        'corp_name',
    ];
    /** is_in_listが固定なレコードのcolumn_name */
    const STATIC_IS_IN_LIST = [];
    /** in_searchが固定なレコードのcolumn_name */
    const STATIC_IS_IN_SEARCH = [];
    /** valid_chkが固定なレコードのcolumn_name */
    const STATIC_VALID_CHK = [
        'corp_no',
        'corp_name',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'corp_column_set';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), $this->commonMaxLengthRule());
    }

    /**
     * 代理店ラベルを同期させる
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->column_name == 'corp_name') {
            $adminColumnSet = AdminColumnSet::findOne(['column_name' => 'corp_master_id']);
            $adminColumnSet->label = $this->label;
            $adminColumnSet->save();
            
            $clientColumnSet = ClientColumnSet::findOne(['column_name' => 'corp_master_id']);
            $clientColumnSet->label = $this->label;
            $clientColumnSet->save();

            $jobColumnSet = JobColumnSet::findOne(['column_name' => 'corpLabel']);
            $jobColumnSet->label = $this->label;
            $jobColumnSet->save();

            $applicationColumnSet = ApplicationColumnSet::findOne(['column_name' => 'corpLabel']);
            $applicationColumnSet->label = $this->label;
            $applicationColumnSet->save();
        }
    }
}
