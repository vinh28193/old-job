<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/05/11
 * Time: 23:00
 */
// TODO:~ColumnSetSearch、~ColumnSubsetは共通化する
namespace app\models\manage;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * JobColumnSetSearch represents the model behind the search form about `app\models\manage\JobColumnSet`.
 */
class JobColumnSetSearch extends JobColumnSet
{
    const EXCEPT_PIC = 42;
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
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = JobColumnSet::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'column_no' => SORT_ASC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'label', $this->searchText])
            ->andWhere(['not', ['column_name' => '']])
            ->andWhere(['<=', 'column_no', self::EXCEPT_PIC])
        ;

        return $dataProvider;
    }
}
