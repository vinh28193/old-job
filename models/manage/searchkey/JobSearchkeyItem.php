<?php

namespace app\models\manage\searchkey;

use app\models\manage\JobMaster;
use Yii;

/**
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $job_master_id
 * @property integer $searchkey_item_id
 *
 * @property SearchkeyItem $searchKeyItem
 * @property JobMaster $jobMaster
 * @property string $itemModelName
 */
class JobSearchkeyItem extends BaseSearchKeyJunction
{
    public $itemForeignKey = 'searchkey_item_id';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['job_master_id', 'searchkey_item_id'], 'required'],
            [['job_master_id', 'searchkey_item_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'job_master_id' => Yii::t('app', '外部キー'),
            'searchkey_item_id' => Yii::t('app', '外部キー'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchKeyItem()
    {
        return $this->hasOne($this->itemModelName, ['id' => 'searchkey_item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobMaster()
    {
        return $this->hasOne(JobMaster::className(), ['id' => 'job_master_id']);
    }

    /**
     * @return mixed
     */
    public function getItemModelName()
    {
        return str_replace('Job', '', static::className());
    }
}
