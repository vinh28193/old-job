<?php

namespace app\models\manage;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\manage\Policy;
use proseeds\helpers\StringUtil;

/**
 * PolicySearch represents the model behind the search form about `app\models\manage\Policy`.
 */
class PolicySearch extends Policy
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [['policy', 'string']];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!StringUtil::is_empty($this->policy)) {
            $query->andFilterWhere(['like', 'policy', $this->policy]);
        }

        return $dataProvider;
    }
}
