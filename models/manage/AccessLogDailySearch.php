<?php

namespace app\models\manage;

use app\modules\manage\models\Manager;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app;
use app\common\SearchModelTrait;
use yii\helpers\Url;
use app\models\manage\searchkey\Pref;
use app\models\manage\CorpMaster;

/**
 * アクセスログの拡張モデル
 * @property int $jobNo
 */
class AccessLogDailySearch extends AccessLog
{
    use SearchModelTrait;

    /** アクセス月 - 今月or先月or先々月 */
    const CURRENT_MONTH = 1;
    const BEFORE_ONE_MONTH = 2;
    const BEFORE_TWO_MONTH = 3;

    /** ページサイズ（一覧） */
    const PAGE_SIZE = 10;
    /** ページサイズ（CSV） */
    const CSV_SIZE_LIMIT = 200000;
    /** @var string キーワード検索対象・キーワード */
    public $searchItem;
    public $searchText;

    /** @var int getter用 */
    private $_corpMasterId;
    private $_clientMasterId;

    /** @var int アクセス月. */
    public $accessMonth;

    /** @var int 全国TOP_PC . */
    public $zenkokuPc;

    /** @var int 全国TOP_スマートフォン. */
    public $zenkokuSp;

    /** @var int エリアTOP_PC. */
    public $areaPc;

    /** @var int エリアTOP_スマートフォン. */
    public $areaSp;

    /** @var int 求人詳細_PC. */
    public $jobPc;

    /** @var int 求人詳細_スマートフォン. */
    public $jobSp;

    /** @var int 応募完了_PC. */
    public $applicationPc;

    /** @var int 応募完了_スマートフォン. */
    public $applicationSp;

    /** @var int 都道府県コード. */
    public $prefId;

    public $accessDate;

    /** @var int 都道府県名. */
    public $prefName;

    /** @var int 代理店名. */
    public $corpName;

    /** @var string 求人モデル名 */
    private $_jobRelationName = 'jobMaster';
    private $_jobTableName = 'job_master';

    /** @var string $_rootUrl rootのURL */
    private $_rootUrl;
    /** @var app\components\Area $_areaComp エリアcomponent */
    private $_areaComp;

    /**
     * モデルの初期化
     */
    public function init()
    {
        parent::init();
        // コンポーネントをpropertyへ
        $this->_areaComp = Yii::$app->area;
        // 判定に使うURLをpropertyにキャッシュ
        $this->_rootUrl = Url::to('/', true);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return  array_merge([
            [['clientMasterId', 'corpMasterId','accessMonth', 'prefId', 'jobNo'], 'integer'],
        ], $this->getCsvSearchRules());
    }

    /**
     * ラベル設定
     */
    public function attributeLabels()
    {
        // ワンエリア表示の場合は、「全国TOP」「エリアTOP」ではなく「TOP」のみ。
        if ($this->_areaComp->isOneArea()) {
                $topName = 'トップ';
        } else {
                $topName = '全国トップ';
        }
        //検索及び一覧で表示するラベルを設定
        return ArrayHelper::merge(parent::attributeLabels(), [
            'jobNo' => Yii::$app->functionItemSet->job->items['job_no']->label,
            'zenkokuPc' => Yii::t('app', $topName . '_PC'),
            'zenkokuSp' => Yii::t('app', $topName . '_スマートフォン'),
            'areaPc' => Yii::t('app', 'エリアトップ_PC'),
            'areaSp' => Yii::t('app', 'エリアトップ_スマートフォン'),
            'jobPc' => Yii::t('app', Yii::$app->nameMaster->JobName . '詳細_PC'),
            'jobSp' => Yii::t('app', Yii::$app->nameMaster->JobName . '詳細_スマートフォン'),
            'applicationPc' => Yii::t('app', Yii::$app->nameMaster->ApplicationName . '完了_PC'),
            'applicationSp' => Yii::t('app', Yii::$app->nameMaster->ApplicationName . '完了_スマートフォン'),
            'accessMonth' => Yii::t('app', 'アクセス月'),
            'prefId' => Yii::t('app', '求人原稿で設定した<br />都道府県'),
            'prefName' =>  Yii::t('app', '都道府県'),
            'corpName' =>  Yii::t('app', '代理店'),
            'corpMasterId' => Yii::$app->functionItemSet->corp->items['corp_name']->label,
            'clientMasterId' => Yii::$app->functionItemSet->client->items['client_name']->label,
        ]);
    }

