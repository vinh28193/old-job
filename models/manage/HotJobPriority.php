<?php

namespace app\models\manage;

use Yii;
use proseeds\models\BaseModel;

/**
 * This is the model class for table "hot_job_priority".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $hot_job_id
 * @property string  $item
 * @property integer $disp_priority
 */
class HotJobPriority extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hot_job_priority';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'hot_job_id',
                    'disp_priority',
                    'tenant_id',
                    'item',
                ],
                'required'
            ],
            [['hot_job_id', 'disp_priority', 'tenant_id'], 'integer'],
            ['item', 'string', 'max' => 30]
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
            'hot_job_id' => Yii::t('app', '外部キー'),
            'item' => Yii::t('app', '優先項目'),
            'disp_priority' => Yii::t('app', '優先順位'),

            'updated_at' => Yii::t('app', '更新日'),
            'random' => Yii::t('app', 'ランダム'),
            'disp_type' => Yii::t('app', '掲載タイプ'),
            'disp_end_date' => Yii::t('app', '締切日近い'),
        ];
    }


    /**
     * hotJobへのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getHotJob()
    {
        return $this->hasMany(HotJob::className(), ['id' => 'hot_job_id']);
    }

}
