<?php

namespace app\models\manage;

use app\common\Helper\JmUtils;
use app\modules\manage\models\Manager;
use proseeds\models\Tenant;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app;
use app\common\SearchModelTrait;
use yii\helpers\Url;

/**
 * アクセスログの拡張モデル
 *
 * @property int $jobNo
 * @property int $accessPageName geOnly
 */
class AccessLogSearch extends AccessLog
{
    use SearchModelTrait;

    /** ページサイズ（一覧） */
    const PAGE_SIZE = 10;
    /** ページサイズ（CSV） */
    const CSV_SIZE_LIMIT = 200000;
    /**
     * アクセスページ
     */
    const NATIONWIDE_TOP_PAGE = 0;
    const ONE_AREA_TOP_PAGE = 1;
    const AREA_TOP_PAGE = 2;
    const JOB_DETAIL_PAGE = 3;
    const APPLIED_PAGE = 4;

    /** @var string キーワード検索対象・キーワード */
    public $searchItem;
    public $searchText;

    /** @var string|int アクセス日時 */
    public $searchStartDate;
    public $searchEndDate;

    /** @var int $accessPageId アクセスページ */
    public $accessPageId;

    /** @var string $_rootUrl rootのURL */
    private $_rootUrl;
    /** @var string $_jobDetailBaseUrl 求人詳細画面のURLの共通部分 */
    private $_jobDetailBaseUrl;
    /** @var string $_appliedBaseUrl 応募完了画面のURL */
    private $_appliedBaseUrl;
    /** @var app\components\Area $_areaComp エリアcomponent */
    private $_areaComp;
    /** @var Tenant $_tenant tenant model */
    private $_tenant;


