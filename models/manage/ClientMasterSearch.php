<?php

namespace app\models\manage;

use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Yii;
use app\common\SearchModelTrait;

class ClientMasterSearch extends ClientMaster
{
    use SearchModelTrait;
    /** ページサイズ */
    const PAGE_SIZE = 10;
    /** 要件定義書にて定義。 */
    const CSV_SIZE_LIMIT = 10000;
    /** @var string キーワード検索対象・キーワード */
    public $searchItem;
    public $searchText;
    /** @var int 課金タイプ */
    public $clientChargeType;
    /** @var int 申し込みプランid */
    public $clientChargePlanId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge([
            [['clientChargePlanId', 'clientChargeType'], 'integer'],
            [['searchItem', 'searchText'], 'string'],
            ['valid_chk', 'boolean'],
        ], $this->getCsvSearchRules());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'clientChargePlanId' => Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label,
            'clientChargeType' => Yii::t('app', '課金タイプ')
        ]);
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // メイン検索クエリ
        $query = self::find()->with(['corpMaster', 'clientCharges.clientChargePlan']);
        // プロバイダ作成
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //1ページあたりのサイズ
            'pagination' => [
                'pageSize' => self::PAGE_SIZE,
            ],
            //ソート
            'sort' => [
                'defaultOrder' => [
                    'client_no' => SORT_DESC,
                ],
            ],
        ]);
        //代理店名用のソート設定。
        $this->searchSort($dataProvider);
        // load処理
        // loadAuthParamはbeforeValidate内に含まれている
        // 何もloadできなかった場合(初期状態)は何も表示させない
        if (!$this->load($params) || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }
        // 検索クエリセット
        $this->searchWhere($query, $params);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function csvSearch($params)
    {
        $query = self::find()->with(['corpMaster', 'clientCharges.clientChargePlan']);

        //検索して管理者情報のプロバイダ取得
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::CSV_SIZE_LIMIT,
            ],
            //ソート
            'sort' => [
                'defaultOrder' => [
                    'client_no' => SORT_DESC,
                ],
            ],
        ]);
        //代理店名用のソート設定。
        $this->searchSort($dataProvider);
        // gridの、json形式の値を使いやすい形に変換
        $params = $this->parse($params, $this->formName());
        // load処理
        // loadAuthParamはbeforeValidate内に含まれている
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
        // loadAuthParamはbeforeValidate内に含まれている
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
     * @param $params
     * @return ActiveDataProvider
     */
    public function csvPlanSearch($params)
    {
        $dataProvider = $this->csvSearch($params);
        $query = $dataProvider->query;

        /** @var ActiveQuery $query */
        $query->select('*')->joinWith('clientCharge.clientChargePlan');
        $query->groupBy = [];
        unset($query->with[1]);

        return $dataProvider;
    }

    /**
     * カラム名を取得します。
     * リレーション関係のデータは対応するリレーション先のカラム名を返します。
     * @param string $attr item_column名
     * @return string value値
     */
    public static function getColumnName($attr)
    {
        switch ($attr) {
            case 'corp_master_id':
                return 'corpMaster.corp_name';
                break;
            default :
                return $attr;
                break;
        }
    }

    /**
     * ActiveDataProviderにsortパラメータを追加
     * @param $dataProvider \yii\data\ActiveDataProvider
     */
    private function searchSort($dataProvider)
    {
        $dataProvider->sort->attributes += ['corp_master_id' => [
            'asc' => [CorpMaster::tableName() . '.corp_name' => SORT_ASC],
            'desc' => [CorpMaster::tableName() . '.corp_name' => SORT_DESC],
        ]];
    }

    /**
     * ActiveQueryに検索条件を追加
     * @param $query \yii\Db\ActiveQuery
     * @param $params array
     */
    private function searchWhere($query, $params)
    {
        // キーワード検索
        if ($this->searchItem == 'all') {
            $orWhere = ['or'];
            /** @var ClientColumnSet $clientColumnSet */
            foreach (Yii::$app->functionItemSet->client->searchableByKeyWord as $clientColumnSet) {
                if ($clientColumnSet->column_name == 'corp_master_id') {
                    $orWhere[] = ['like', CorpMaster::tableName() . '.corp_name', $this->searchText];
                } else {
                    $orWhere[] = ['like', self::tableName() . '.' . $clientColumnSet->column_name, $this->searchText];
                }
            }
            $query->filterWhere($orWhere);
        } elseif (!$this->isEmpty($this->searchItem)) {
            if ($this->searchItem == 'corp_master_id') {
                $query->orFilterWhere(['like', CorpMaster::tableName() . '.corp_name', $this->searchText]);
            } else {
                $query->orFilterWhere(['like', $this->searchItem, $this->searchText]);
            }
        }
        // 各項目検索
        $query->andFilterWhere(['=', 'client_master.valid_chk', $this->valid_chk])
            ->andFilterWhere([ClientChargePlan::tableName() . '.client_charge_type' => $this->clientChargeType])
            ->andFilterWhere([ClientCharge::tableName() . '.client_charge_plan_id' => $this->clientChargePlanId])
            // 権限適用
            ->andFilterWhere([self::tableName() . '.corp_master_id' => $this->corp_master_id]);

        if ($this->searchItem == 'all' || $this->searchItem == 'corp_master_id' || strpos(ArrayHelper::getValue($params, 'sort'), 'corp_master_id') !== false) {
            $query->joinWith('corpMaster');
        }
        if (!$this->isEmpty($this->clientChargeType)) {
            $query->joinWith('clientCharges.clientChargePlan')->groupBy(self::tableName() . '.id');
        } elseif (!$this->isEmpty($this->clientChargePlanId)) {
            $query->joinWith('clientCharges')->groupBy(self::tableName() . '.id');
        }
    }
}
