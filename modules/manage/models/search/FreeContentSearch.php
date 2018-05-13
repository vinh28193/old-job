<?php

namespace app\modules\manage\models\search;

use app\common\interfaces\DeleteInterface;
use app\common\SearchModelTrait;
use app\models\FreeContentElement;
use app\modules\manage\models\forms\FreeContentElementForm;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use app\models\FreeContent;

/**
 * FreeContentSearch represents the model behind the search form about `app\models\FreeContent`.
 */
class FreeContentSearch extends FreeContent implements DeleteInterface
{
    use SearchModelTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['valid_chk'], 'boolean'],
        ], $this->getCsvSearchRules());
    }

    /**
     * 検索条件からDataProviderを返す
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->attributes['url'] = [
            'asc' => [static::tableName() . '.url_directory' => SORT_ASC],
            'desc' => [static::tableName() . '.url_directory' => SORT_DESC],
        ];

        // load処理
        if (!$this->load($params) || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }
        // 検索クエリセット
        $this->searchWhere($query);
        return $dataProvider;
    }

    /**
     * 削除するidを返す
     * @param $params
     * @return array|bool
     */
    public function deleteSearch($params)
    {
        $query = static::find()->select(static::tableName() . '.id');
        // gridの、json形式の値を使いやすい形に変換
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
        return $query->column();
    }

    /**
     * 削除時に関連エレメントも削除する
     * @param array $ids
     * @param array $params
     * @return int
     * @throws Exception
     */
    public static function deleteAllData(array $ids, $params = []):int
    {
        // 消されるファイル名を取得
        $deletedFileNames = FreeContentElement::find()->where(['free_content_id' => $ids])->imageFileNames();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // relationも一緒に削除
            if (FreeContentElement::deleteAll(['free_content_id' => $ids]) === 0) {
                throw new Exception();
            }
            $count = static::deleteAll(['id' => $ids], $params);

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $count = 0;
        }
        // 使われていないファイルを削除
        FreeContentElementForm::deleteUnusedFilesByName($deletedFileNames);

        return $count;
    }

    /**
     * ActiveQueryに検索条件を追加
     * @param $query \yii\Db\ActiveQuery
     */
    private function searchWhere($query)
    {
        $query->andFilterWhere(['valid_chk' => $this->valid_chk]);
    }
}
