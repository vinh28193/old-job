<?php

namespace app\models\manage\searchkey;

use Yii;

/**
 * This is the model class for table "job_type".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $job_master_id
 * @property integer $job_type_small_id
 * relation getter
 * @property JobTypeSmall $jobTypeSmall
 */
class JobType extends BaseSearchKeyJunction
{
    public $itemForeignKey = 'job_type_small_id';
    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'job_type';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['job_master_id', 'job_type_small_id'], 'required'],
            [['job_master_id', 'job_type_small_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'id' => Yii::t('app', 'ID'),
            'job_master_id' => Yii::t('app', 'テーブルjob_masterのカラムid'),
            'job_type_small_id' => Yii::t('app', 'テーブルjob_type_smallのカラムid'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobTypeSmall()
    {
    	return $this->hasOne(JobTypeSmall::className(), ['id' => 'job_type_small_id']);
    }
}
