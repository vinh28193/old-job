<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "occupation".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $occupation_name
 * @property integer $valid_chk
 * @property integer $sort
 * @property integer $occupation_no
 */
class Occupation extends BaseModel
{
    /** 状態 - 有効 */
    const FLAG_VALID = 1;

    /** 状態 - 無効 */
    const FLAG_UNVALID = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'occupation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['occupation_name', 'occupation_no'], 'required'],
            [['valid_chk', 'sort', 'occupation_no'], 'integer'],
            [['occupation_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'occupation_name' => Yii::$app->functionItemSet->application->items['occupation_id']->label,
            'valid_chk' => Yii::t('app', '状態'),
            'sort' => Yii::t('app', '表示順'),
            'occupation_no' => Yii::t('app', '属性コード'),
        ];
    }

    /**
     * 勤務地リストを取得する
     * @return array
     */
    public static function getOccupationList()
    {
        return self::find()
            ->where(['valid_chk' => self::FLAG_VALID,])
            ->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC,])
            ->all();
    }

}
