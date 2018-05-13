<?php

namespace app\models\manage;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\common\SearchModelTrait;

/**
 * SearchCorp represents the model behind the search form about `app\models\manage\CorpMaster`.
 */
class CorpMasterSearch extends CorpMaster
{
    use SearchModelTrait;
    /** ページサイズ */
    const PAGE_SIZE_LIMIT = 10;
    /** 要件定義書にて定義 */
    const CSV_SIZE_LIMIT = 200000;
    /** @var string 検索項目・検索文字列 */
    public $searchItem;
    public $searchText;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['searchItem', 'searchText'], 'string'],
            [['corp_review_flg', 'valid_chk'], 'boolean'],
        ], $this->getCsvSearchRules());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CorpMaster::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::PAGE_SIZE_LIMIT,
            ],
            'sort' => [
                'defaultOrder' => [
                    'corp_no' => SORT_DESC,
                ],
            ],
        ]);
        // load処理
        if (!$this->load($params) || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }
        // 検索クエリセット
        $this->searchWhere($query, $params);
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function csvSearch($params)
    {
        $query = CorpMaster::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::CSV_SIZE_LIMIT,
            ],
            'sort' => [
                'defaultOrder' => [
                    'corp_no' => SORT_DESC,
                ],
            ],
        ]);
        // gridの、json形式の値を使いやすい形に変換
        $params = $this->parse($params, 'CorpMasterSearch');
        // load処理
        if (!$this->load($params) || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }
        // 検索クエリセット
        $this->searchWhere($query, $params);
        // 選択した値に関するwhere句を追加
        $this->selected($query);
        return $dataProvider;
    }

    /**
     * @param $params
     * @return array|bool
     */
    public function deleteSearch($params)
    {
        $query = self::find()->select(self::tableName() . '.id');
        // gridの、json形式の値を使いやすい形に変換
        $params = $this->parse($params, $this->formName());
        // load処理
        // 何もloadできなかった場合(初期状態)は権限情報のみloadして検索する
        $this->load($params);
        if (!$this->validate()) {
            return false;
        }
        // 検索クエリセット
        $this->searchWhere($query, $params);
        $this->selected($query);
        return $query->column();
    }

    /**
     * ActiveQueryに検索条件を追加
     * @param $query \yii\Db\ActiveQuery
     * @param $params array
     */
    private function searchWhere($query, $params)
    {
        if ($this->searchItem == 'all') {
            $orWhere = ['or'];
            foreach (Yii::$app->functionItemSet->corp->searchableByKeyWord as $attribute) {
                $orWhere[] = ['like', $attribute->column_name, $this->searchText];
            }
            $query->filterWhere($orWhere);
        } elseif (!$this->isEmpty($this->searchItem)) {
            $query->orFilterWhere(['like', $this->searchItem, $this->searchText]);
        }

        $query->andFilterWhere(['corp_review_flg' => $this->corp_review_flg]);
        $query->andFilterWhere(['valid_chk' => $this->valid_chk]);
    }
}
