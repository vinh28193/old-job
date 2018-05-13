<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "job_type_small".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string  $job_type_small_name
 * @property integer $job_type_big_id
 * @property integer $valid_chk
 * @property integer $sort
 * @property integer $job_type_small_no
 *
 * @property JobTypeBig[] $jobBigTypes
 * @property JobTypeBig $jobTypeBig
 */
class JobTypeSmall extends BaseModel
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
                $this->job_type_small_no = (new Query())
                        ->select('max(job_type_small_no)')
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
        return 'job_type_small';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['job_type_small_name', 'job_type_big_id', 'valid_chk', 'sort', 'job_type_small_no'], 'required'],
            [['job_type_big_id', 'valid_chk', 'sort', 'job_type_small_no'], 'integer'],
            [['job_type_small_name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'job_type_small_name' => Yii::t('app', '職種小名'),
            'valid_chk' => Yii::t('app', '状態'),
            'sort' => Yii::t('app', '表示順'),
            'job_type_big_id' => Yii::t('app', '職種大ID'),
            'job_type_small_no' => Yii::t('app', '職種コード')
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
                'pageSize' => 10000,
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
     * 大職種リレーション
     * @return ActiveQuery
     */
    public function getJobBigTypes()
    {
        return $this->hasMany(JobTypeBig::className(), ['id' => 'job_type_big_id'])->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * 大職種リレーション（↑のrelationが変な気がするが、影響範囲が読めないのでもう一つ作る）
     * @return ActiveQuery
     */
    public function getJobTypeBig()
    {
        return $this->hasOne(JobTypeBig::className(), ['id' => 'job_type_big_id']);
    }

    /**
     * カテゴリリレーション
     * @return ActiveQuery
     */
    public function getJobTypeCategory()
    {
        return $this->hasOne(JobTypeCategory::className(), ['id' => 'job_type_category_id'])
            ->viaTable(JobTypeBig::tableName(), ['id' => 'job_type_big_id']);
    }

    /**
     * 検索キーコードのcsvダウンロード用
     * csvのファイル名を生成
     *
     * @return array
     */
    public function searchkeyCsvAttributes()
    {
        return [
            'job_type_small_no',
            'jobTypeCategory.name',
            'jobTypeBig.job_type_big_name',
            'job_type_small_name'
        ];
    }

}