    /**
     * アクセスログ一覧を検索して結果を取得します。
     * @param array $params GETパラメータ
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        //代理店管理者、掲載企業管理者の場合は、紐づく情報だけを取得する
        $identity = Yii::$app->user->identity;
        if ($identity->myRole == Manager::OWNER_ADMIN) {
            $query = $this->findTop();
            $subQuery = $this->findJob();
            $query->leftJoin(
                ['app' => $subQuery,],
                'app.tenant_id = ' . self::tableName() . '.tenant_id AND 
                    app.accessDate = ' . self::tableName() . '.search_date'
            );
        } else {
            $query = $this->findJob();
        }

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
                    'accessDate' => SORT_DESC,
                ],
            ],
        ]);
        // sort追加設定
        $this->searchSort($dataProvider);

        // load処理
        if (!$this->load($params) || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // クリアボタン押下時、アクセス月を今月に設定
        if (empty($this->accessMonth)) {
            $this->accessMonth = self::CURRENT_MONTH;
        }

        //代理店管理者、掲載企業管理者の場合は、紐づく情報だけを取得する
        if ($identity->myRole == Manager::OWNER_ADMIN) {
            // 検索クエリセット(全国TOP、エリアTOP検索用)
            $this->searchWhereTop($query);
            // 検索サブクエリセット（求人詳細、応募完了検索用）
            $this->searchWhereJob($subQuery);
        } else {
            // 検索サブクエリセット（求人詳細、応募完了検索用）
            $this->searchWhereJob($query);
        }

        // 表示初期化のための処理
        if ($this->clientMasterId && !$this->corpMasterId) {
            $this->corpMasterId = ArrayHelper::getValue(ClientMaster::find()->select('corp_master_id')->where(['id' => $this->clientMasterId])->one(), 'corp_master_id');
        }

        return $dataProvider;
    }

    /**
     * アクセスログ一覧を検索して結果を取得します。
     * @param array $params GETパラメータ
     * @return ActiveDataProvider
     */
    public function csvSearch($params)
    {
        //代理店管理者、掲載企業管理者の場合は、紐づく情報だけを取得する
        $identity = Yii::$app->user->identity;
        if ($identity->myRole == Manager::OWNER_ADMIN) {
            $query = $this->findTop();
            $subQuery = $this->findJob();
            $query->leftJoin(
                ['app' => $subQuery,],
                'app.tenant_id = ' . self::tableName() . '.tenant_id AND 
                    app.accessDate = ' . self::tableName() . '.search_date'
            );
        } else {
            $query = $this->findJob();
        }

        //検索して管理者情報のプロバイダ取得
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //1ページあたりのサイズ
            'pagination' => [
                'pageSize' => self::CSV_SIZE_LIMIT,
            ],
            //ソート
            'sort' => [
                'defaultOrder' => [
                    'accessDate' => SORT_DESC,
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

        // クリアボタン押下時、アクセス月を今月に設定
        if (empty($this->accessMonth)) {
            $this->accessMonth = self::CURRENT_MONTH;
        }

        //代理店管理者、掲載企業管理者の場合は、紐づく情報だけを取得する
        if ($identity->myRole == Manager::OWNER_ADMIN) {
            // 検索クエリセット(全国TOP、エリアTOP検索用)
            $this->searchWhereTop($query);
            // 検索サブクエリセット（求人詳細、応募完了検索用）
            $this->searchWhereJob($subQuery);
        } else {
            // 検索サブクエリセット（求人詳細、応募完了検索用）
            $this->searchWhereJob($query);
        }

        // 表示初期化のための処理
        if ($this->clientMasterId && !$this->corpMasterId) {
            $this->corpMasterId = ArrayHelper::getValue(ClientMaster::find()->select('corp_master_id')->where(['id' => $this->clientMasterId])->one(), 'corp_master_id');
        }
        // 選択した値に関するwhere句を追加
        $this->selected($query);

        // 検索条件の内容追加
        // 都道府県名取得
        if (!$this->isEmpty($this->prefId)) {
            $this->prefName = Pref::find()->select('pref_name')->where(['id' => $this->prefId])->scalar();
            $query->addSelect(['("' . $this->prefName . '") as prefName']);
        }
        // 代理店名取得
        if (!$this->isEmpty($this->corpMasterId)) {
            $this->corpName = CorpMaster::find()->select('corp_name')->where(['id' => $this->corpMasterId])->scalar();
            $query->addSelect(['("' . $this->corpName . '") as corpName']);
        }
        if (!$this->isEmpty($this->jobNo)) {
            $query->addSelect(['(' . $this->jobNo . ') as jobNo']);
        }
        return $dataProvider;
    }

    /**
     * 全国TOP、地域TOPのSQL分取得
     * @return \yii\db\ActiveQuery
     */
    private function findTop()
    {
        return self::find()->select([
            'DATE_FORMAT(' . self::tableName() . '.search_date, \'%Y%m%d\') as id',
            self::tableName() . '.tenant_id',
            self::tableName() . '.search_date as accessDate',
            'SUM(access_url = \'' . $this->_rootUrl . '\' AND ' . self::tableName() . '.carrier_type = 0) as zenkokuPc',
            'SUM(access_url = \'' . $this->_rootUrl . '\' AND ' . self::tableName() . '.carrier_type = 1) as zenkokuSp',
            'SUM(access_url != \'' . $this->_rootUrl . '\' AND ' . self::tableName() . '.job_master_id is null AND ' . self::tableName() . '.carrier_type = 0) as areaPc',
            'SUM(access_url != \'' . $this->_rootUrl . '\' AND ' . self::tableName() . '.job_master_id is null AND ' . self::tableName() . '.carrier_type = 1) as areaSp',
            'COALESCE(app.jobPc, 0) as jobPc',
            'COALESCE(app.jobSp, 0) as jobSp',
            'COALESCE(app.applicationPc, 0) as applicationPc',
            'COALESCE(app.applicationSp, 0) as applicationSp',
            ])->groupBy(self::tableName() . '.search_date');
    }

    /**
     * 求人詳細、応募完了のSQL分取得
     * @return \yii\db\ActiveQuery
     */
    private function findJob()
    {
        return self::find()->joinWith('applicationMaster')->select([
            'DATE_FORMAT(' . self::tableName() . '.search_date, \'%Y%m%d\') as id',
            self::tableName() . '.tenant_id',
            self::tableName() . '.search_date as accessDate',
            'SUM(' . self::tableName() . '.job_master_id is not null AND ' . self::tableName() . '.application_master_id is null AND ' . self::tableName() . '.carrier_type = 0) as jobPc',
            'SUM(' . self::tableName() . '.job_master_id is not null AND ' . self::tableName() . '.application_master_id is null AND ' . self::tableName() . '.carrier_type = 1) as jobSp',
            'COALESCE(SUM(application_master.carrier_type = 0), 0) as applicationPc',
            'COALESCE(SUM(application_master.carrier_type = 1), 0) as applicationSp',
            ])->groupBy('accessDate');
    }

    /**
     * ActiveQueryに検索条件を追加
     * @param $query \yii\Db\ActiveQuery
     */
    private function searchWhereTop($query)
    {
        // アクセス月
        if (!empty($this->accessMonth)) {
            switch ($this->accessMonth) {
                //今月
                case self::CURRENT_MONTH:
                    $monthStart = date('Y/m/1');
                    $monthEnd = date('Y/m/1', strtotime('+1 month'));
                    break;
                //先月
                case self::BEFORE_ONE_MONTH:
                    $monthStart = date('Y/m/1', strtotime('-1 month'));
                    $monthEnd = date('Y/m/1');
                    break;
                //先々月
                case self::BEFORE_TWO_MONTH:
                    $monthStart = date('Y/m/1', strtotime('-2 month'));
                    $monthEnd = date('Y/m/1', strtotime('-1 month'));
                    break;
            }
            $query->andWhere(['>=', self::tableName() . '.accessed_at', strtotime($monthStart)]);
            $query->andWhere(['<', self::tableName() . '.accessed_at', strtotime($monthEnd)]);
        }
    }

    /**
     * ActiveQueryに検索条件を追加
     * @param $query \yii\Db\ActiveQuery
     */
    private function searchWhereJob($query)
    {
        // queryセット
        // join分岐（検索とソートのパラメータを見て必要十分なjoinをする）
        if (!$this->isEmpty($this->prefId)) {
            $query->innerJoin(['job_master2' => $this->_jobTableName], 'job_master2.id = ' . self::tableName() . '.job_master_id')
                  ->innerJoin('job_pref', 'job_master2.id = job_pref.job_master_id');
        }
        if (!$this->isEmpty($this->corpMasterId)) {
            $query->joinWith($this->_jobRelationName . '.clientMaster');
        } elseif (!$this->isEmpty($this->clientMasterId) || !$this->isEmpty($this->corpMasterId)) {
            $query->joinWith($this->_jobRelationName . '');
        }
        // アクセス月
        if (!empty($this->accessMonth)) {
            switch ($this->accessMonth) {
                //今月
                case self::CURRENT_MONTH:
                    $monthStart = date('Y/m/1');
                    $monthEnd = date('Y/m/1', strtotime('+1 month'));
                    break;
                //先月
                case self::BEFORE_ONE_MONTH:
                    $monthStart = date('Y/m/1', strtotime('-1 month'));
                    $monthEnd = date('Y/m/1');
                    break;
                //先々月
                case self::BEFORE_TWO_MONTH:
                    $monthStart = date('Y/m/1', strtotime('-2 month'));
                    $monthEnd = date('Y/m/1', strtotime('-1 month'));
                    break;
            }
            $query->andWhere(['>=', self::tableName() . '.accessed_at', strtotime($monthStart)]);
            $query->andWhere(['<', self::tableName() . '.accessed_at', strtotime($monthEnd)]);
        }
        // relational dataの検索
        $query->andFilterWhere(['=', $this->_jobTableName . '.client_master_id', $this->clientMasterId])
            ->andFilterWhere(['=', ClientMaster::tableName() . '.corp_master_id', $this->corpMasterId])
            ->andFilterWhere(['=', 'job_pref.pref_id', $this->prefId]);

        // 仕事ID(jobNoのsetterで自動代入されているのでjob_master_idがそのまま使える)
        $query->andFilterWhere([self::tableName() . '.job_master_id' => $this->job_master_id]);
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
            'accessDate' => [
                'asc' => ['accessDate' => SORT_ASC],
                'desc' => ['accessDate' => SORT_DESC],
            ],
            'zenkokuPc' => [
                'asc' => ['zenkokuPc' => SORT_ASC],
                'desc' => ['zenkokuPc' => SORT_DESC],
            ],
            'zenkokuSp' => [
                'asc' => ['zenkokuSp' => SORT_ASC],
                'desc' => ['zenkokuSp' => SORT_DESC],
            ],
            'areaPc' => [
                'asc' => ['areaPc' => SORT_ASC],
                'desc' => ['areaPc' => SORT_DESC],
            ],
            'areaSp' => [
                'asc' => ['areaSp' => SORT_ASC],
                'desc' => ['areaSp' => SORT_DESC],
            ],
            'jobPc' => [
                'asc' => ['jobPc' => SORT_ASC],
                'desc' => ['jobPc' => SORT_DESC],
            ],
            'jobSp' => [
                'asc' => ['jobSp' => SORT_ASC],
                'desc' => ['jobSp' => SORT_DESC],
            ],
            'applicationPc' => [
                'asc' => ['applicationPc' => SORT_ASC],
                'desc' => ['applicationPc' => SORT_DESC],
            ],
            'applicationSp' => [
                'asc' => ['applicationSp' => SORT_ASC],
                'desc' => ['applicationSp' => SORT_DESC],
            ],
        ];
    }

    /**
     * アクセス月リストを取得する。
     * @return array 状態リスト
     */
    public static function getAccessMonthList()
    {
        return [
            self::CURRENT_MONTH => Yii::t('app', '今月'),
            self::BEFORE_ONE_MONTH => Yii::t('app', '先月'),
            self::BEFORE_TWO_MONTH => Yii::t('app', '先々月'),
        ];
    }

    /**
     * ActiveQueryに検索条件を追加
     * @param $query \yii\Db\ActiveQuery
     */
    private function selected($query)
    {
        if (!$this->isEmpty($this->selected)) {
            if ($this->allCheck == true) {
                $query->andFilterWhere(['not', [self::tableName() . '.search_date' => $this->selected]]);
            } else {
                $query->andFilterWhere([self::tableName() . '.search_date' => $this->selected]);
            }
        }
    }
}
