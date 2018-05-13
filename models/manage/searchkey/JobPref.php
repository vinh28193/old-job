<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use yii;
use app\models\manage\JobMaster;

/**
 * JobPrefモデル
 *
 * @author Yukinori Nakamura
 *
 * @property integer $tenant_id
 * @property integer $job_master_id
 * @property integer $pref_id
 *
 * @property Pref $pref
 */
class JobPref extends BaseSearchKeyJunction
{
    public $itemForeignKey = 'pref_id';

    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'job_pref';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['job_master_id', 'pref_id'], 'required'],
            [['job_master_id', 'pref_id'], 'integer'],
        ]);
    }

    /**
     * 要素のラベル名を設定。
     * function_item_setテーブルから名前を取得し、カラムごとに適用しています。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('app', 'ID'),
            'tenant_id'     => Yii::t('app', 'テナントID'),
            'job_master_id' => Yii::t('app', 'テーブルjob_masterのカラムid'),
            'pref_id'       => Yii::t('app', '都道府県コード'),
        ];
    }

    /**
     * prefとのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getPref()
    {
        return $this->hasOne(Pref::className(), ['id' => 'pref_id']);
    }

    /**
     * JobMasterとのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobMaster()
    {
        return $this->hasOne(JobMaster::className(), ['id' => 'job_master_id']);
    }

    /**
     * 紐付く都道府県の有効チェック
     *
     * @return bool
     */
    public function checkPrefArea()
    {
        return isset($this->pref->area);
    }


}
