<?php

namespace app\models\manage;

use Yii;

/**
 * This is the model class for table "name_master".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $name_id
 * @property string $change_name
 * @property string $default_name
 */
class NameMaster extends \proseeds\models\BaseModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'name_master';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'name_id', 'change_name', 'default_name'], 'required'],
            [['tenant_id', 'name_id'], 'integer'],
            [['change_name', 'default_name'], 'string', 'max' => 200]
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
            'name_id' => Yii::t('app', '名前ID'),
            'change_name' => Yii::t('app', '変更後名称'),
            'default_name' => Yii::t('app', '初期名称'),
        ];
    }

    /**
     * 変更名称の取得
     * 初期名称が見つからなければ、初期名称を返す。
     * @param string $defaultName 初期名称
     * @return string
     */
    public static function getChangeName($defaultName)
    {
        $nameMaster = self::findOne(['default_name' => $defaultName]);
        return isset($nameMaster) ? $nameMaster->change_name : $defaultName;
    }

}
