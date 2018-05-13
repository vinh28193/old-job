<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;

/**
 * This is the model class for table "tenant_message".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $content
 * @property integer $is_active
 * @property integer $created_at
 * @property integer $updated_at
 */
class MessageConvert extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message_convert';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id'], 'required'],
            [['tenant_id', 'is_active'], 'integer'],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '主キー'),
            'tenant_id' => Yii::t('app', 'テナントIDで'),
            'content' => Yii::t('app', '変換パターンJSON'),
            'is_active' => Yii::t('app', '有効フラグ'),
            'created_at' => Yii::t('app', '作成日時'),
            'updated_at' => Yii::t('app', '更新日時'),
        ];
    }
}
