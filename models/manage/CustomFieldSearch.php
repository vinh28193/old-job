<?php

namespace app\models\manage;

use app\common\traits\FileDeleteTrait;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use app\common\SearchModelTrait;
use proseeds\helpers\StringUtil;

/**
 * CustomFieldSearch represents the model behind the search form about `app\models\manage\CustomField`.
 */
class CustomFieldSearch extends CustomField
{
    use SearchModelTrait;
    use FileDeleteTrait;

    const PAGE_SIZE = 20;
    const CSV_SIZE_LIMIT = 10000;

    /**
     * ルール設定
     * @return array ルールの構成
     */
    public function rules()
    {
        return array_merge([['url', 'string']], $this->getCsvSearchRules());
    }

    /**
     * 検索条件を組み立てたデータプロバイダーを返す
     * @param $params array リクエストされたクエリパラメータ
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::PAGE_SIZE,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->load($params) || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $this->searchWhere($query);

        return $dataProvider;
    }

    /**
     * カスタムフィールド一覧を検索して（CSV出力用の）結果を取得します。
     * @param array $params GETパラメータ
     * @return ActiveDataProvider
     */
    public function csvSearch($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::CSV_SIZE_LIMIT,
            ],
            //ソート
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        // gridの、json形式の値を使いやすい形に変換
        $params = $this->parse($params, 'CustomFieldSearch');

        $this->load($params);

        if (!$this->load($params) || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // 検索クエリセット
        $this->searchWhere($query);
        // 選択した値に関するwhere句を追加
        $this->selected($query);

        return $dataProvider;
    }

    /**
     * 削除対象データを検索してIDリストを返す
     * @param $params array gridからポストされたデータ
     * @return CustomField[]|bool 削除対象IDリスト
     */
    public function deleteSearch($params)
    {
        $query = CustomField::find()->select([self::tableName() . '.id', self::tableName() . '.pict']);
        // gridDataの、json形式の値を使いやすい形に変換
        $params = $this->parse($params, $this->formName());
        // load処理
        // 何もloadできなかった場合(初期状態)は権限情報のみloadして検索する
        $this->load($params);
        if (!$this->validate()) {
            return false;
        }
        // 検索クエリセット
        $this->searchWhere($query);
        $this->selected($query);
        return $query->all();
    }

    /**
     * 検索条件をセットする
     * @param ActiveQuery $query
     */
    private function searchWhere($query)
    {
        if (!StringUtil::is_empty($this->url)) {
            $query->andFilterWhere(['like', 'url', $this->url]);
        }
    }

    /**
     * isUsedPictの処理を挟むためにオーバーライド
     * @param CustomField[] $models
     */
    public static function deleteFiles($models)
    {
        foreach ($models as $model) {
            if (!static::isUsedPict($model->pict)) {
                $model->deleteFile();
            }
        }
    }
}
