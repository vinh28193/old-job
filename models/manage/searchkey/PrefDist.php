<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use yii;
/**
 * PrefDistモデル
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $pref_dist_master_id
 * @property integer $dist_id
 *
 * @property Dist $dist
 * @property PrefDistMaster[] $prefDistMaster
 *
 * @author Yukinori Nakamura
 */
class PrefDist extends BaseModel
{
    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'pref_dist';
    }
    
    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['tenant_id', 'pref_dist_master_id', 'dist_id'], 'required'],
            [['tenant_id', 'pref_dist_master_id', 'dist_id'], 'integer'],
        ];
    }
    
    /**
     * 要素のラベル名を設定。
     * function_item_setテーブルから名前を取得し、カラムごとに適用しています。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'pref_dist_master_id' => Yii::t('app', 'テーブルpref_dist_masterのカラムid'),
            'dist_id' => Yii::t('app', 'テーブルdistのカラムid'),
        ];
    }

    /**
     * 項目リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getDistCd()
    {
        return $this->hasMany(Dist::className(), ['dist_cd' => 'dist_id']);
    }
    /**
     * 項目リレーション
     * todo hasOneに変更
     * @return \yii\db\ActiveQuery
     */
    public function getPrefDistMaster()
    {
        return $this->hasMany(PrefDistMaster::className(), ['id' => 'pref_dist_master_id']);
    }
}
