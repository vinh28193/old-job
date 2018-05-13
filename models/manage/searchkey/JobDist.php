<?php

namespace app\models\manage\searchkey;

use app\models\manage\JobMaster;
use Yii;

/**
 * This is the model class for table "job_dist".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $job_master_id
 * @property integer $dist_id
 *
 * @property array $prefIds
 * @property Dist $dist
 */
class JobDist extends BaseSearchKeyJunction
{
    /** @var string */
    public $itemForeignKey = 'dist_id';

    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'job_dist';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['tenant_id', 'job_master_id', 'dist_id', 'itemIds'], 'required'],
            [['tenant_id', 'job_master_id', 'dist_id'], 'integer'],
        ]);
    }

    /**
     * 要素のラベル名を設定。
     * function_item_setテーブルから名前を取得し、カラムごとに適用しています。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'job_master_id' => Yii::t('app', 'テーブルjob_masterのカラムid'),
            'dist_id' => Yii::t('app', 'テーブルdistのカラムid'),
        ]);
    }

    /**
     * 求人とのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobMaster()
    {
        return $this->hasOne(JobMaster::className(), ['id' => 'job_master_id']);
    }

    /**
     * 市区町村とのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getDist()
    {
        return $this->hasOne(Dist::className(), ['id' => 'dist_id']);
    }

    /**
     * $itemIdsに入っているidに紐づいたpref_idを重複を回避して取得する
     * @return array
     */
    public function getPrefIds()
    {
        return array_unique(
            Pref::find()->select(Pref::tableName() . '.id')
                ->innerJoinWith('dist')
                ->where([Dist::tableName() . '.id' => $this->itemIds])
                ->column()
        );
    }
}
