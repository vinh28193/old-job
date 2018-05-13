<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "wage_item".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $wage_category_id
 * @property integer $wage_item_no
 * @property integer $wage_item_name
 * @property integer $valid_chk
 * @property string $disp_price
 *
 * @property WageCategory $wageCategory
 */
class WageItem extends BaseModel
{
    /** @var int 状態 - 有効 */
    const FLAG_VALID = 1;

    /** @var int 状態 - 無効 */
    const FLAG_UNVALID = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wage_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wage_category_id', 'wage_item_name', 'disp_price', 'valid_chk'], 'required'],
            [['wage_category_id', 'wage_item_name', 'valid_chk'], 'integer'],
            [['disp_price'], 'string', 'max' => 20],
            [['wage_item_name'], 'integer', 'max' => 1000000000]
        ];
    }

    /**
     * @inheritdoc
     * 保存前処理
     * 新規登録時、求人原稿番号をMAX+1して挿入しています。
     * @param boolean $insert INSERT判別
     * @return boolean
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->wage_item_no = self::find()->max('wage_item_no') + 1;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'Tenant ID'),
            'wage_category_id' => Yii::t('app', 'カテゴリ名'),
            'wage_item_no' => Yii::t('app', '給与検索キーNo'),
            'disp_price' => Yii::t('app', '表示金額'),
            'wage_item_name' => Yii::t('app', '金額'),
            'valid_chk' => Yii::t('app', '公開状況'),
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
                    'wage_item_name' => SORT_ASC,
                ]
            ]
        ]);
        $this->load($params);

        return $dataProvider;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobWage()
    {
        return $this->hasMany(JobWage::className(), ['wage_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWageCategory()
    {
        return $this->hasOne(WageCategory::className(), ['id' => 'wage_category_id']);
    }

    /**
     * 検索キーコードのcsvダウンロードで使われる
     * attributeの配列を生成する
     * @return array
     */
    public function searchkeyCsvAttributes()
    {
        return [
            'wage_item_name',
            'wageCategory.wage_category_name',
            'disp_price',
        ];
    }

    /**
     * 検索結果のある給与を取得する
     * @param array|int $jobIds
     * @return array 給与体系、金額の順にネストされた配列を返す
     */
    public static function wageArray($jobIds)
    {
        $numArray = self::find()
            ->innerJoin('wage_category', '`wage_item`.`wage_category_id` = `wage_category`.`id`')
            ->innerJoin('job_wage', '`wage_item`.`id` = `job_wage`.`wage_item_id`')
            ->select([
                'wage_item.wage_item_no',
                'wage_category.wage_category_no',
            ])->where([
                'job_wage.job_master_id' => $jobIds,
                'wage_item.valid_chk' => WageItem::FLAG_VALID,
                'wage_category.valid_chk' => WageCategory::FLAG_VALID,
            ])->distinct()->asArray()->all();

        $array = [];
        foreach ((array)$numArray as $numbers) {
            $array[$numbers['wage_category_no']][] = $numbers['wage_item_no'];
        }

        return $array;
    }

}
