<?php

namespace app\models\manage\searchkey;

use app\models\manage\SearchkeyMaster;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "searchkey_item".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $searchkey_category_id
 * @property string $searchkey_item_name
 * @property integer $searchkey_item_no
 * @property integer $sort
 * @property integer $valid_chk
 *
 * @property SearchkeyCategory $category
 */
class SearchkeyItem extends \proseeds\models\BaseModel
{
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
                $this->searchkey_item_no = self::find()->max('searchkey_item_no') + 1;
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
            [['searchkey_category_id', 'searchkey_item_name', 'sort', 'valid_chk'], 'required'],
            [['sort', 'valid_chk', 'searchkey_item_no'], 'integer'],
            [['searchkey_item_name'], 'string', 'max' => 50]
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
            'searchkey_category_id' => Yii::t('app', 'カテゴリ名'),
            'searchkey_item_name' => Yii::t('app', '項目名'),
            'sort' => Yii::t('app', '表示順'),
            'valid_chk' => Yii::t('app', '公開状況'),
            'searchkey_item_no' => Yii::t('app', '検索キーコード'),
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
        return self::find()->orderBy(['sort' => SORT_ASC])->all();
    }

    /**
     * カテゴリ取得relation
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        $className = str_replace('Item', 'Category', static::className());
        return $this->hasOne($className, ['id' => 'searchkey_category_id']);
    }

    /**
     * 検索キーコードのcsvダウンロード
     * で使われるattributeの配列を生成する
     *
     * @return array
     */
    public function searchkeyCsvAttributes()
    {
        return [
            'searchkey_item_no',
            'searchkey_item_name'
        ];
    }

    /**
     * 検索結果のあるサーチキーを取得する
     * @param array $jobIds
     * @param SearchkeyMaster $searchkeyMaster
     * @return array アイテムの配列を返す
     */
    public static function itemArray($jobIds, $searchkeyMaster)
    {
        /** @var SearchkeyItem $itemModelName */
        $itemModelName = $searchkeyMaster->modelFullName;
        $itemTableName = $itemModelName::tableName();
        $jobRelationTableName = $searchkeyMaster->job_relation_table;

        $numArray = $itemModelName::find()
            ->innerJoin($jobRelationTableName,
                "`{$itemTableName}`.`id` = `{$jobRelationTableName}`.`searchkey_item_id`")
            ->select([
                $itemTableName . '.searchkey_item_no',
            ])->where([
                $itemTableName . '.valid_chk' => SearchkeyItem::FLAG_VALID,
                $jobRelationTableName . '.job_master_id' => $jobIds,
            ])->distinct()->asArray()->all();

        $array = [];
        foreach ((array)$numArray as $numbers) {
            $array[] = $numbers['searchkey_item_no'];
        }

        return $array;
    }
}
