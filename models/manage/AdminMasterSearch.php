<?php

namespace app\models\manage;

use app\modules\manage\models\Manager;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app;
use app\common;
use app\common\SearchModelTrait;

/**
 * 管理者の拡張モデル
 */
class AdminMasterSearch extends AdminMaster
{
    use SearchModelTrait;

    const ALL_KEY_WORD = 'all';
    /** ページサイズ */
    const PAGE_SIZE = 10;
    /** 要件定義書にて定義。 */
    const CSV_SIZE_LIMIT = 10000;
    /** @var string キーワード検索対象・キーワード */
    public $searchItem;
    public $searchText;

    public function beforeValidate()
    {
        if (Model::beforeValidate()) {
            return true;
        }
        return false;
    }

    /**
     * ルールの定義
     * @return array ルール
     */
    public function rules()
    {
        return array_merge([
            ['searchText', 'safe'],
            ['searchItem', 'string'],
            ['valid_chk', 'boolean'],
            [['corp_master_id', 'client_master_id'], 'integer'],
            ['role', 'string'],
        ], $this->getCsvSearchRules());
    }

    /**
     * ラベル設定
     */
    public function attributeLabels()
    {
        //検索及び一覧で表示するラベルを設定
        return ArrayHelper::merge(parent::attributeLabels(), [
        ]);
    }

    /**
     * 管理者一覧を検索して結果を取得します。
     * @param array $params GETパラメータ
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find()->with(['corpMaster', 'clientMaster']);
        //検索して管理者情報のプロバイダ取得
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //1ページあたりのサイズ
            'pagination' => [
                'pageSize' => self::PAGE_SIZE,
            ],
            //ソート
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);
        $this->searchSort($dataProvider);
        // load処理
        if (!$this->load($params) || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }
        // 検索クエリセット
        $this->searchWhere($query, $params);
        // 表示初期化のための処理
        if ($this->client_master_id && !$this->corp_master_id) {
            $this->corp_master_id = ArrayHelper::getValue(ClientMaster::find()->select('corp_master_id')->where(['id' => $this->client_master_id])->one(), 'corp_master_id');
        }

        return $dataProvider;
    }

    /**
     * 管理者一覧を検索して（CSV出力用の）結果を取得します。
     * @param array $params GETパラメータ
     * @return ActiveDataProvider
     */
    public function csvSearch($params)
    {
        $query = self::find()->with(['corpMaster', 'clientMaster']);
        //検索して管理者情報のプロバイダ取得
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
        $this->searchSort($dataProvider);
        // gridの、json形式の値を使いやすい形に変換
        $params = $this->parse($params, 'AdminMasterSearch');
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
        // 何もloadできなかった場合(初期状態)はfalseを返す
        if (!$this->load($params) && !$this->validate()) {
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
        // queryセット
        // キーワード検索
        if ($this->searchItem == self::ALL_KEY_WORD) {
            $orWhere = ['or'];
            foreach (Yii::$app->functionItemSet->admin->searchableByKeyWord as $attribute) {
                $orWhere[] = ['like', self::tableName() . '.' . $attribute->column_name, $this->searchText];
            }
            $query->filterWhere($orWhere)
                ->orFilterWhere(['like', 'concat(name_sei, name_mei)', preg_replace('/[\s　]/u', '', $this->searchText)]);
        } elseif (!$this->isEmpty($this->searchItem)) {
            if ($this->searchItem == 'fullName') {
                $query->orFilterWhere(['like', 'concat(name_sei, name_mei)', preg_replace('/[\s　]/u', '', $this->searchText)]);
            } else {
                $query->orFilterWhere(['like', $this->searchItem, $this->searchText]);
            }
        }
        // join分岐（検索とソートのパラメータを見て必要十分なjoinをする）
        if (strpos(ArrayHelper::getValue($params, 'sort'), 'corp_master_id') !== false) {
            $query->joinWith('corpMaster');
        }
        if (strpos(ArrayHelper::getValue($params, 'sort'), 'client_master_id') !== false) {
            $query->joinWith('clientMaster');
        }
        // 代理店・掲載企業・種別検索
        $query->andFilterWhere([self::tableName() . '.corp_master_id' => $this->corp_master_id]);
        $query->andFilterWhere([self::tableName() . '.client_master_id' => $this->client_master_id]);

        if (!empty($this->role)) {
            switch ($this->role) {
                case Manager::OWNER_ADMIN:
                    $query->andWhere([self::tableName() . '.corp_master_id' => null])
                        ->andWhere([self::tableName() . '.client_master_id' => null]);
                    break;
                case Manager::CORP_ADMIN:
                    $query->andWhere(['not', [self::tableName() . '.corp_master_id' => null]])
                        ->andWhere([self::tableName() . '.client_master_id' => null]);
                    break;
                case Manager::CLIENT_ADMIN:
                    $query->andWhere(['not', [self::tableName() . '.corp_master_id' => null]])
                        ->andWhere(['not', [self::tableName() . '.client_master_id' => null]]);
                    break;
            }
        }
        // 状態
        $query->andFilterWhere(['=', self::tableName() . '.valid_chk', $this->valid_chk]);
    }

    /**
     * ActiveDataProviderにsortパラメータを追加
     * @param $dataProvider \yii\data\ActiveDataProvider
     */
    private function searchSort($dataProvider)
    {
        $dataProvider->sort->attributes += [
            'client_master_id' => [
                'asc' => [ClientMaster::tableName() . '.client_name' => SORT_ASC],
                'desc' => [ClientMaster::tableName() . '.client_name' => SORT_DESC],
            ],
            'corp_master_id' => [
                'asc' => [CorpMaster::tableName() . '.corp_name' => SORT_ASC],
                'desc' => [CorpMaster::tableName() . '.corp_name' => SORT_DESC],
            ],
            'fullName' => [
                'asc' => ['concat(name_sei, name_mei)' => SORT_ASC],
                'desc' => ['concat(name_sei, name_mei)' => SORT_DESC],
            ]
        ];
    }

    /**
     * attributeからlistItemのvalue値を取得する
     * todo コントローラーに書くべきか？
     * @param string $attr attribute
     * @return string value値
     */
    public static function getColumnName($attr)
    {
        switch ($attr) {
            case 'corp_master_id' :
                return 'corpMaster.corp_name';
                break;
            case 'client_master_id' :
                return 'clientMaster.client_name';
                break;
            default :
                return $attr;
                break;
        }
    }
}
