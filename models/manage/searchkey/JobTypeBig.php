<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "job_type_big".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $job_type_big_name
 * @property integer $valid_chk
 * @property integer $sort
 * @property integer $job_type_category_id
 * @property integer $job_type_big_no
 *
 * @property jobTypeSmall[] $jobTypeSmall
 * @property jobTypeCategory $jobTypeCategory
 */
class JobTypeBig extends BaseModel
{

    /** 状態 - 有効 */
    const FLAG_VALID = 1;

    /** 状態 - 無効 */
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
                $this->job_type_big_no = (new Query())
                        ->select('max(job_type_big_no)')
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
        return 'job_type_big';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['job_type_big_name', 'valid_chk', 'sort', 'job_type_big_no', 'job_type_category_id'], 'required'],
            [['valid_chk', 'sort', 'job_type_category_id', 'job_type_big_no'], 'integer'],
            [['job_type_big_name'], 'string', 'max' => 50],
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
            'job_type_big_name' => Yii::t('app', '職種大名'),
            'valid_chk' => Yii::t('app', '状態'),
            'sort' => Yii::t('app', '表示順'),
            'job_type_category_id' => Yii::t('app', '職種カテゴリID'),
            'job_type_big_no' => Yii::t('app', '職種大コード'),
        ];
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
     * todo これいる？
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAllOrdered()
    {
        return self::find()->orderBy(['sort' => SORT_ASC])->all();
    }

    /**
     * 小項目リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobTypeSmall()
    {
        return $this->hasMany(JobTypeSmall::className(), ['job_type_big_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * カテゴリリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobTypeCategory()
    {
        return $this->hasOne(JobTypeCategory::className(), ['id' => 'job_type_category_id']);
    }

    /**
     * 職種一覧を取得
     * @return array
     */
    public static function getJobTypes()
    {
        return self::find()->joinWith(['jobTypeSmall', 'jobTypeCategory'])
            ->where([
                JobTypeCategory::tableName() . '.valid_chk' => self::FLAG_VALID,
                self::tableName() . '.valid_chk' => self::FLAG_VALID,
                JobTypeSmall::tableName() . '.valid_chk' => self::FLAG_VALID,
            ])
            ->orderBy([
                JobTypeCategory::tableName() . '.sort' => SORT_ASC,
                JobTypeCategory::tableName() . '.job_type_category_cd' => SORT_ASC,
                self::tableName() . '.sort' => SORT_ASC,
                self::tableName() . '.job_type_big_no' => SORT_ASC,
                JobTypeSmall::tableName() . '.sort' => SORT_ASC,
                JobTypeSmall::tableName() . '.job_type_small_no' => SORT_ASC
            ])
            ->all();
    }

    /**
     * 職種名の配列
     * @param bool $flg true=有効なレコードのみ取得　false=全てのレコード取得
     * @return ArrayHelper　取得した名前の配列を返す
     */
    public static function getJobTypeBigList($validChk = null)
    {
        return ArrayHelper::map(self::find()->select([self::tableName() . '.id', 'job_type_big_name'])->joinWith(['jobTypeCategory'])
            ->filterWhere([JobTypeCategory::tableName() . '.valid_chk' => $validChk,])
            ->andFilterWhere([self::tableName() . '.valid_chk' => $validChk])
            ->orderBy([
                JobTypeCategory::tableName() . '.sort' => SORT_ASC,
                JobTypeCategory::tableName() . '.job_type_category_cd' => SORT_ASC,
                self::tableName() . '.sort' => SORT_ASC,
                self::tableName() . '.job_type_big_no' => SORT_ASC
            ])->all(), 'id', 'job_type_big_name');
    }
}
