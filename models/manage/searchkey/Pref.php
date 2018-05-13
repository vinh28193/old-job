<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "pref".
 *
 * @property integer          $id
 * @property integer          $tenant_id
 * @property integer          $pref_no
 * @property string           $pref_name
 * @property integer          $area_id
 * @property integer          $sort
 *
 * @property Dist[]           $dist
 * @property Dist[]           $distLite
 * @property Area             $area
 * @property PrefDistMaster[] $prefDistMasters
 * @property PrefDistMaster[] $dispPrefDistMasters
 */
class Pref extends BaseModel
{
    /** 状態 - 有効 */
    const FLAG_VALID = 1;

    /** 状態 - 無効 */
    const FLAG_INVALID = 0;

    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'pref';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['pref_no', 'pref_name', 'sort'], 'required'],
            [['pref_no', 'area_id', 'sort'], 'integer'],
            [['pref_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * 要素のラベル名を設定。
     * function_item_setテーブルから名前を取得し、カラムごとに適用しています。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        // pref_no, area_idはDBコメントがわからないため、そのももカラム名を入れておきます。
        return [
            'id'        => Yii::t('app', 'ID'),
            'pref_name' => Yii::t('app', '都道府県名'),
            'sort'      => Yii::t('app', '表示順'),
            'pref_no'   => Yii::t('app', '都道府県コード'),
            'area_id'   => Yii::t('app', 'テーブルareaのカラムid'),
        ];
    }

    /**
     * 市区町村リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getDist()
    {
        return $this->hasMany(Dist::className(), ['pref_no' => 'pref_no']);
    }

    /**
     * 軽量化市区町村リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getDistLite()
    {
        return $this->hasMany(Dist::className(), ['pref_no' => 'pref_no'])->select([
            Dist::tableName() . '.pref_no',
            Dist::tableName() . '.dist_name',
            Dist::tableName() . '.id',
            Dist::tableName() . '.dist_cd',
        ]);
    }

    /**
     * 地域エリアリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getPrefDistMasters()
    {
        return $this->hasMany(PrefDistMaster::className(), ['pref_id' => 'id', 'tenant_id' => 'tenant_id']);
    }

    /**
     * エリアリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['id' => 'area_id']);
    }

    /**
     * 地域エリアリレーション（表示用）
     * @return \yii\db\ActiveQuery
     */
    public function getDispPrefDistMasters()
    {
        return $this->hasMany(PrefDistMaster::className(), ['pref_id' => 'id', 'tenant_id' => 'tenant_id'])
            ->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * 勤務地リストを取得する
     * @return array
     */
    public static function getDistList()
    {
        return self::find()->joinWith('dist')
            ->orderBy([
                self::tableName() . '.sort' => SORT_ASC,
                self::tableName() . '.id'   => SORT_ASC,
                Dist::tableName() . '.id'   => SORT_ASC,
            ])
            ->all();
    }

    /**
     * todo 第二引数の精査お願いいたします
     * @param bool $flag
     * @param bool $isId
     * @return array|yii\db\ActiveRecord[]
     */
    public static function getPrefId($flag = false, $isId = false)
    {
        $result = Pref::find()->all();
        if ($flag) {
            return ArrayHelper::map($result, ($isId ? 'id' : 'pref_no'), 'pref_name');
        } else {
            return $result;
        }
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query        = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                ],
            ],
        ]);
        $this->load($params);

        $query->andFilterWhere(['pref_no' => $this->pref_no]);

        return $dataProvider;
    }

    /**
     * 都道府県のdropDown用の配列を返す
     * @param bool $areaCheck true:有効なエリアのものだけ返す false:全部返す
     * @return array 取得した名前の配列を返す
     */
    public static function getPrefList($areaCheck = false)
    {
        $q = self::find()->select([
            self::tableName() . '.id',
            self::tableName() . '.pref_name'
        ])->orderBy([
            self::tableName() . '.id' => SORT_ASC
        ]);

        if ($areaCheck) {
            $q->joinWith('area')->where([Area::tableName() . '.valid_chk' => self::FLAG_VALID]);
        }

        return ArrayHelper::map($q->all(), 'id', 'pref_name');
    }

    /**
     * 指定エリア内prefのid、pref_noを取得する
     * @param $area Area 指定するエリア
     * @return array list($ids, $prefNos) IDの配列、Noの配列を併せて返す
     */
    public static function prefIdsPrefNos($area)
    {
        $prefs = self::find()->select([
            self::tableName() . '.id',
            self::tableName() . '.pref_no',
        ])->where([
            self::tableName() . '.area_id' => $area->id,
        ])->all();

        return array(
            ArrayHelper::getColumn($prefs, 'id'),
            ArrayHelper::getColumn($prefs, 'pref_no'),
        );
    }

    /**
     * 検索結果のある勤務地を取得する
     * @param Area $area 指定エリア
     * @param array $jobIds 対象とする仕事ID配列
     * @return array 都道府県、地域、市区町村の順にネストされた配列を返す
     */
    public static function prefArray($area, $jobIds)
    {
        $numArray = self::find()
            ->innerJoin('pref_dist_master', '`pref`.`id` = `pref_dist_master`.`pref_id`')
            ->innerJoin('pref_dist', '`pref_dist_master`.`id` = `pref_dist`.`pref_dist_master_id`')
            ->innerJoin('dist', '`pref_dist`.`dist_id` = `dist`.`dist_cd`')
            ->innerJoin('job_dist', '`dist`.`id` = `job_dist`.`dist_id`')
            ->select([
                'pref.pref_no',
                'pref_dist_master.pref_dist_master_no',
                'dist.dist_cd',
            ])->where([
                'pref.area_id' => $area->id,
                'pref_dist_master.valid_chk' => self::FLAG_VALID,
                'job_dist.job_master_id' => $jobIds,
            ])->distinct()->asArray()->all();

        $array = [];
        foreach ((array)$numArray as $numbers) {
            $array[$numbers['pref_no']][$numbers['pref_dist_master_no']][] = $numbers['dist_cd'];
        }

        return $array;
    }
}
