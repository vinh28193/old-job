<?php

namespace app\models\manage;

use yii;
use proseeds\models\BaseModel;
use app\modules\manage\components\validators\OverlapValidator;

/**
 * This is the model class for table "job_column_subset".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $column_name
 * @property string $subset_name
 */
class JobColumnSubset extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_column_subset';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'column_name', 'subset_name'], 'required'],
            [['tenant_id'], 'integer'],
            [['column_name'], 'string', 'max' => 30],
            [['subset_name'], 'string', 'max' => 255],
            [['subset_name'], OverlapValidator::className()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '主キーID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'column_name' => Yii::t('app', 'job_masterのカラム名'),
            'subset_name' => Yii::t('app', '選択肢項目名'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSetItem()
    {
        return $this->hasOne(CorpColumnSet::className(), ['column_name' => 'column_name', 'tenant_id' => 'tenant_id']);
    }
}
