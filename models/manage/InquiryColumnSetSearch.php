<?php

namespace app\models\manage;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * InquiryColumnSetSearch represents the model behind the search form about `app\models\manage\InquiryColumnSet`.
 */
class InquiryColumnSetSearch extends InquiryColumnSet
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

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
        ;

        return $dataProvider;
    }
}
