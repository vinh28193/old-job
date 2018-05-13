<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use yii;
use yii\helpers\ArrayHelper;

/**
 * Distモデル
 * 市区町村
 *
 * @author Yukinori Nakamura
 *
 * @property integer $id
 * @property integer $pref_no
 * @property string  $dist_name
 * @property integer $dist_sub_cd
 * @property integer $dist_cd
 * 
 * @property Pref $pref
 * @property PrefDist $prefDist
 */
class Dist extends BaseModel
{
    /** 状態 - 有効 */
    const FLAG_VALID = 1;

    /** 状態 - 無効 */
    const FLAG_UNVALID = 0;

    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'dist';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['pref_no', 'dist_name', 'dist_cd'], 'required'],
            [['pref_no', 'dist_sub_cd', 'dist_cd'], 'integer'],
            [['dist_name'], 'string', 'max' => 255],
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
            'id'          => Yii::t('app', 'ID'),
            'pref_no'     => Yii::t('app', 'prefテーブルの都道府県コード'),
            'dist_name'   => Yii::t('app', '市区町村名'),
            'dist_sub_cd' => Yii::t('app', '市区町村サブコード'),
            'dist_cd'     => Yii::t('app', '市区町村コード'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPref()
    {
        // onConditionに関して。distはtenant_id、joinする際、on句にtenant_idがサイトから取得したもので絞っている。
        return $this->hasOne(Pref::className(), [
            'pref_no' => 'pref_no'
        ])->onCondition([Pref::tableName() . '.tenant_id' => Yii::$app->tenant->id]);
    }

    /**
     * 項目リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getPrefDist()
    {
        // onConditionに関して。distはtenant_id、joinする際、on句にtenant_idがサイトから取得したもので絞っている。
        return $this->hasMany(PrefDist::className(), [
            'dist_id' => 'dist_cd'
        ])->onCondition([PrefDist::tableName() . '.tenant_id' => Yii::$app->tenant->id]);
    }

    /**
     * 地域グループに紐付いていない市区町村
     * @param $params
     * @return self[]
     */
    public static function listFind($params)
    {
        $pref = Pref::findOne([
            'id' => ArrayHelper::getValue(
                ArrayHelper::getValue($params, 'PrefDistMaster'),'pref_id'
            )
        ]);
        $prefNo = !is_null($pref) ? $pref->pref_no : null;
        return self::find()->joinWith('prefDist')
            ->andFilterWhere([self::tableName() . '.pref_no' => $prefNo, 'pref_dist.id' => null])
            ->andWhere(['pref_dist.id' => null])
            ->all();
    }

    /**
     * 検索キーコードのcsvダウンロード用にattributeの配列を生成する
     * @return array
     */
    public function searchkeyCsvAttributes()
    {
        return [
            'dist_cd',
            'pref.pref_name',
            'dist_name',
        ];
    }

}
