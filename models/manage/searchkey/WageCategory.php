<?php

namespace app\models\manage\searchkey;
use proseeds\models\BaseModel;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use Yii;

/**
 * This is the model class for table "wage_category".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $wage_category_name
 * @property integer $sort
 * @property integer $valid_chk
 * @property integer $wage_category_no
 *
 * @property WageItem[] $wageItem
 * @property WageItem[] $wageItemValid
 */
class WageCategory extends BaseModel
{
    /** @var int 状態 - 有効or無効 */
    const FLAG_VALID = 1;
    const FLAG_INVALID = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wage_category';
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAllOrdered()
    {
        return self::find()->orderBy(['sort' => SORT_ASC])->all();
    }

    /**
     * category名の配列
     * @param int|null $validChk
     * @return ArrayHelper 　取得した名前の配列を返す
     */
    public static function getWageCategoryList($validChk = null)
    {
        return ArrayHelper::map(self::find()
            ->select('id, wage_category_name')
            ->andFilterWhere(['valid_chk' => $validChk])
            ->orderBy(['sort' => SORT_ASC])->all(), 'id', 'wage_category_name');
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
                $this->wage_category_no = self::find()->max('wage_category_no') + 1;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wage_category_name', 'sort', 'valid_chk'], 'required'],
            [['sort', 'valid_chk'], 'integer'],
            [['wage_category_name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'Tenant ID'),
            'wage_category_name' => Yii::t('app', 'カテゴリ名'),
            'sort' => Yii::t('app', '表示順'),
            'valid_chk' => Yii::t('app', '公開状況'),
            'wage_category_no' => Yii::t('app', '検索URLに表示されるID'),
        ];
    }

    /**
     * Creates data provider instance with search query applied
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
     * itemとのリレーション（求職者画面用。有効なもののみ抽出）
     * @return \yii\db\ActiveQuery
     */
    public function getWageItemValid()
    {
        return $this->getWageItem()->where(['valid_chk' => self::FLAG_VALID]);
    }

    /**
     * itemとのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getWageItem()
    {
        return $this->hasMany(WageItem::className(), ['wage_category_id' => 'id'])->orderBy(['wage_item_name' => SORT_ASC]);
    }
}
