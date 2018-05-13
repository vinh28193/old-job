<?php

namespace app\models\manage;

use app\common\Helper\JmUtils;
use app\common\traits\FileDeleteTrait;
use proseeds\models\BaseModel;
use Yii;
use app\common\SearchModelTrait;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * WidgetDataSearch represents the model behind the search form about `app\models\manage\WidgetData`.
 */
class WidgetDataSearch extends WidgetData
{
    use SearchModelTrait;
    use FileDeleteTrait;

    const SEARCH_ITEMS = ['title', 'description'];
    /**  */
    const DEFAULT_SELECT_VALUE = 'all';
    /** @var string キーワード検索項目 */
    public $searchItem;
    /** @var string キーワード検索テキスト */
    public $searchText;
    /** @var string 公開開始日 */
    public $startFrom;
    /** @var string 公開終了日 */
    public $startTo;
    /** @var int エリアid */
    public $areaId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['widget_id', 'areaId'], 'integer'],
            ['valid_chk', 'boolean'],
            [['searchText', 'searchItem'], 'string'],
            ['startFrom', 'date', 'timestampAttribute' => 'startFrom'],
            ['startTo', 'date', 'timestampAttribute' => 'startTo'],
        ], $this->getCsvSearchRules());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'searchItem' => 'Item Search',//not show
            'searchText' => 'Text Search',//not show
            'areaId' => Yii::t('app', 'エリア'),
        ]);
    }

    /**
     * loadFileInfoの影響を排除
     * todo モデル構成整理
     * @param $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        return BaseModel::load($data, $formName = null);
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = WidgetDataSearch::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->searchSort($dataProvider);
        // load処理
        if (!$this->load($params) || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $this->searchWhere($query, $params);
        return $dataProvider;
    }

    /**
     * @param ActiveQuery $query
     * @param $params
     */
    private function searchWhere($query, $params)
    {
        $query->joinWith('widget');
        if ($this->searchItem == self::DEFAULT_SELECT_VALUE) {
            $orWhere = ['or'];
            foreach (self::SEARCH_ITEMS as $attribute) {
                $orWhere[] = ['like', self::tableName() . '.' . $attribute, $this->searchText];
            }
            $query->andFilterWhere($orWhere);
        } else {
            $query->andFilterWhere(['like', self::tableName() . '.' . $this->searchItem, $this->searchText]);
        }

        $query->andFilterWhere([self::tableName() . '.widget_id' => $this->widget_id])
            ->andFilterWhere(['>=', self::tableName() . '.disp_start_date', $this->startFrom])
            ->andFilterWhere(['<=', self::tableName() . '.disp_start_date', $this->startTo])
            ->andFilterWhere(['=', self::tableName() . '.valid_chk', $this->valid_chk]);

        if (!JmUtils::isEmpty($this->areaId)) {
            $query->joinWith('widgetDataArea', false)->andWhere([WidgetDataArea::tableName() . '.area_id' => $this->areaId]);
        }
    }

    /**
     * ActiveDataProviderにsortパラメータを追加
     * @param $dataProvider \yii\data\ActiveDataProvider
     */
    private function searchSort($dataProvider)
    {
        $dataProvider->sort->attributes += [
            'widget.widget_no' => [
                'asc' => [Widget::tableName() . '.widget_no' => SORT_ASC],
                'desc' => [Widget::tableName() . '.widget_no' => SORT_DESC],
            ],
            'widget.widget_name' => [
                'asc' => [Widget::tableName() . '.widget_name' => SORT_ASC],
                'desc' => [Widget::tableName() . '.widget_name' => SORT_DESC],
            ],
            'widget.widget_type' => [
                'asc' => [Widget::tableName() . '.widget_type' => SORT_ASC],
                'desc' => [Widget::tableName() . '.widget_type' => SORT_DESC],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getSearchItemArray()
    {
        foreach (self::SEARCH_ITEMS as $attribute) {
            $labels[] = $this->getAttributeLabel($attribute);
        }
        return array_combine(self::SEARCH_ITEMS, $labels);
    }

    /**
     * @param $params
     * @return WidgetData[]|bool
     */
    public function deleteSearch($params)
    {
        $query = WidgetData::find()->select([static::tableName() . '.id', static::tableName() . '.pict']);
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
        return $query->all();
    }

    /**
     * IDでの指定されたものに関しては子レコードも削除する
     * @param string $condition
     * @param array $params
     * @return int
     */
    public static function deleteAll($condition = '', $params = [])
    {
        if (isset($condition['id'])) {
            WidgetDataArea::deleteAll(['widget_data_id' => $condition['id']], $params);
        }

        return parent::deleteAll($condition, $params);
    }
}
