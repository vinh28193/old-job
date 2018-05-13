<?php

namespace app\models\manage;

use Yii;

/**
 * This is the model class for table "manager_session".
 *
 * @property string $id
 * @property integer $admin_id
 * @property integer $expire
 * @property string $data
 */
class ManagerSession extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manager_session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['admin_id', 'expire'], 'integer'],
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
            'admin_id' => Yii::t('app', '管理者ID'),
            'expire' => Yii::t('app', '有効期限'),
            'data' => Yii::t('app', 'データ'),
        ];
    }
}
