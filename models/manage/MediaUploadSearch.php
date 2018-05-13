<?php

namespace app\models\manage;

use app\common\SearchModelTrait;
use app\common\traits\FileDeleteTrait;
use app\models\bases\BaseMediaUpload;
use yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use app\modules\manage\models\Manager;

/**
 * SearchMediaUpload represents the model behind the search form about `app\models\manage\MediaUpload`.
 */
class MediaUploadSearch extends BaseMediaUpload
{
    use SearchModelTrait;
    use FileDeleteTrait;

    /** @var string 管理者名（フルネーム） */
    public $adminMasterName;

    /** @var string 検索権限（権限名） */
    public $role;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['disp_file_name', 'adminMasterName', 'role', 'tag'], 'string'],
            [['role'], 'required'],
        ], $this->getCsvSearchRules());
    }

    /**
     * 権限入力が無い場合は自分の権限を入れる
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        if (!$this->role) {
            /** @var Manager $identity */
            $identity = Yii::$app->user->identity;
            $this->role = $identity->myRole;
        }
        return true;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MediaUpload::find()
            ->joinWith(['adminMaster'])
            ->joinWith(['clientMaster']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ],
            ],
        ]);

        $dataProvider->sort->attributes += [
            'adminName' => [
                'asc' => ['concat(' . AdminMaster::tableName() . '.name_sei, ' . AdminMaster::tableName() . '.name_mei)' => SORT_ASC],
                'desc' => ['concat(' . AdminMaster::tableName() . '.name_sei, ' . AdminMaster::tableName() . '.name_mei)' => SORT_DESC],
            ],
            'clientName' => [
                'asc' => [ClientMaster::tableName() . '.client_name' => SORT_ASC],
                'desc' => [ClientMaster::tableName() . '.client_name' => SORT_DESC],
            ],
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
     * 削除用メソッド
     * @param $params
     * @return MediaUpload[]|bool
     */
    public function deleteSearch($params)
    {
        $query = MediaUpload::find();
        // gridの、json形式の値を使いやすい形に変換
        $params = $this->parse($params, $this->formName());
        // load処理
        // 何もloadできなかった場合(初期状態)はfalseを返す
        if (!$this->load($params) && !$this->validate()) {
            return false;
        }
        // 検索クエリセット
        $this->searchWhere($query);
        $this->selected($query);
        return $query->all();
    }

    /**
     * 検索条件の追加
     * @param ActiveQuery $query
     */
    private function searchWhere($query)
    {
        $query->andFilterWhere(['like', 'disp_file_name', $this->disp_file_name])
            ->andFilterWhere(['like', 'concat(name_sei, name_mei)', preg_replace('/[\s　]/u', '', $this->adminMasterName)]);

        if ($this->tag === '0') {
            $query->andWhere([
                'or',
                [static::tableName() . '.tag' => null],
                [static::tableName() . '.tag' => ''],
            ]);
        } else {
            $query->andFilterWhere(['=', 'tag', $this->tag]);
        }

        switch ($this->role) {
            case Manager::OWNER_ADMIN :
                $query->andWhere([static::tableName() . '.client_master_id' => null]);
                break;
            case Manager::CLIENT_ADMIN :
                //掲載企業管理者が、運営元管理者の画像を変更削除できないようにするための処理
                /** @var Manager $identity */
                $identity = Yii::$app->user->identity;
                if ($identity->myRole == Manager::CLIENT_ADMIN) {
                    $query->andWhere([static::tableName() . '.client_master_id' => $identity->client_master_id]);
                } else {
                    $query->andWhere(['not', [static::tableName() . '.client_master_id' => null]]);
                }
                break;
            default :
                break;
        }
    }

    /**
     * 一覧画面の検索formのドロップダウンに表示するタグの配列を取得する
     * @return array
     */
    public static function tagDropDownSelections()
    {
        $tags = static::find()->addTagAuthQuery()->select(static::tableName() . '.tag')->distinct()->column();
        $array = [];
        foreach ($tags as $key => $value) {
            if ($value != null) {
                $array[$value] = $value;
            }
        }
        return static::orderTags($array);
    }

    /**
     * 権限毎の管理画像の合計容量を取得する
     * @return string
     */
    public static function getTotalFileSize()
    {
        $sum = MediaUpload::find()->addAuthQuery()->sum('file_size');
        if ($sum == null) {
            $sum = 0;
        }
        return $sum;
    }
}
