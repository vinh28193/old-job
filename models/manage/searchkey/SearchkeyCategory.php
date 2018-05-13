<?php

namespace app\models\manage\searchkey;

use app\models\manage\SearchkeyMaster;
use proseeds\models\BaseModel;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "searchkey_category".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $searchkey_category_name
 * @property integer $sort
 * @property integer $valid_chk
 * @property integer $select_type
 * @property integer $searchkey_category_no
 * @property integer $search_type
 *
 * @property SearchkeyItem $itemModelName
 * @property string $relationColumnName
 * @property SearchkeyItem[] $items
 */
class SearchkeyCategory extends BaseModel
{
    public $searchKeyCategoryNo = '';
    /** @var int 状態 - 有効 */
    const FLAG_VALID = 1;

    /** @var int 状態 - 無効 */
    const FLAG_UNVALID = 0;

    /**
     * @inheritdoc
     * 保存前処理
     * 新規登録時、検索URLに表示されるIDをMAX+1にして挿入しています。
     * @param boolean $insert INSERT判別
     * @return boolean
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->searchkey_category_no = self::find()->max('searchkey_category_no') + 1;
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
            [['searchkey_category_name', 'sort', 'valid_chk'], 'required'],
            [['sort', 'valid_chk', 'searchkey_category_no'], 'integer'],
            [['searchkey_category_name'], 'string', 'max' => 50]
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
            'searchkey_category_name' => Yii::t('app', 'カテゴリ名'),
            'sort' => Yii::t('app', '表示順'),
            'valid_chk' => Yii::t('app', '公開状況'),
            'searchkey_category_no' => Yii::t('app', '検索URLに表示されるID'),
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
     *  todo これいる？
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAllOrdered()
    {
        return self::find()->with('searchkeyItem')->orderBy(['sort' => SORT_ASC])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        $itemModel = $this->itemModelName;
        return $this->hasMany($itemModel::className(), ['searchkey_category_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getItemModelName()
    {
        return 'app\models\manage\searchkey\SearchkeyItem' . $this->searchKeyCategoryNo;
    }

    /**
     * category名の配列
     * @param null $validChk
     * @return array 取得した名前の配列を返す
     */
    public static function getSearchkeyCategoryList($validChk = null)
    {
        return ArrayHelper::map(static::find()
            ->select('id, searchkey_category_name')
            ->andFilterWhere(['valid_chk' => $validChk])
            ->orderBy('sort')
            ->all(), 'id', 'searchkey_category_name');
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
            'searchkey_item_no',
            'category.searchkey_category_name',
            'searchkey_item_name'
        ];
    }

    /**
     * 検索結果のあるサーチキーを取得する
     * @param array $jobIds
     * @param SearchkeyMaster $searchkeyMaster
     * @return array カテゴリ、アイテムの順にネストされた配列を返す
     */
    public static function categoryArray($jobIds, $searchkeyMaster)
    {
        /** @var SearchkeyCategory $categoryModelName */
        $categoryModelName = $searchkeyMaster->modelFullName;
        $categoryTableName = $categoryModelName::tableName();
        $itemTableName = str_replace('category', 'item', $categoryTableName);
        $jobRelationTableName = $searchkeyMaster->job_relation_table;

        $numArray = $categoryModelName::find()
            ->innerJoin($itemTableName, "`{$categoryTableName}`.`id` = `{$itemTableName}`.`searchkey_category_id`")
            ->innerJoin($jobRelationTableName,
                "`{$itemTableName}`.`id` = `{$jobRelationTableName}`.`searchkey_item_id`")
            ->select([
                $categoryTableName . '.searchkey_category_no',
                $itemTableName . '.searchkey_item_no',
            ])->where([
                $categoryTableName . '.valid_chk' => SearchkeyCategory::FLAG_VALID,
                $itemTableName . '.valid_chk' => SearchkeyItem::FLAG_VALID,
                $jobRelationTableName . '.job_master_id' => $jobIds,
            ])->distinct()->asArray()->all();

        $array = [];
        foreach ((array)$numArray as $numbers) {
            $array[$numbers['searchkey_category_no']][] = $numbers['searchkey_item_no'];
        }

        return $array;
    }
}
