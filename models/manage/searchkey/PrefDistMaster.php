<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use yii;
use yii\data\ActiveDataProvider;

/**
 * PrefDistMasterモデル
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $pref_id
 * @property string $pref_dist_name
 * @property integer $valid_chk
 * @property integer $pref_dist_master_no
 * @property integer $sort
 *
 * @property Pref $pref
 * @property Dist[] $districts
 * @property PrefDist[] $prefDist
 *
 * @author Yukinori Nakamura
 */
class PrefDistMaster extends BaseModel
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
        return 'pref_dist_master';
    }

    /**
     * 保存前処理
     * 新規登録時、検索キーNoをMAX+1して挿入しています。
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->pref_dist_master_no = self::find()->max('pref_dist_master_no') + 1;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['pref_id', 'pref_dist_name', 'valid_chk', 'sort'], 'required'],
            [['pref_id', 'valid_chk', 'sort', 'pref_dist_master_no'], 'integer'],
            [['pref_dist_name'], 'string', 'max' => 50],
            ['pref_dist_name', 'unique', 'filter' => ['pref_id' => $this->pref_id]],
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
            'pref_id' => Yii::t('app', 'テーブルprefのカラムid'),
            'pref_dist_name' => Yii::t('app', '地域名'),
            'sort' => Yii::t('app', '表示順'),
            'valid_chk' => Yii::t('app', '状態'),
            'pref_dist_master_no' => Yii::t('app', '検索URLに表示されるID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistricts()
    {
        return $this->hasMany(Dist::className(), ['dist_cd' => 'dist_id'])
            ->viaTable(PrefDist::tableName(), ['pref_dist_master_id' => 'id']);
    }

    /**
     * 項目リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getPrefDist()
    {
        return $this->hasMany(PrefDist::className(), ['pref_dist_master_id' => 'id']);
    }

    /**
     * 項目リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getPref()
    {
        return $this->hasOne(Pref::className(), ['id' => 'pref_id']);
    }

    /**
     * Creates data provider instance with search query applied
     * todo これいる？
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 1000,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);

        $this->load($params);

        $query->andFilterWhere(['pref_id' => $this->pref_id]);

        return $dataProvider;
    }

    public function findAllOrdered()
    {
        return self::find()
            ->joinWith('prefDist.distCd')
            ->andFilterWhere([self::tableName() . '.pref_id' => $this->pref_id])
            ->orderBy(['sort' => SORT_ASC, self::tableName() . '.id' => SORT_ASC])
            ->all();
    }

}
