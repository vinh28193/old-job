<?php

namespace app\models\manage;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "admin_column_set".
 */
class AdminColumnSet extends BaseColumnSet
{
    /** labelが固定なレコードのcolumn_name */
    const STATIC_LABEL = [
        'corp_master_id',
        'client_master_id',
    ];
    /** data_typeが固定なレコードのcolumn_name */
    const STATIC_DATA_TYPE = [
        'admin_no',
        'corp_master_id',
        'client_master_id',
        'fullName',
        'login_id',
        'password',
        'tel_no',
        'exceptions',
        'mail_address',
    ];
    /** max_lengthが固定なレコードのcolumn_name BaseColumnSetで使用しているので注意 */
    const STATIC_MAX_LENGTH = [
        'admin_no',
        'corp_master_id',
        'client_master_id',
//        'fullName',
        'exceptions',
        'mail_address', // 254固定（ColumnSetにてセット）
    ];
    /** is_mustが固定なレコードのcolumn_name（exceptions以外必須固定） */
    const STATIC_IS_MUST = [
        'admin_no',
        'corp_master_id',
        'client_master_id',
        'fullName',
        'login_id',
        'password',
        'exceptions', // これのみ任意固定
        'mail_address',
    ];
    /** is_in_listが固定なレコードのcolumn_name（リスト除外固定） */
    const STATIC_IS_IN_LIST = [
        'password',
        'exceptions',
    ];
    /** is_in_searchが固定なレコードのcolumn_name（検索対象外固定） */
    const STATIC_IS_IN_SEARCH = [
        'corp_master_id',
        'client_master_id',
        'password',
        'exceptions',
    ];
    /** valid_chkが固定のレコードのcolumn_name（有効固定） */
    const STATIC_VALID_CHK = [
        'admin_no',
        'corp_master_id',
        'client_master_id',
        'fullName',
        'login_id',
        'password',
        'exceptions',
        'mail_address',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_column_set';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), $this->commonMaxLengthRule());
    }
}