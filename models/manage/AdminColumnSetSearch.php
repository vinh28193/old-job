<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/05/13
 * Time: 3:21
 */
namespace app\models\manage;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AdminColumnSetSearch represents the model behind the search form about `app\models\manage\AdminColumnSet`.
 */
class AdminColumnSetSearch extends AdminColumnSet
{
    public $searchText;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['searchText', 'safe'],
        ];
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AdminColumnSet::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 100],
            'sort' => [
                'defaultOrder' => ['column_no' => SORT_ASC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'label', $this->searchText])
            ->andWhere(['not', ['column_name' => '']]);

        return $dataProvider;
    }
}