<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "application_status".
 */
class ApplicationStatus extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'application_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_status_no', 'application_status', 'valid_chk'], 'required'],
            [['application_status_no', 'valid_chk'], 'integer'],
            [['application_status'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'application_status_no' => Yii::t('app', '状況コード'),
            'application_status' => Yii::t('app', '状況'),
            'valid_chk' => Yii::t('app', '状態'),
        ];
    }

    /**
     * ドロップダウン用リストを取得
     * 初期値ラベルにnullやfalseや空文字を入れると初期選択が無くなる
     * @param string $defaultLabel
     * @return array
     */
    public static function getDropDownList($defaultLabel = null)
    {
        $array = ArrayHelper::map(self::find()->select(['id', 'application_status'])->where(['valid_chk' => 1])->all(), 'id', 'application_status');
        if($defaultLabel){
            return ['' => $defaultLabel] + $array;
        }
        return $array;
    }
}
