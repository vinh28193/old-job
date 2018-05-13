<?php

namespace app\models\manage;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * SearchkeyMasterSearch represents the model behind the search form about `app\models\manage\SearchkeyMaster`.
 * @property $isLabelForGrid
 */
class SearchkeyMasterSearch extends SearchkeyMaster
{
    public $hierarchyType;

    /** 階層 - 一階層or二階層 */
    const HIERARCHY_TYPE_ONE = 1;
    const HIERARCHY_TYPE_TWO = 2;
    const HIERARCHY_TYRE_OTHER = 3;

    //検索キー画面の検索における「その他」の検索キー
    const PREF_SEARCHKEY_NO = 1;
    const STATION_SEARCHKEY_NO = 2;
    const WAGE_CATEGORY_SEARCHKEY_NO = 5;

    //検索キー画面の検索における「その他」の検索キーの配列
    const OTHER_SERCHKEY_NOS =[
        self::PREF_SEARCHKEY_NO,
        self::STATION_SEARCHKEY_NO,
        self::WAGE_CATEGORY_SEARCHKEY_NO,
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['valid_chk','is_on_top'], 'boolean'],
            ['hierarchyType', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        $model = new SearchkeyMaster();
        return array_merge(parent::attributeLabels(), [
                'isLabelForGrid' => $model->getAttributeLabel('is_category_label'),
                'searchInputToolGrid' => $model->getAttributeLabel('search_input_tool'),
                'hierarchyType' => Yii::t('app', '階層タイプ'),
            ]);
    }

    /**
     * 全検索キーのdataProviderを返す
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find()->andWhere(['not', ['job_relation_table' => null]])->andWhere(['not', ['table_name' => 'job_type_category']]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
        ]);
        $dataProvider->sort->attributes['isLabelForGrid'] = [
            'asc' => ['is_category_label' => SORT_ASC, 'id' => SORT_ASC],
            'desc' => ['is_category_label' => SORT_DESC, 'id' => SORT_ASC],
        ];
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        // 検索クエリセット
        $this->searchWhere($query, $params);
        return $dataProvider;
    }

    /**
     * grid表示用のカテゴリ選択
     * @return string
     */
    public function getIsLabelForGrid()
    {
        if (in_array($this->table_name, SearchkeyMaster::STATIC_KEYS) || $this->principal_flg == 1) {
            return Yii::t('app', '選択する（固定）');
        } elseif ($this->is_category_label === null) {
            return '-';
        } else {
            $list = self::getIsCategoryLabel();
            return $list[$this->is_category_label];
        }
    }

    /**
     * grid表示用の入力方法
     * @return int
     */
    public function getSearchInputToolGrid()
    {
        if (in_array($this->table_name, SearchkeyMaster::STATIC_KEYS) || $this->principal_flg == 1) {
            return Yii::t('app', 'モーダル（固定）');
        } else {
            $list = self::getSearchInputTool();
            return $list[$this->search_input_tool];
        }
    }

    /**
     * @param $query ActiveQuery
     * @param $params
     */
    private function searchWhere($query, $params)
    {
        if ($this->hierarchyType == 1) {
            $query->andWhere(['second_hierarchy_cd' => null]);
        } elseif ($this->hierarchyType == 2) {
            $query->andWhere([
                'and',
                ['not', ['second_hierarchy_cd' => null]],
                ['not', ['searchkey_no' => self::OTHER_SERCHKEY_NOS]],
            ]);
        } elseif ($this->hierarchyType == 3) {
            $query->andWhere(['searchkey_no' => self::OTHER_SERCHKEY_NOS]);
        }
        $query->andFilterWhere(['valid_chk' => $this->valid_chk])
            ->andFilterWhere(['is_on_top' => $this->is_on_top]);
    }

    public static function getHierarchyType()
    {
        return [
            self::HIERARCHY_TYPE_ONE => Yii::t('app', '一階層キー'),
            self::HIERARCHY_TYPE_TWO => Yii::t('app', '二階層キー'),
            self::HIERARCHY_TYRE_OTHER => Yii::t('app', 'その他'),
        ];
    }
}
