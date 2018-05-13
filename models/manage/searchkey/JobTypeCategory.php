<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "job_type_category".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $job_type_category_cd
 * @property string $name
 * @property integer $sort
 * @property integer $valid_chk
 *
 * @property JobTypeBig[] $jobTypeBig
 */
class JobTypeCategory extends BaseModel
{
    /** @var int 状態 - 有効 */
    const FLAG_VALID = 1;

    /** @var int 状態 - 無効 */
    const FLAG_UNVALID = 0;

    /**
     * saveの前に行う処理
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && $this->isNewRecord) {
                $this->job_type_category_cd = (new Query())
                        ->select('max(job_type_category_cd)')
                        ->from(static::tableName())
                        ->scalar() + 1;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'job_type_category';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['job_type_category_cd', 'name', 'sort', 'valid_chk'], 'required'],
            [['job_type_category_cd', 'sort', 'valid_chk'], 'integer'],
            [['name'], 'string', 'max' => 50]
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
            'tenant_id' => Yii::t('app', 'Tenant ID'),
            'job_type_category_cd' => Yii::t('app', 'Job Type Category Cd'),
            'name' => Yii::t('app', '職種カテゴリ名'),
            'sort' => Yii::t('app', '表示順'),
            'valid_chk' => Yii::t('app', '公開状況'),
        ];
    }

    /**
     * todo これいる？
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAllOrdered()
    {
        return self::find()->orderBy(['sort' => SORT_ASC])->all();
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
                    'sort' => SORT_ASC,
                ]
            ]
        ]);
        $this->load($params);

        return $dataProvider;
    }

    /**
     * 職種リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobTypeBig()
    {
        return $this->hasMany(JobTypeBig::className(), ['job_type_category_id' => 'id'])->orderBy([JobTypeBig::tableName() . '.sort' => SORT_ASC]);
    }

    /**
     * 職種グループ名の配列
     * @return ArrayHelper
     */
    public static function getJobTypeCategoryList($validChk = null)
    {
        return ArrayHelper::map(self::find()
            ->andFilterWhere(['valid_chk' => $validChk])
            ->orderBy(['sort' => SORT_ASC])
            ->all(), 'id', 'name');
    }
}
