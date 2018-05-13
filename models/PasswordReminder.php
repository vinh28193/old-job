<?php

namespace app\models;

use proseeds\models\BaseModel;
use Yii;

/**
 * This is the model class for table "password_reminder".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $key_id
 * @property string $collation_key
 * @property integer $created_at
 * @property integer $key_flg
 */
class PasswordReminder extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'password_reminder';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key_id', 'collation_key', 'created_at'], 'required'],
            [['tenant_id', 'key_id', 'created_at', 'key_flg'], 'integer'],
            [['collation_key'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '主キー'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'key_id' => Yii::t('app', '会員・管理者ID'),
            'collation_key' => Yii::t('app', '照合キー'),
            'created_at' => Yii::t('app', '申請日時'),
            'key_flg' => Yii::t('app', 'アカウントフラグ'),
        ];
    }
}
