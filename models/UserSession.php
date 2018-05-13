<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_session".
 *
 * @property string $id
 * @property integer $expire
 * @property string $data
 */
class UserSession extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['expire'], 'integer'],
            [['data'], 'string'],
            [['id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'SESSION ID'),
            'expire' => Yii::t('app', '有効期限'),
            'data' => Yii::t('app', 'データ'),
        ];
    }
}
