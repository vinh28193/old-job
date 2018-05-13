<?php

namespace app\models\manage;

use app\modules\manage\models\Manager;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\common\SearchModelTrait;

/**
 * SearchApplication represents the model behind the search form about `app\models\manage\ApplicationMaster`.
 *
 * @property string|integer $searchStartDate
 * @property string|integer $searchEndDate
 * @property int $clientMasterId
 * @property int $corpMasterId
 * @property int $deleted_at
 */
class ApplicationMasterSearch extends ApplicationMaster
{
    use SearchModelTrait;

    /** ページサイズ */
    const PAGE_SIZE_LIMIT = 10;
    /** 要件定義書にて定義 */
    const CSV_SIZE_LIMIT = 200000;
    /** @var string 検索項目・検索文字列 */
    public $searchItem;
    public $searchText;
    /** @var string|int 応募日検索開始、終了日 */
    public $searchStartDate;
    public $searchEndDate;
    /** @var int 仕事no. */
    public $jobNo;
    /** @var int getter用 */
    private $_corpMasterId;
    private $_clientMasterId;
    /** @var int プランid */
    public $clientChargePlanId;
    /** @var boolean 原稿削除済みフラグ */
    public $isJobDeleted = 0;
    /** @var string 求人モデル名 */
    private $_jobRelationName = 'jobMaster';
    private $_jobTableName = 'job_master';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['birthDateYear', 'birthDateMonth', 'birthDateDay'], 'safe'],
            [['pref_id', 'corpMasterId', 'clientMasterId', 'clientChargePlanId', 'application_status_id'], 'number'],
            [['searchItem', 'searchText', 'jobNo'], 'string'],
            ['searchStartDate', 'date', 'timestampAttribute' => 'searchStartDate'],
            ['searchEndDate', 'date', 'timestampAttribute' => 'searchEndDate'],
            [['isJobDeleted', 'sex'], 'boolean'],
            ['isJobDeleted', 'required'],
        ], $this->getCsvSearchRules());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'jobNo' => Yii::$app->functionItemSet->job->items['job_no']->label,
            'corpMasterId' => ArrayHelper::getValue(parent::attributeLabels(), 'corpLabel'),
            'clientMasterId' => ArrayHelper::getValue(parent::attributeLabels(), 'clientLabel'),
            'clientChargePlanId' => Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label,
            'isJobDeleted' => Yii::t('app', '応募原稿の状態'),
        ]);
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // query呼び出し
        $query = self::find();
        // DataProvider呼び出し
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::PAGE_SIZE_LIMIT,
            ],
            'sort' => [
                'defaultOrder' => [
                    'application_no' => SORT_DESC,
                ],
            ],
        ]);
        // sort追加設定
        $this->searchSort($dataProvider);
        // load処理
        if (!$this->load($params) || !$this->loadAuthParam() || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }
        // 検索クエリセット
        $this->searchWhere($query, $params);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function csvSearch($params)
    {
        // query呼び出し
        $query = ApplicationMaster::find();
        // DataProvider呼び出し
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::CSV_SIZE_LIMIT,
            ],
            'sort' => [
                'defaultOrder' => [
                    'application_no' => SORT_DESC,
                ],
            ],
        ]);
        // sort追加設定
        $this->searchSort($dataProvider);
        // gridの、json形式の値を使いやすい形に変換
        $params = $this->parse($params, $this->formName());
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
     * 削除するレコードのmodelを取得
     * @param $params
     * @return ApplicationMasterSearch[]|bool
     */
    public function deleteSearch($params)
    {
        $query = self::find();
        // gridの、json形式の値を使いやすい形に変換
        $params = $this->parse($params, $this->formName());
        // load処理とvalidateのチェック
        // 何もloadできなかった場合(初期状態)やvalidate通らないものはfalseを返す
        if (!$this->load($params) || !$this->validate()) {
            return false;
        }
        // 検索クエリセット
        $this->searchWhere($query, $params);
        $this->selected($query);
        return $query->all();
    }

    /**
     * バックアップを取って削除
     * 削除件数が多い場合のことを想定してbatchInsertしています
     * @param ApplicationMasterSearch[] $models
     * @return int 削除件数
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function backupAndDelete($models)
    {
        $columnNames = ApplicationMasterBackup::getTableSchema()->columnNames;
        $values = [];
        foreach ($models as $model) {
            /** @var JobMasterSearch $model */
            $value = [];
            foreach ($columnNames as $columnName) {
                $value[] = $model->$columnName;
            }
            $values[] = $value;
        }
        Yii::$app->db->createCommand()->batchInsert(ApplicationMasterBackup::tableName(), $columnNames, $values)->execute();
        return $this->deleteAll(['id' => ArrayHelper::getColumn($models, 'id')]);
    }

    /**
     * ActiveQueryに検索条件を追加
     * @param $query \yii\Db\ActiveQuery
     * @param $params array
     */
    private function searchWhere($query, $params)
    {
        if ($this->isJobDeleted) {
            $this->_jobRelationName = 'jobMasterBackup';
            $this->_jobTableName = 'job_master_backup';
            $query->andFilterWhere(['not', ['job_master_id' => JobMaster::find()->select('id')->column()]]);
        } else {
            $query->where(['not', [$this->_jobTableName . '.id' => null]]);
        }
        $query->with([$this->_jobRelationName . '.clientMaster.corpMaster', 'pref', 'applicationStatus', 'occupation']);
        // キーワード検索
        if ($this->searchItem == 'all') {
            $orWhere = ['or'];
            foreach (Yii::$app->functionItemSet->application->searchableByKeyWord as $attribute) {
                $orWhere[] = ['like', self::tableName() . '.' . $attribute->column_name, $this->searchText];
            }
            $query->andFilterWhere($orWhere)
                ->orFilterWhere(['like', 'concat(name_sei, name_mei)', preg_replace('/[\s　]/u', '', $this->searchText)])
                ->orFilterWhere(['like', 'concat(kana_sei, kana_mei)', preg_replace('/[\s　]/u', '', $this->searchText)]);
        } elseif (isset($this->searchItem)) {
            if ($this->searchItem == 'fullName') {
                $query->andFilterWhere(['like', 'concat(name_sei, name_mei)', preg_replace('/[\s　]/u', '', $this->searchText)]);
            } elseif ($this->searchItem == 'fullNameKana') {
                $query->andFilterWhere(['like', 'concat(kana_sei, kana_mei)', preg_replace('/[\s　]/u', '', $this->searchText)]);
            } else {
                $query->andFilterWhere(['like', $this->searchItem, $this->searchText]);
            }
        }
        // 状況・応募日・都道府県・性別の検索
        $query->andFilterWhere(['sex' => $this->sex])
            ->andFilterWhere(['pref_id' => $this->pref_id])
            ->andFilterWhere(['application_status_id' => $this->application_status_id])
            ->andFilterWhere(['>=', self::tableName() . '.created_at', $this->searchStartDate]);
        if (!$this->isEmpty($this->searchEndDate)) {
            $query->andWhere(['<=', self::tableName() . '.created_at', $this->searchEndDate + 24 * 60 * 60]);
        }
        // 生年月日の検索
        // todo もう少しやり方何とかならんのか検討・修正
        if ($this->getBirthDate() != '') {
            // andFilterWhereのlikeを使うと%がescapeされためandWhereを使用
            $query->andWhere('birth_date like "%' . $this->getBirthDate() . '%"');
        }
        // join分岐（検索とソートのパラメータを見て必要十分なjoinをする）
        if (strpos(ArrayHelper::getValue($params, 'sort'), 'corpLabel') !== false) {
            $query->joinWith($this->_jobRelationName . '.clientMaster.corpMaster');
        } elseif (!$this->isEmpty($this->corpMasterId) || strpos(ArrayHelper::getValue($params, 'sort'), 'clientLabel') !== false) {
            $query->joinWith($this->_jobRelationName . '.clientMaster');
        } elseif (!$this->isEmpty($this->jobNo) || !$this->isEmpty($this->clientMasterId) || !$this->isEmpty($this->clientChargePlanId) || !$this->isJobDeleted) {
            $query->joinWith($this->_jobRelationName . '');
        }
        // relational dataの検索
        $query->andFilterWhere(['like', $this->_jobTableName . '.job_no', $this->jobNo])
            ->andFilterWhere(['=', $this->_jobTableName . '.client_charge_plan_id', $this->clientChargePlanId])
            ->andFilterWhere(['=', $this->_jobTableName . '.client_master_id', $this->clientMasterId])
            ->andFilterWhere(['=', ClientMaster::tableName() . '.corp_master_id', $this->corpMasterId]);
    }

    /**
     * @return int
     */
    public function getCorpMasterId()
    {
        return $this->_corpMasterId ?: $this->clientModel->corp_master_id;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setCorpMasterId($value)
    {
        return $this->_corpMasterId = $value;
    }

    /**
     * @return int
     */
    public function getClientMasterId()
    {
        return $this->_clientMasterId ?: $this->jobModel->client_master_id;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setClientMasterId($value)
    {
        return $this->_clientMasterId = $value;
    }

    /**
     * client_masterとのrelation
     * idに値が無くかつclientMasterIdに値があるという状況でrelationする必要があるため
     * @return $this
     */
    public function getClientMaster()
    {
        return $this->hasOne(ClientMaster::className(), ['id' => 'clientMasterId']);
    }

    /**
     * ActiveDataProviderにsortパラメータを追加
     * @param $dataProvider \yii\data\ActiveDataProvider
     */
    private function searchSort($dataProvider)
    {
        $dataProvider->sort->attributes += [
            'clientLabel' => [
                'asc' => [ClientMaster::tableName() . '.client_name' => SORT_ASC],
                'desc' => [ClientMaster::tableName() . '.client_name' => SORT_DESC],
            ],
            'corpLabel' => [
                'asc' => [CorpMaster::tableName() . '.corp_name' => SORT_ASC],
                'desc' => [CorpMaster::tableName() . '.corp_name' => SORT_DESC],
            ],
            'fullName' => [
                'asc' => ['concat(name_sei, name_mei)' => SORT_ASC],
                'desc' => ['concat(name_sei, name_mei)' => SORT_DESC],
            ],
            'fullNameKana' => [
                'asc' => ['concat(kana_sei, kana_mei)' => SORT_ASC],
                'desc' => ['concat(kana_sei, kana_mei)' => SORT_DESC],
            ],
            'jobMaster.job_no' => [
                'asc' => [JobMaster::tableName() . '.job_no' => SORT_ASC],
                'desc' => [JobMaster::tableName() . '.job_no' => SORT_DESC],
            ],
        ];
    }

    /**
     * 権限を元に検索条件をロードする
     * @return bool
     */
    private function loadAuthParam()
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        switch ($identity->myRole) {
            case Manager::OWNER_ADMIN:
                return true;
                break;
            case Manager::CORP_ADMIN:
                $this->corpMasterId = $identity->corp_master_id;
                return true;
                break;
            case Manager::CLIENT_ADMIN:
                $this->corpMasterId = $identity->corp_master_id;
                $this->clientMasterId = $identity->client_master_id;
                return true;
                break;
            default :
                return false;
                break;
        }
    }

    /**
     * 一括削除時にdeleted_atを取得するためのgetter
     * @return int
     */
    public function getDeleted_at()
    {
        return time();
    }
}
