<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;

/**
 * This is the model class for table "disp_type".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $disp_type_no
 * @property string $disp_type_name
 * @property integer $valid_chk
 */
class DispType extends BaseModel
{
    /** 状態 - 有効or無効 */
    const VALID = 1;
    const INVALID = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'disp_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'disp_type_no', 'disp_type_name'], 'required'],
            [['tenant_id', 'disp_type_no', 'valid_chk'], 'integer'],
            [['disp_type_name'], 'string', 'max' => 255]
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
            'disp_type_no' => Yii::t('app', '掲載タイプコード'),
            'disp_type_name' => Yii::t('app', '掲載タイプ名'),
            'valid_chk' => Yii::t('app', '状態'),
        ];
    }
}