    /**
     * モデルの初期化
     */
    public function init()
    {
        parent::init();
        // コンポーネントをpropertyへ
        $this->_tenant = Yii::$app->tenant->tenant;
        $this->_areaComp = Yii::$app->area;
        // 判定に使うURLをpropertyにキャッシュ
        $this->_rootUrl = Url::to('/', true);
        $this->_appliedBaseUrl = Url::to('/apply/complete', true);
        $this->_jobDetailBaseUrl = $this->_rootUrl . $this->_tenant->kyujin_detail_dir;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['accessPageId', 'carrier_type', 'jobNo'], 'integer'],
            [['access_url', 'access_user_agent', 'access_referrer'], 'string'],
            ['searchStartDate', 'date', 'timestampAttribute' => 'searchStartDate'],
            ['searchEndDate', 'date', 'timestampAttribute' => 'searchEndDate'],
        ], $this->getCsvSearchRules());
    }

    /**
     * ラベル設定
     */
    public function attributeLabels()
    {
        //検索及び一覧で表示するラベルを設定
        return ArrayHelper::merge(parent::attributeLabels(), [
            'accessPageId' => Yii::t('app', 'アクセスページ'),
            'accessPageName' => Yii::t('app', 'アクセスページ'),
            'access_url' => 'アクセスURL',
            'access_user_agent' => 'ユーザーエージェント',
            'access_referrer' => 'リファラー',
            'accessed_at' => Yii::t('app', 'アクセス日時'),
            'carrier_type' => 'アクセス機器',
            'jobNo' => Yii::$app->functionItemSet->job->items['job_no']->label,
        ]);
    }

    /**
     * ページ名を配列で返す
     * @return array
     */
    public static function accessPageNames()
    {
        return [
            self::NATIONWIDE_TOP_PAGE => Yii::t('app', '全国トップ'),
            self::ONE_AREA_TOP_PAGE => Yii::t('app', 'トップ'),
            self::AREA_TOP_PAGE => Yii::t('app', 'エリアトップ'),
            self::JOB_DETAIL_PAGE => Yii::t('app', '求人詳細'),
            self::APPLIED_PAGE => Yii::t('app', '応募完了'),
        ];
    }

    /**
     * access_urlを元にページ名を返すgetter
     * @return mixed|null
     */
    public function getAccessPageName()
    {
        $accessPageNames = self::accessPageNames();
        // トップページ
        if ($this->isRootPage()) {
            if ($this->_areaComp->isOneArea()) {
                return $accessPageNames[self::ONE_AREA_TOP_PAGE];
            }
            return $accessPageNames[self::NATIONWIDE_TOP_PAGE];
        }
        // エリアトップページ
        if ($this->isAreaTopPage()) {
            return $accessPageNames[self::AREA_TOP_PAGE];
        }
        if ($this->isJobDetailPage()) {
            return $accessPageNames[self::JOB_DETAIL_PAGE];
        }
        // 応募完了ページ
        if ($this->isAppliedPage()) {
            return $accessPageNames[self::APPLIED_PAGE];
        }
        // その他
        return null;
    }

    /**
     * ルートページか否か
     * @return bool
     */
    public function isRootPage()
    {
        return $this->access_url == $this->_rootUrl;
    }

    /**
     * エリアトップページか否か
     * @return bool
     */
    public function isAreaTopPage()
    {
        $directories = str_replace($this->_rootUrl, '', $this->access_url);
        $areaDirs = ArrayHelper::getColumn($this->_areaComp->models, 'area_dir');
        return in_array($directories, $areaDirs);
    }

    /**
     * 求人詳細ページか否か
     * @return bool
     */
    public function isJobDetailPage()
    {
        return strpos($this->access_url, $this->_jobDetailBaseUrl) !== false;
    }

    /**
     * 応募完了ページか否か
     * @return bool
     */
    public function isAppliedPage()
    {
        return strpos($this->access_url, $this->_appliedBaseUrl) !== false;
    }

    /**
     * アクセスログ一覧を検索して結果を取得します。
     * @param array $params GETパラメータ
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::authFind()->with('jobMaster');

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
                    'accessed_at' => SORT_DESC,
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
        // 検索クエリセット
        $this->searchWhere($query);
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
        $query = self::authFind()->with('jobMaster');

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
                    'accessed_at' => SORT_DESC,
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
        $this->searchWhere($query);
        // 表示初期化のための処理
        if ($this->clientMasterId && !$this->corpMasterId) {
            $this->corpMasterId = ArrayHelper::getValue(ClientMaster::find()->select('corp_master_id')->where(['id' => $this->clientMasterId])->one(), 'corp_master_id');
        }
        // 選択した値に関するwhere句を追加
        $this->selected($query);

        return $dataProvider;
    }

    /**
     * ActiveQueryに検索条件を追加
     * @param $query \yii\Db\ActiveQuery
     */
    private function searchWhere($query)
    {
        // queryセット
        // アクセスページ
        if (!JmUtils::isEmpty($this->accessPageId)) {
            switch ($this->accessPageId) {
                case self::NATIONWIDE_TOP_PAGE:
                case self::ONE_AREA_TOP_PAGE:
                    $query->andWhere(['access_url' => $this->_rootUrl]);
                    break;
                case self::AREA_TOP_PAGE:
                    $orCondition = ['or'];
                    foreach ($this->_areaComp->models as $area) {
                        $orCondition[] = ['access_url' => $this->_rootUrl . $area->area_dir];
                    }
                    $query->andWhere($orCondition);
                    break;
                case self::JOB_DETAIL_PAGE:
                    // 前方一致にするために第4引数をfalseにしてvalueのescapeをoffにしている
                    $query->andWhere(['like', 'access_url', addcslashes($this->_jobDetailBaseUrl, '_%') . '%', false]);
                    break;
                case self::APPLIED_PAGE:
                    // 前方一致にするために第4引数をfalseにしてvalueのescapeをoffにしている
                    $query->andWhere(['like', 'access_url', addcslashes($this->_appliedBaseUrl, '_%') . '%', false]);
                    break;
            }
        }

        // アクセス日時
        $query->andFilterWhere(['>=', self::tableName() . '.accessed_at', $this->searchStartDate]);
        if (!$this->isEmpty($this->searchEndDate)) {
            $query->andWhere(['<=', self::tableName() . '.accessed_at', strtotime('+1day', $this->searchEndDate)]);
        }
        // アクセスURL
        $query->andFilterWhere(['=', self::tableName() . '.access_url', $this->access_url]);
        // アクセスされたユーザエージェント
        $query->andFilterWhere(['like', self::tableName() . '.access_user_agent', $this->access_user_agent]);
        // アクセスされたリファラー
        $query->andFilterWhere(['like', self::tableName() . '.access_referrer', $this->access_referrer]);
        // アクセスされた機器
        $query->andFilterWhere(['=', self::tableName() . '.carrier_type', $this->carrier_type]);
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
            // noとidの順番は逆転することは無いので
            'jobNo' => [
                'asc' => ['job_master_id' => SORT_ASC],
                'desc' => ['job_master_id' => SORT_DESC],
            ],
            'accessPageName' => [
                'asc' => ['access_url' => SORT_ASC],
                'desc' => ['access_url' => SORT_DESC],
            ],
        ];
    }

    /**
     * アクセスページリストを取得する。
     * @return array 状態リスト
     */
    public static function accessPageArray()
    {
        /** @var app\components\Area $areaComp エリアcomponent */
        $areaComp = Yii::$app->area;
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;

        $accessPageNames = self::accessPageNames();
        if ($identity->myRole == Manager::OWNER_ADMIN) {
            // 運営元はtopも表示する
            if ($areaComp->isOneArea()) {
                ArrayHelper::remove($accessPageNames, self::NATIONWIDE_TOP_PAGE);
                ArrayHelper::remove($accessPageNames, self::AREA_TOP_PAGE);
            } else {
                ArrayHelper::remove($accessPageNames, self::ONE_AREA_TOP_PAGE);
            }
        } else {
            // その他はtopは表示しない
            ArrayHelper::remove($accessPageNames, self::NATIONWIDE_TOP_PAGE);
            ArrayHelper::remove($accessPageNames, self::AREA_TOP_PAGE);
            ArrayHelper::remove($accessPageNames, self::ONE_AREA_TOP_PAGE);
        }
        return $accessPageNames;
    }

    /**
     * オートコンプリートリストの取得
     * @param string $select 取得項目
     * @param string $value 検索内容(入力内容)
     * @return array
     */
    public static function getAutoCompleteList($select, $value)
    {
        $list = [];
        $autoCompletes = self::authFind()
            ->select($select)
            ->distinct(true)
            ->andFilterWhere(['like', $select, addcslashes($value, '_%')])
            ->all();

        foreach ((array) $autoCompletes as $autoComplete) {
            $list[] = $autoComplete->$select;
        }
        return $list;
    }

    /**
     * ユーザーエージェント、リファラーオートコンプリートのsourceスクリプト取得
     *      - 部分一致を判定して、入力後に候補を出力する。
     * @param string $targetSelector オートコンプリート対象セレクタ
     * @param string $url ajax用のURL
     * @return string
     */
    public static function getScriptSource($targetSelector = '.auto-complete-access-log', $url)
    {
        //配列をjson形式にしてJSで解釈できるように
        return <<< JS
                function(request, response) {
                    var value = $('{$targetSelector}').val();
                    $.ajax({
                      method : 'get',
                      url : '{$url}',
                      data : { q:value },
                      dataType: 'json',
                      success: function(res) {
                          response(
                              $.grep(res.results, function(value){
                                  return value;
                              })
                          )
                      },
                    });
                }
JS;
    }

    /**
     * ユーザーエージェント、リファラーオートコンプリートのfocusスクリプト取得
     *      - オートコンプリートフォーカスを発火させて候補の自動入力をさせる。
     * @return string
     */
    public static function getScriptFocus()
    {
        return <<< JS
                function( event, ui ) {
                    ui.item.value = ui.item.label;
                }
JS;
    }
}
