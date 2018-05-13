<?php

namespace app\models\manage;

use Yii;

/**
 * This is the model class for table "social_button".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $option_social_button_no
 * @property string $social_name
 * @property string $social_script
 * @property string $social_meta
 * @property integer $valid_chk
 */
class SocialButton extends \proseeds\models\BaseModel
{
    /**
     * 状態 - 無効
     */
    const UNVALID = 0;
    
    /**
     * 状態 - 有効
     */
    const VALID = 1;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'social_button';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'option_social_button_no', 'social_name', 'social_script', 'social_meta'], 'required'],
            [['tenant_id', 'option_social_button_no', 'valid_chk'], 'integer'],
            [['social_script', 'social_meta'], 'string'],
            [['social_name'], 'string', 'max' => 255]
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
            'option_social_button_no' => Yii::t('app', 'ソーシャルボタンナンバー'),
            'social_name' => Yii::t('app', 'ソーシャル名'),
            'social_script' => Yii::t('app', 'スクリプト'),
            'social_meta' => Yii::t('app', 'メタタグ'),
            'valid_chk' => Yii::t('app', '状態'),
        ];
    }
}
