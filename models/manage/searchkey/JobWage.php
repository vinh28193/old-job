<?php

namespace app\models\manage\searchkey;

use Yii;

/**
 * This is the model class for table "job_wage".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $job_master_id
 * @property integer $wage_item_id
 * 
 * @property WageItem $wageItem
 */
class JobWage extends BaseSearchKeyJunction
{
    public $itemForeignKey = 'wage_item_id';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_wage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['job_master_id', 'wage_item_id'], 'required'],
            [['job_master_id', 'wage_item_id'], 'integer'],
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
            'job_master_id' => Yii::t('app', 'テーブルjob_masterのカラムid'),
            'wage_master_id' => Yii::t('app', 'テーブルwage_masterのカラムid'),
        ]);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWageItem()
    {
        return $this->hasOne(WageItem::className(), ['id' => 'wage_item_id']);
    }
}
