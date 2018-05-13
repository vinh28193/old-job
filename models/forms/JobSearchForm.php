<?php

namespace app\models\forms;

use app\common\Helper\JmUtils;
use app\models\JobMasterDisp;
use app\models\JobSearch;
use app\models\manage\JobColumnSet;
use app\models\manage\JobMaster;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\JobDist;
use app\models\manage\searchkey\JobStationInfo;
use app\models\manage\searchkey\JobType;
use app\models\manage\searchkey\JobTypeBig;
use app\models\manage\searchkey\JobTypeCategory;
use app\models\manage\searchkey\JobTypeSmall;
use app\models\manage\searchkey\JobWage;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\PrefDist;
use app\models\manage\searchkey\PrefDistMaster;
use app\models\manage\searchkey\SearchkeyCategory;
use app\models\manage\searchkey\Station;
use app\models\manage\searchkey\WageCategory;
use app\models\manage\searchkey\WageItem;
use app\models\manage\SearchkeyMaster;
use app\models\SearchCategory;
use app\models\SearchItem;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\models\manage\searchkey\JobPref;

/**
 * Class JobSearchForm
 *
 * @package app\models\forms
 * @author  Nobuhiro Ueda <ueda@tech-vein.com>
 *
 * @property array               $distOptions
 * @property array               $wageOptions
 *
 * @property Area[]              $areas
 * @property Pref[]              $prefs                エリアで有効な都道府県のinstance
 * @property Station[]           $stations             エリアで有効な駅のinstance
 * @property PrefDistMaster[]    $prefDistricts        エリアで有効な地域グループのinstance
 * @property Dist[]              $districts            エリアで有効な市区町村のinstance
 * @property WageCategory[]      $wages                エリアで有効な給与カテゴリのinstance
 * @property JobTypeCategory[]   $jobTypes             エリアで有効な職種大カテゴリのinstance
 * @property JobSearch[]         $searchKeys
 * @property JobSearch           $principalKey
 * @property array               $stationParts
 * @property array               $stationRoutes        エリアで有効な路線[route_cd=>route_name]
 * @property array               $stationRouteChildren エリアで有効な路線[route_cd=>[station_cd => station_name]]
 * @property array               $keywords             検索フリーワードを配列で取得
 * @property SearchkeyCategory[] $principalCategories  スマホ特別キーのカテゴリのインスタンスの配列を返す
 */
class JobSearchForm extends Model
{
    const JOB_ITEM_TABLE_BASE = 'job_searchkey_item';
    const JOB_ITEM_MODEL_BASE = 'jobSearchkeyItem';

    const SCENARIO_TOP      = 'top';
    const SCENARIO_AREA_TOP = 'area-top';
    const SCENARIO_RESULT   = 'result';
    const SCENARIO_DETAIL   = 'detail';

    const DROPDOWN_CATE_PREFIX = 'cate_';
    const DROPDOWN_ITEM_PREFIX = 'item_';

    const FLAG_ON = 1;

    const DEFAULT_PAGE_SIZE = 10;// 暫定：JM1のページサイズに揃えた。

    const CONST_COLUMN_NAME_LIST = [
        'pref'                     => [
            'table'  => 'pref',
            'id'     => 'pref_no',
            'column' => 'pref_name',
        ],
        'pref_dist_master'         => [
            'table'  => 'dist',
            'id'     => 'dist_cd',
            'column' => 'dist_name',
        ],
        'pref_dist_master_parent'  => [
            'table'  => 'pref_dist_master',
            'id'     => 'pref_dist_master_no',
            'column' => 'pref_dist_name',
        ],
        'station'                  => [
            'table'  => 'station',
            'id'     => 'station_no',
            'column' => 'station_name',
        ],
        'station_parent'           => [
            'table'  => 'station',
            'id'     => 'route_cd',
            'column' => 'route_name',
        ],
        'wage_category_parent'     => [
            'table'  => 'wage_category',
            'id'     => 'wage_category_no',
            'column' => 'wage_category_name',
        ],
        'wage_category'            => [
            'table'  => 'wage_item',
            'id'     => 'wage_item_no',
            'column' => 'disp_price',
        ],
        'job_type_category_first'  => [
            'table'  => 'job_type_category',
            'id'     => 'job_type_category_cd',
            'column' => 'name',
        ],
        'job_type_category_parent' => [
            'table'  => 'job_type_big',
            'id'     => 'job_type_big_no',
            'column' => 'job_type_big_name',
        ],
        'job_type_category'        => [
            'table'  => 'job_type_small',
            'id'     => 'job_type_small_no',
            'column' => 'job_type_small_name',
        ],
    ];

    /** @var string */
    public $search_from;

    /** @var string */
    public $area;
    /** @var string */
    public $pref_parent;
    /** @var string */
    public $pref;
    /** @var string */
    public $station;
    /** @var string */
    public $station_parent;
    /** @var string */
    public $pref_dist_master_parent;
    /** @var string */
    public $pref_dist_master;
    /** @var string */
    public $wage_category_parent;
    /** @var string */
    public $wage_category;
    /** @var string */
    public $job_type_category_first;
    /** @var string */
    public $job_type_category_parent;
    /** @var string */
    public $job_type_category;
    /** @var string */
    public $keyword;
    /** @var string */
    public $searchkey_category1_parent;
    /** @var string */
    public $searchkey_category1;
    /** @var string */
    public $searchkey_category2_parent;
    /** @var string */
    public $searchkey_category2;
    /** @var string */
    public $searchkey_category3_parent;
    /** @var string */
    public $searchkey_category3;
    /** @var string */
    public $searchkey_category4_parent;
    /** @var string */
    public $searchkey_category4;
    /** @var string */
    public $searchkey_category5_parent;
    /** @var string */
    public $searchkey_category5;
    /** @var string */
    public $searchkey_category6_parent;
    /** @var string */
    public $searchkey_category6;
    /** @var string */
    public $searchkey_category7_parent;
    /** @var string */
    public $searchkey_category7;
    /** @var string */
    public $searchkey_category8_parent;
    /** @var string */
    public $searchkey_category8;
    /** @var string */
    public $searchkey_category9_parent;
    /** @var string */
    public $searchkey_category9;
    /** @var string */
    public $searchkey_category10_parent;
    /** @var string */
    public $searchkey_category10;
    /** @var string */
    public $searchkey_item11;
    /** @var string */
    public $searchkey_item12;
    /** @var string */
    public $searchkey_item13;
    /** @var string */
    public $searchkey_item14;
    /** @var string */
    public $searchkey_item15;
    /** @var string */
    public $searchkey_item16;
    /** @var string */
    public $searchkey_item17;
    /** @var string */
    public $searchkey_item18;
    /** @var string */
    public $searchkey_item19;
    /** @var string */
    public $searchkey_item20;

    /** @var string */
    public $disp_type_sort;

    /** @var int */
    public $pageSize = self::DEFAULT_PAGE_SIZE;
    /**
     * フォームとテーブル・名前用のリスト
     *
     * @var array
     */
    public $columnNameList = [];
    /** @var [attribute => 検索code]という配列をキャッシュする */
    public $attributeToCode;

    /**
     * 検索条件
     *
     * @var JobSearch[]
     */
    private $_searchKeys;
    /**
     * 結果キャッシュ用配列
     *
     * @var array
     */
    private $_cache = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        $list = [];

        for ($i = 1; $i <= 10; $i++) {
            $list['searchkey_category' . $i]             = [
                'table'  => 'searchkey_item' . $i,
                'id'     => 'searchkey_item_no',
                'column' => 'searchkey_item_name',
            ];
            $list['searchkey_category' . $i . '_parent'] = [
                'table'  => 'searchkey_category' . $i,
                'id'     => 'searchkey_category_no',
                'column' => 'searchkey_category_name',
            ];
        }

        for ($i = 11; $i <= 20; $i++) {
            $list['searchkey_item' . $i] = [
                'table'  => 'searchkey_item' . $i,
                'id'     => 'searchkey_item_no',
                'column' => 'searchkey_item_name',
            ];
        }

        $this->columnNameList = ArrayHelper::merge(
            self::CONST_COLUMN_NAME_LIST,
            $list
        );

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            array_map(function (JobSearch $jobSearch) {
                return [[$jobSearch->table_name], 'safe'];
            }, $this->searchKeys),
            array_map(function (JobSearch $jobSearch) {
                return [[$jobSearch->table_name . '_parent'], 'safe'];
            }, array_filter($this->searchKeys, function (JobSearch $jobSearch) {
                return $jobSearch->isCategory;
            })),
            [
                [['pref_parent'], 'safe'],
                [['station_parent'], 'safe'],
                [['pref_dist_master_parent'], 'safe'],
                [['wage_category_parent'], 'safe'],
                [['job_type_category_first'], 'safe'],
                [['job_type_category_parent'], 'safe'],
                [['search_from'], 'safe'],
                [['keyword'], 'string', 'max' => 100],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            ArrayHelper::map(
                $this->searchKeys,
                'table_name',
                'searchkey_name'
            ),
            [
                'keyword'                  => Yii::t('app', 'キーワード'),
                'pref_dist_master_parent'  => Yii::t('app', '地区名'),
                'station_parent'           => Yii::t('app', '路線'),
                'station'                  => Yii::t('app', '駅'),
                'job_type_category_first'  => Yii::t('app', '職種'),
                'search_from'              => Yii::t('app', '検索元'),
                'wage_category_parent'     => Yii::t('app', '給与カテゴリ'),
                'job_type_category_parent' => Yii::t('app', '職種カテゴリ'),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $searchKeyCategoryParents = [];
        for ($i = 0; $i <= 10; $i++) {
            $searchKeyCategoryParents [] = 'searchkey_category' . $i . '_parent';
        }
        $searchAttributes = array_merge(
            array_keys($this->attributeLabels()),
            $searchKeyCategoryParents
        );
        return array_merge(parent::scenarios(), [
                self::SCENARIO_TOP      => $searchAttributes,
                self::SCENARIO_AREA_TOP => $searchAttributes,
                self::SCENARIO_RESULT   => $searchAttributes,
                self::SCENARIO_DETAIL   => $searchAttributes,
            ]);
    }

    /**
     * 検索を実行
     *
     * @return ActiveDataProvider
     */
    public function search()
    {
        $dataProvider = new ActiveDataProvider([
            'query'      => $this->generateSearchQuery(),
            //1ページあたりのサイズ
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
            //ソート
            'sort'       => [
                'defaultOrder' => [
                    'disp_type_sort'  => SORT_DESC,
                    'disp_start_date' => SORT_DESC,
                    'updated_at'      => SORT_DESC,
                ],
            ],
        ]);

        $dataProvider->sort->attributes['disp_type_sort'] = [
            'asc'   => ['disp_type_sort' => SORT_ASC, 'disp_start_date' => SORT_ASC, 'updated_at' => SORT_ASC],
            'desc'  => ['disp_type_sort' => SORT_DESC, 'disp_start_date' => SORT_DESC, 'updated_at' => SORT_DESC],
            'label' => Yii::t('app', 'おすすめ順'),
        ];

        return $dataProvider;
    }

    /**
     * 注目情報の検索を実行
     * @return ActiveDataProvider
     */
    public function searchHotJob($sort_amount, $areaId)
    {
        $hotJob = $sort_amount;
        $this->area = $areaId;
        $disp_priority = ArrayHelper::map($hotJob->hotJobPriority, 'disp_priority', 'item');
        ksort($disp_priority);

        $sort = [];
        foreach ($disp_priority as $value){
            switch ($value){
                case 'updated_at' :
                    $value = ['updated_at' => SORT_DESC];
                    break;
                case 'disp_end_date' :
                    //日付のASCソートの際に、nullをソートの後ろに回す処理
                    $endDateNull = 'ISNULL(disp_end_date)';
                    array_push($sort, $endDateNull);
                    $value = ['disp_end_date' => SORT_ASC];
                    break;
                case 'disp_type' :
                    $value = ['disp_type_sort' => SORT_DESC];
                    break;
                case 'random' :
                    //ソートをランダム順にする
                    $value = 'RAND()';
                    break;
            }
            array_push($sort, $value);
        }

        $dataProvider = new ActiveDataProvider([
//            ActiveDataProviderのsortではISNULL(),RAND()が定義できないため、以下のqueryコードで対応
            'query' => $this->generateSearchQuery()->orderBy($sort[0])->addOrderBy($sort[1])->addOrderBy($sort[2])->addOrderBy($sort[3])->addOrderBy($sort[4]),
            //1ページあたりのサイズ
            'pagination' => [
                'pageSize' => $hotJob->disp_amount,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * 検索結果総数を得る
     *
     * @return false|null|string
     */
    public function count()
    {
        return $this->generateSearchQuery()->count();
    }

    /**
     * 給与オプションを取得
     *
     * @return array
     */
    public function getWageOptions()
    {
        $results = [];

        /** @var WageCategory $wage */
        foreach ($this->wages as $wage) {
            $results[$wage->wage_category_name] = ArrayHelper::map(
                $wage->wageItemValid,
                'wage_item_no',
                function (WageItem $item) use ($wage) {
                    return $item->disp_price;
                }
            );
        }

        return $results;
    }

    /**
     * 地域オプションを取得
     *
     * @return array
     */
    public function getDistOptions()
    {
        $results = [];

        /** @var PrefDistMaster $distMaster */
        foreach ($this->prefDistricts as $distMaster) {
            $results[$distMaster->pref_dist_name] = ArrayHelper::map(
                $distMaster->districts,
                'id',
                'dist_name'
            );
        }

        return $results;
    }

    /**
     * カテゴリ設定2階層のOptionを取得する
     *
     * @param $categories SearchCategory[]
     * @return array
     */
    public function getCategoryOptions($categories)
    {
        $results = [];
        foreach ($categories as $category) {
            if ($category->items) {
                $results[$category->searchkey_category_name] = ArrayHelper::map(
                    $category->items,
                    'searchkey_item_no',
                    'searchkey_item_name'
                );
            }
        }

        return $results;
    }

    /**
     * カテゴリ設定2階層と1階層を合わせたOptionを取得する
     *
     * @param $categories SearchCategory[]
     * @return array
     */
    public function getCategoryItemsList($categories)
    {
        $results = [];
        $list    = [];
        foreach ($categories as $k => $category) {
            if ($category->items) {
                $results['cate_' . $category->searchkey_category_no] = $category->searchkey_category_name;
                $list['cate_' . $category->searchkey_category_no]    = ['style' => 'font-weight: bold;'];
                foreach ($category->items as $item) {
                    $results['item_' . $item->searchkey_item_no] = $item->searchkey_item_name;
                }
            }
        }

        return ['results' => $results, 'list' => $list];
    }

    /**
     * attribute name から jobSearchを取得する
     *
     * @param $name
     * @return JobSearch|null
     */
    public function getSearchKeyByName($name)
    {
        return ArrayHelper::getValue(
            array_values(
                array_filter($this->searchKeys, function (JobSearch $jobSearch) use ($name) {
                    return $jobSearch->table_name == $name;
                })
            ),
            0
        );
    }

    /**
     * 検索条件項目を取得
     *
     * @return \app\models\JobSearch[]
     */
    public function getSearchKeys()
    {
        if ($this->_searchKeys) {
            return $this->_searchKeys;
        }

        $query = JobSearch::find()
            ->where(['valid_chk' => JobSearch::FLAG_VALID])
            ->orderBy([JobSearch::tableName() . '.sort' => SORT_ASC]);

        if ($this->scenario == self::SCENARIO_TOP) {
            // TOPページは4つだけ
            $query->limit(4);
        }

        if ($this->scenario == self::SCENARIO_AREA_TOP) {
            // 地域TOPはフラグONのだけ
            $query->andWhere([
                JobSearch::tableName() . '.is_on_top' => self::FLAG_ON,
            ]);
        }

        $this->_searchKeys = ArrayHelper::index($query->all(), 'table_name');

        return $this->_searchKeys;
    }

    /**
     * エリア情報を取得
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAreas()
    {
        if (isset($this->_cache['areas'])) {
            return $this->_cache['areas'];
        }

        $this->_cache['areas'] = Area::find()
            ->where(['valid_chk' => JobSearch::FLAG_VALID])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->_cache['areas'];
    }

    /**
     * 都道府県情報取得
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getPrefs()
    {
        if (isset($this->_cache['prefs'])) {
            return $this->_cache['prefs'];
        }

        $conditions = [];
        if ($this->scenario == self::SCENARIO_AREA_TOP || $this->area) {
            $conditions['area_id'] = $this->area;
        }

        $this->_cache['prefs'] = ArrayHelper::index(
            Pref::find()
                ->with('dispPrefDistMasters.districts')// todo 詳細画面で使わないようなら削除
                ->where($conditions)
                ->orderBy(['sort' => SORT_ASC])
                ->all(),
            'pref_no'
        );

        return $this->_cache['prefs'];
    }

    /**
     * 路線駅情報取得
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStations()
    {
        if (isset($this->_cache['stations'])) {
            return $this->_cache['stations'];
        }

        $this->_cache['stations'] = Station::find()
            ->where([
                'pref_no' => array_map(function (Pref $pref) {
                    return $pref->pref_no;
                }, $this->prefs),
            ])
            ->orderBy(['sort_no' => SORT_ASC])
            ->all();

        return $this->_cache['stations'];
    }

    /**
     * 地域グループ
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getPrefDistricts()
    {
        if (isset($this->_cache['prefDistricts'])) {
            return $this->_cache['prefDistricts'];
        }

        $this->_cache['prefDistricts'] = ArrayHelper::index(
            PrefDistMaster::find()
                ->where([
                    'pref_id'   => array_map(function (Pref $pref) {
                        return $pref->id;
                    }, $this->prefs),
                    'valid_chk' => JobSearch::FLAG_VALID,
                ])
                ->orderBy([
                    PrefDistMaster::tableName() . '.sort' => SORT_ASC,
                    PrefDistMaster::tableName() . '.id'   => SORT_ASC,
                ])
                ->all(),
            'pref_dist_master_no'
        );

        return $this->_cache['prefDistricts'];
    }

    /**
     * 現在のエリアで必要な市区町村を返す（キャッシュもする）
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getDistricts()
    {
        if (isset($this->_cache['districts'])) {
            return $this->_cache['districts'];
        }

        $this->_cache['districts'] = ArrayHelper::index(
            Dist::find()
                ->where([
                    'pref_no' => array_map(function (Pref $pref) {
                        return $pref->pref_no;
                    }, $this->prefs),
                ])
                ->orderBy([
                    Dist::tableName() . '.id' => SORT_ASC,
                ])
                ->all(),
            'dist_cd'
        );

        return $this->_cache['districts'];
    }

    /**
     * スマホ特別キーのカテゴリのインスタンスの配列を返す（キャッシュもする）
     *
     * @return mixed
     */
    public function getPrincipalCategories()
    {
        if (isset($this->_cache['principalCategories'])) {
            return $this->_cache['principalCategories'];
        }

        $this->_cache['principalCategories'] = $this->principalKey->getCategoryQuery()->with('items')->all();

        return $this->_cache['principalCategories'];
    }

    /**
     * 給与
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getWages()
    {
        if (isset($this->_cache['wages'])) {
            return $this->_cache['wages'];
        }

        $this->_cache['wages'] = ArrayHelper::index(
            WageCategory::find()
            ->where(['valid_chk' => JobSearch::FLAG_VALID])
            ->orderBy(['sort' => SORT_ASC])
            ->all(),
            'wage_category_no'
        );

        return $this->_cache['wages'];
    }

    /**
     * 給与
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getWageItems()
    {
        if (isset($this->_cache['wageItems'])) {
            return $this->_cache['wageItems'];
        }

        $this->_cache['wageItems'] = ArrayHelper::index(
            WageItem::find()
            ->where(['valid_chk' => JobSearch::FLAG_VALID])
            ->all(),
            'wage_item_no'
        );
        return $this->_cache['wageItems'];
    }

    /**
     * 職種
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getJobTypes()
    {
        if (isset($this->_cache['jobTypes'])) {
            return $this->_cache['jobTypes'];
        }

        $this->_cache['jobTypes'] = JobTypeCategory::find()
            ->joinWith([
                'jobTypeBig' => function (ActiveQuery $q) {
                    $q->joinWith([
                        'jobTypeSmall' => function (ActiveQuery $q) {
                            $q->where([
                                JobTypeSmall::tableName() . '.valid_chk' => JobSearch::FLAG_VALID,
                            ]);
                        },
                    ])->where([
                        JobTypeBig::tableName() . '.valid_chk' => JobSearch::FLAG_VALID,
                    ]);
                },
            ])
            ->where([JobTypeCategory::tableName() . '.valid_chk' => JobSearch::FLAG_VALID])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->_cache['jobTypes'];
    }

    /**
     * 路線表示用配列を返す
     *
     * @return array
     */
    public function getStationParts()
    {
        $prefNames = [];
        $prefSort  = [];    //並び順を記録するための、配列
        foreach ($this->prefs as $pref) {
            $prefNames[$pref->pref_no] = $pref->pref_name;
            $prefSort[$pref->pref_no]  = $pref->sort;
        }

        // 勤務地検索の「エリア」と「駅」で表示される都道府県の順番が違ったので、ソート。
        $stationArray = $this->stations;
        usort($stationArray, function ($first, $second) use ($prefSort) {
            /** @var $first Station */
            /** @var $second Station */
            $firstSort  = $prefSort[$first->pref_no];
            $secondSort = $prefSort[$second->pref_no];
            if ($firstSort == $secondSort) {
                return 0;
            }
            return ($firstSort < $secondSort) ? -1 : 1;
        });

        $results = [];
        foreach ($stationArray as $station) {
            $results[$station->pref_no][$station->railroad_company_cd][$station->route_cd][] = $station;

            // 名前取得用
            $this->_cache['stationPrefNames'][$station->pref_no]                 = $prefNames[$station->pref_no];
            $this->_cache['stationRailroadNames'][$station->railroad_company_cd] = $station->railroad_company_name;
            $this->_cache['stationRouteNames'][$station->route_cd]               = $station->route_name;
        }

        return $results;
    }

    /**
     * 路線沿線を取得
     *
     * @return array
     */
    public function getStationRoutes()
    {
        if (isset($this->_cache['stationRoutes'])) {
            return $this->_cache['stationRoutes'];
        }

        $routes   = [];
        $children = [];
        foreach ($this->stations as $station) {
            $routes[$station->route_cd]                         = $station->route_name;
            $children[$station->route_cd][$station->station_no] = $station->station_name;
        }
        $this->_cache['stationRoutes']        = $routes;
        $this->_cache['stationRouteChildren'] = $children;

        return $routes;
    }

    /**
     * 路線別駅を取得
     *
     * @return array
     */
    public function getStationRouteChildren()
    {
        if (isset($this->_cache['stationRouteChildren'])) {
            return $this->_cache['stationRouteChildren'];
        }

        $routes   = [];
        $children = [];
        foreach ($this->stations as $station) {
            $routes[$station->route_cd]                         = $station->route_name;
            $children[$station->route_cd][$station->station_no] = $station->station_name;
        }
        $this->_cache['stationRoutes']        = $routes;
        $this->_cache['stationRouteChildren'] = $children;

        return $children;
    }

    /**
     * 検索フリーワードを配列で取得
     *
     * @return array
     */
    public function getKeyWords()
    {
        return explode(' ', trim(mb_convert_kana($this->keyword, 's')));
    }

    /**
     * 都道府県Noから都道府県名を取得する (事前に stationParts の参照が必要)
     *
     * @param $id
     * @return null|string
     */
    public function getPrefNameById($id)
    {
        if (!isset($this->_cache['stationPrefNames'])) {
            return null;
        }

        return $this->_cache['stationPrefNames'][$id];
    }

    /**
     * 鉄道会社コードから鉄道会社名を取得する (事前に stationParts の参照が必要)
     *
     * @param $id
     * @return null|string
     */
    public function getRailroadNameById($id)
    {
        if (!isset($this->_cache['stationRailroadNames'])) {
            return null;
        }

        return $this->_cache['stationRailroadNames'][$id];
    }

    /**
     * 路線コードから路線名を取得する (事前に stationParts の参照が必要)
     *
     * @param $id
     * @return null|string
     */
    public function getRouteNameById($id)
    {
        if (!isset($this->_cache['stationRouteNames'])) {
            return null;
        }

        return $this->_cache['stationRouteNames'][$id];
    }

    /**
     * 検索結果一覧画面のタイトルとダイジェストを取得する
     * 検索した項目がカンマ区切りで入る。
     *
     * @param array $conditions
     * @return array
     */
    public function getSelectArray($conditions)
    {
        $list = [];
        if ($conditions && is_array($conditions)) {
            foreach ($conditions as $key => $val) {
                if (!JmUtils::isEmpty($val)) {
                    if ($key == 'keyword') {
                        $list['title'][]  = $val;
                        $list['digest'][] = $val;
                    } elseif ($key != 'area') {
                        foreach ($val as $val2) {
                            $rows                                         = (new Query())
                                ->select('*')
                                ->from($this->columnNameList[$key]['table'])
                                ->filterWhere([$this->columnNameList[$key]['id'] => $val2])
                                ->one();
                            $list[$this->columnNameList[$key]['table']][] = $rows[$this->columnNameList[$key]['column']];
                            $list['title'][]                              = $rows[$this->columnNameList[$key]['column']];
                            $list['digest'][]                             = $rows[$this->columnNameList[$key]['column']];
                        }
                    }
                }
            }
        }

        $list2 = [];

        /**
         * 検索項目が1個または2個の場合、選択された一覧画検索条件画面のタイトル（h1）に入る文字列がtitleに代入され
         * 3個以上の場合、ダイジェスト（h1タグの下に表示される）に入る文字列がdigestに代入される。
         */
        foreach ($list as $key => $val) {
            if (($key == 'title' && (count($list[$key]) == 1 || count($list[$key]) == 2))
                || $key == 'digest' && (count($list[$key]) > 2)
            ) {
                $list2[$key] = mb_substr(implode(Yii::t('app', '・'), $list[$key]), 0, 200);
            } elseif ($key != 'title' && !($key == 'digest' && (count($list[$key]) <= 2))) {
                $list2[$key] = mb_substr(implode(Yii::t('app', ' / '), $list[$key]), 0, 200);
            }
        }

        return $list2;
    }

    /**
     * スマホ用詳細検索画面用に、JobSearchFormのインスタンスの
     * プロパティに値をロードする。
     */
    public function setSearchParamForView()
    {
        // 都道府県による検索
        if ($this->pref || $this->pref_dist_master_parent) {
            $prefIds = [];

            if ($this->pref) {
                // 都道府県IDを取得
                $prefIds = array_merge(
                    $prefIds,
                    array_map(function (Pref $pref) {
                        return $pref->id;
                    }, Pref::find()->where([
                        'pref_no' => (array)$this->pref,
                    ])->all())
                );
            }

            // 親IDをセットする
            $this->pref_dist_master_parent = array_unique(array_merge(
                (array)$this->pref_dist_master_parent,
                array_map(function (PrefDistMaster $master) {
                    return $master->pref_dist_master_no;
                }, PrefDistMaster::find()->where([
                    'valid_chk' => self::FLAG_ON,
                    'pref_id'   => $prefIds,
                ])->all())
            ));

            // 市区町村Codeを取得して渡す
            $prefDistMasterIds      = PrefDistMaster::find()
                ->select(['id'])
                ->where(['pref_dist_master_no' => $this->pref_dist_master_parent])
                ->column();
            $this->pref_dist_master = array_unique(array_merge(
                (array)$this->pref_dist_master,
                array_map(function (Dist $dist) {
                    return $dist->dist_cd;
                }, Dist::find()->joinWith([
                    'prefDist',
                ])->where([
                    PrefDist::tableName() . '.pref_dist_master_id' => $prefDistMasterIds,
                ])->all())
            ));
        }

        // 路線検索
        if ($this->station_parent) {
            $this->station = array_merge(
                (array)$this->station,
                array_map(function (Station $station) {
                    return $station->station_no;
                }, Station::find()->where([
                    'route_cd' => $this->station_parent,
                ])->all())
            );
        }

        // 給与カテゴリ
        if ($this->wage_category_parent) {
            $wageCategoryIds     = WageCategory::find()
                ->select(['id'])
                ->where(['wage_category_no' => $this->wage_category_parent])
                ->column();
            $this->wage_category = array_unique(array_merge(
                (array)$this->wage_category,
                array_map(function (WageItem $item) {
                    return $item->wage_item_no;
                }, WageItem::find()->where([
                    'wage_category_id' => $wageCategoryIds,
                ])->all())
            ));
        }

        // 職種検索
        if ($this->job_type_category_first || $this->job_type_category_parent || $this->job_type_category) {
            // 職種カテゴリ
            if ($this->job_type_category_first) {
                /** @var JobTypeBig[] $jobBigTypes */
                $jobBigTypes = JobTypeBig::find()->joinWith([
                    'jobTypeCategory',
                ])->where([
                    JobTypeCategory::tableName() . '.valid_chk'            => self::FLAG_ON,
                    JobTypeBig::tableName() . '.valid_chk'                 => self::FLAG_ON,
                    JobTypeCategory::tableName() . '.job_type_category_cd' => $this->job_type_category_first,
                ])->all();

                // 職種大を取得
                $this->job_type_category_parent = array_merge(
                    (array)$this->job_type_category_parent,
                    array_map(function (JobTypeBig $jobTypeBig) {
                        return $jobTypeBig->job_type_big_no;
                    }, $jobBigTypes)
                );
            }
            // 大職種
            if ($this->job_type_category_parent) {
                /** @var JobTypeSmall[] $jobSmallTypes */
                $jobSmallTypes = JobTypeSmall::find()->joinWith([
                    'jobBigTypes',
                ])->where([
                    JobTypeSmall::tableName() . '.valid_chk'     => self::FLAG_ON,
                    JobTypeBig::tableName() . '.valid_chk'       => self::FLAG_ON,
                    JobTypeBig::tableName() . '.job_type_big_no' => $this->job_type_category_parent,
                ])->all();

                // 職種小を取得
                $this->job_type_category = array_merge(
                    (array)$this->job_type_category,
                    array_map(function (JobTypeSmall $jobType) {
                        return $jobType->job_type_small_no;
                    }, $jobSmallTypes)
                );
            }
        }

        // 自由検索2階層
        for ($i = 1; $i <= SearchCategory::CATEGORY_MAX; $i++) {
            $attributeName       = SearchCategory::TABLE_BASE . $i;
            $attributeNameParent = "{$attributeName}_parent";
            if ($this->{$attributeName} || $this->{$attributeNameParent}) {
                if ($this->{$attributeNameParent}) {
                    SearchCategory::setTableId($i);
                    $searchkeyCategoryIds = SearchCategory::find()
                        ->select(['id'])
                        ->where(['searchkey_category_no' => (array)$this->{$attributeNameParent}])
                        ->column();
                    SearchItem::setTableId($i);
                    $this->{$attributeName} = array_unique(array_merge(
                        $this->{$attributeNameParent},
                        array_map(function (SearchItem $item) {
                            return $item->searchkey_item_no;
                        }, SearchItem::find()->andWhere([
                            'valid_chk'             => self::FLAG_ON,
                            'searchkey_category_id' => $searchkeyCategoryIds,
                        ])->all())
                    ));
                }
            }
        }
    }

    /**
     * フリーワード検索キー以外の検索キーに一つでもパラメーターがセットされているか
     * todo スマホでもフリーワードクロス検索ができるようになったら必要なくなるはずです
     *
     * @return bool
     */
    public function hasDetailSearchParams()
    {
        // 汎用検索キーで1つでも入力があればtrue
        foreach (range(1, 10) as $i) {
            if ($this->{'searchkey_category' . $i}) {
                return true;
            }
        }
        foreach (range(11, 20) as $i) {
            if ($this->{'searchkey_item' . $i}) {
                return true;
            }
        }
        // 特殊検索キーで1つでも入力があればtrue
        return $this->pref_dist_master || $this->station || $this->job_type_category_first || $this->wage_category;
    }

    /**
     * スマホのtopと検索結果画面に表示する特別キーを取得する
     *
     * @return SearchkeyMaster
     */
    public function getPrincipalKey()
    {
        if (isset($this->_cache['principalKey'])) {
            return $this->_cache['principalKey'];
        }

        $principalKeyArray = array_filter($this->searchKeys, function (JobSearch $v) {
            return $v->principal_flg;
        });

        $this->_cache['principalKey'] = array_shift($principalKeyArray);

        return $this->_cache['principalKey'];
    }

    /**
     * 検索条件を下記の条件で生成する（本モデルにload可能）
     * [
     *     attribute名 => [
     *         検索キー項目名 => 検索キーナンバー,
     *         検索キー項目名 => 検索キーナンバー,
     *     ],
     *     attribute名 => [
     *         検索キー項目名 => 検索キーナンバー,
     *         検索キー項目名 => 検索キーナンバー,
     *     ],
     * ]
     * attribute　 ：searchkey_master.sort順
     * 検索キー項目：URLに書かれた順（input順なので普通に入力していたらsort順になる）
     *
     * ついでにattributeToCodeもキャッシュする
     *
     * @param $params
     * @return array
     */
    public function getConditionsFromParam($params)
    {
        $prefFinished = false;
        $results      = [];
        // エリア
        if (is_numeric($params['areaId'])) {
            $results['area'] = $params['areaId'];
        }

        // FW検索
        if (isset($params['keyword'])) {
            $results['keyword'] = rawurldecode($params['keyword']);
        }

        foreach ($this->searchKeys as $i => $key) {
            // 勤務地（キャッシュから取得）
            if (($key->isPref || $key->isPrefDist) && !$prefFinished) {
                if (empty($key->first_hierarchy_cd) && empty($key->second_hierarchy_cd)) {
                    continue;
                }
                // 都道府県
                $this->condition($key->first_hierarchy_cd, 'prefs.[number].pref_name', 'pref', $results, $params);
                // 地域名
                $this->condition($key->second_hierarchy_cd, 'prefDistricts.[number].pref_dist_name', 'pref_dist_master_parent', $results, $params);
                // 市区町村
                $this->condition($key->third_hierarchy_cd, 'districts.[number].dist_name', 'pref_dist_master', $results, $params);
                $prefFinished = true;
            }

            // 路線検索（キャッシュから取得）
            if ($key->isStation) {
                if (empty($key->first_hierarchy_cd) && empty($key->second_hierarchy_cd)) {
                    continue;
                }
                // 路線
                $this->condition($key->first_hierarchy_cd, 'stationRoutes.[number]', 'station_parent', $results, $params);
                // 駅(station_noが一意でないのでキャッシュ時点でindexすると具合が悪いためこうする)
                if ($key->second_hierarchy_cd && isset($params['conditions'][$key->second_hierarchy_cd])) {
                    $stations = ArrayHelper::index($this->stations, 'station_no');
                    foreach (array_unique($params['conditions'][$key->second_hierarchy_cd]) as $number) {
                        $name = ArrayHelper::getValue($stations, $number . '.station_name', null);
                        if ($name !== null) {
                            $results['station'][$name] = $number;
                        }
                    }
                    $this->attributeToCode['station'] = $key->second_hierarchy_cd;
                }
            }

            // 給与（キャッシュから取得）
            // todo キャッシュ不要？
            if ($key->isWage) {
                // 給与カテゴリ
                $this->condition($key->first_hierarchy_cd, 'wages.[number].wage_category_name', "{$key->table_name}_parent", $results, $params);
                // 給与
                $this->condition($key->second_hierarchy_cd, 'wageItems.[number].disp_price', "{$key->table_name}", $results, $params);
            }

            // 自由検索項目（キャッシュしていない）
            // todo 職種（優先キー）はキャッシュ必要？
            if ($key->isCategory) {
                if (empty($key->first_hierarchy_cd) && empty($key->second_hierarchy_cd)) {
                    continue;
                }
                if ($key->first_hierarchy_cd && isset($params['conditions'][$key->first_hierarchy_cd])) {
                    $categories = ArrayHelper::map(
                        $key->getCategoryQuery()
                        ->select(['searchkey_category_no', 'searchkey_category_name'])
                        ->andWhere(['searchkey_category_no' => $params['conditions'][$key->first_hierarchy_cd]])
                        ->asArray()
                        ->all(),
                        'searchkey_category_no',
                        'searchkey_category_name'
                    );
                    foreach ($params['conditions'][$key->first_hierarchy_cd] as $number) {
                        if (isset($categories[$number])) {
                            $results["{$key->table_name}_parent"][$categories[$number]] = $number;
                        }
                    }
                    $this->attributeToCode[$key->table_name . '_parent'] = $key->first_hierarchy_cd;
                }
                if ($key->second_hierarchy_cd && isset($params['conditions'][$key->second_hierarchy_cd])) {
                    SearchItem::setTableId($key->categoryId);
                    $items = ArrayHelper::map(
                        SearchItem::find()->where(['valid_chk' => JobSearch::FLAG_VALID])
                        ->orderBy(['sort' => SORT_ASC])
                        ->select(['searchkey_item_no', 'searchkey_item_name'])
                        ->andWhere(['searchkey_item_no' => $params['conditions'][$key->second_hierarchy_cd]])
                        ->asArray()
                        ->all(),
                        'searchkey_item_no',
                        'searchkey_item_name'
                    );
                    foreach ($params['conditions'][$key->second_hierarchy_cd] as $number) {
                        if (isset($items[$number])) {
                            $results[$key->table_name][$items[$number]] = $number;
                        }

                    }
                    $this->attributeToCode[$key->table_name] = $key->second_hierarchy_cd;
                }
            }
            if ($key->isItem) {
                if (empty($key->first_hierarchy_cd)) {
                    continue;
                }
                if (isset($params['conditions'][$key->first_hierarchy_cd])) {
                    $items = ArrayHelper::map(
                        $key->getItemQuery()
                        ->select(['searchkey_item_no', 'searchkey_item_name'])
                        ->andWhere(['searchkey_item_no' => $params['conditions'][$key->first_hierarchy_cd]])
                        ->asArray()
                        ->all(),
                        'searchkey_item_no',
                        'searchkey_item_name'
                    );
                    foreach ($params['conditions'][$key->first_hierarchy_cd] as $number) {
                        if (isset($items[$number])) {
                            $results[$key->table_name][$items[$number]] = $number;
                        }
                    }
                    $this->attributeToCode[$key->table_name] = $key->first_hierarchy_cd;
                }
            }
        }

        return $results;
    }

    /**
     * 検索条件を追加する
     *
     * @param string $code      検索コード
     * @param string $key       ArrayHelperに渡されるkey.[number]が検索キーnoに置換される
     * @param string $attribute JobSearchFormのattribute名
     * @param array  $results   検索条件が追加される配列
     */
    private function condition($code, $key, $attribute, &$results, $params)
    {
        if ($code && isset($params['conditions'][$code])) {
            foreach (array_unique($params['conditions'][$code]) as $number) {
                $name = ArrayHelper::getValue($this, str_replace('[number]', $number, $key), null);
                if ($name !== null) {
                    $results[$attribute][$name] = $number;
                }
            }
            $this->attributeToCode[$attribute] = $code;
        }
    }

    /**
     * 検索のクエリを作成する
     *
     * @return ActiveQuery
     */
    private function generateSearchQuery()
    {
        $grouping = false;

        // 有効なもののみに絞る
        $query = JobMasterDisp::find()->active()->distinct();

        // エリアによる検索
        if ($this->area) {
            $query->joinWith(['jobPref'])->andWhere([
                JobPref::tableName() . '.pref_id' =>
                    array_map(function (Pref $pref) {
                        return $pref->id;
                    }, Pref::find()->where([
                        'area_id' => $this->area,
                    ])->all()),
            ]);
        }

        // 都道府県による検索
        if ($this->pref || $this->pref_dist_master_parent) {
            $prefIds = [];

            if ($this->pref) {
                // 都道府県IDを取得
                $prefIds = array_merge(
                    $prefIds,
                    array_map(function (Pref $pref) {
                        return $pref->id;
                    }, Pref::find()->where([
                        'pref_no' => (array)$this->pref,
                    ])->all())
                );
            }

            // 親IDをセットする
            $this->pref_dist_master_parent = array_unique(array_merge(
                (array)$this->pref_dist_master_parent,
                array_map(function (PrefDistMaster $master) {
                    return $master->pref_dist_master_no;
                }, PrefDistMaster::find()->where([
                    'valid_chk' => self::FLAG_ON,
                    'pref_id'   => $prefIds,
                ])->all())
            ));

            // 市区町村Codeを取得して渡す
            $prefDistMasterIds      = PrefDistMaster::find()
                ->select(['id'])
                ->where(['pref_dist_master_no' => $this->pref_dist_master_parent])
                ->column();
            $this->pref_dist_master = array_unique(array_merge(
                (array)$this->pref_dist_master,
                array_map(function (Dist $dist) {
                    return $dist->dist_cd;
                }, Dist::find()->joinWith([
                    'prefDist',
                ])->where([
                    PrefDist::tableName() . '.pref_dist_master_id' => $prefDistMasterIds,
                ])->all())
            ));
        }

        // 地域検索
        if ($this->pref_dist_master) {
            $query->joinWith(['jobDist'])->andWhere([
                JobDist::tableName() . '.dist_id' => array_map(function (Dist $dist) {
                    return $dist->id;
                }, Dist::find()->where([
                    'dist_cd' => (array)$this->pref_dist_master,
                ])->all()),
            ]);
        }

        // 路線検索
        if ($this->station_parent) {
            $this->station = array_merge(
                (array)$this->station,
                array_map(function (Station $station) {
                    return $station->station_no;
                }, Station::find()->where([
                    'route_cd' => $this->station_parent,
                ])->all())
            );
        }

        // 駅検索
        if ($this->station) {
            $query->joinWith(['jobStation'])->andWhere([
                JobStationInfo::tableName() . '.station_id' => (array)$this->station,
            ]);
        }

        // 給与カテゴリ
        if ($this->wage_category_parent) {
            $wageCategoryIds     = WageCategory::find()
                ->select(['id'])
                ->where(['wage_category_no' => $this->wage_category_parent])
                ->column();
            $this->wage_category = array_unique(array_merge(
                (array)$this->wage_category,
                array_map(function (WageItem $item) {
                    return $item->wage_item_no;
                }, WageItem::find()->where([
                    'wage_category_id' => $wageCategoryIds,
                ])->all())
            ));
        }

        // 給与検索
        //wage_category_idとwage_item_nameを取得
        if ($this->wage_category) {
            $wageItems = WageItem::find()
                ->select(['wage_category_id', 'wage_item_name'])
                ->where(['wage_item_no' => $this->wage_category])
                ->asArray()->all();
            //取得したwage_category_idが一致しているかつ取得値以上のwage_item_nameに当てはまるidを取得
            $wageQuery = WageItem::find()->select(['id']);
            foreach ($wageItems as $wageItem) {
                $wageQuery->orWhere([
                    'and',
                    ['wage_category_id' => $wageItem['wage_category_id']],
                    ['>=','wage_item_name', $wageItem['wage_item_name']],
                ]);
            }
            $wageItemIds = $wageQuery->column();
            $query->joinWith(['jobWage'])->andWhere([
                JobWage::tableName() . '.wage_item_id' => $wageItemIds,
            ]);
        }

        // 職種検索
        if ($this->job_type_category_first || $this->job_type_category_parent || $this->job_type_category) {
            // 職種カテゴリ
            if ($this->job_type_category_first) {
                /** @var JobTypeBig[] $jobBigTypes */
                $jobBigTypes = JobTypeBig::find()->joinWith([
                    'jobTypeCategory',
                ])->where([
                    JobTypeCategory::tableName() . '.valid_chk'            => self::FLAG_ON,
                    JobTypeBig::tableName() . '.valid_chk'                 => self::FLAG_ON,
                    JobTypeCategory::tableName() . '.job_type_category_cd' => $this->job_type_category_first,
                ])->all();

                // 職種大を取得
                $this->job_type_category_parent = array_merge(
                    (array)$this->job_type_category_parent,
                    array_map(function (JobTypeBig $jobTypeBig) {
                        return $jobTypeBig->job_type_big_no;
                    }, $jobBigTypes)
                );
            }
            // 大職種
            if ($this->job_type_category_parent) {
                /** @var JobTypeSmall[] $jobSmallTypes */
                $jobSmallTypes = JobTypeSmall::find()->joinWith([
                    'jobBigTypes',
                ])->where([
                    JobTypeSmall::tableName() . '.valid_chk'     => self::FLAG_ON,
                    JobTypeBig::tableName() . '.valid_chk'       => self::FLAG_ON,
                    JobTypeBig::tableName() . '.job_type_big_no' => $this->job_type_category_parent,
                ])->all();

                // 職種小を取得
                $this->job_type_category = array_merge(
                    (array)$this->job_type_category,
                    array_map(function (JobTypeSmall $jobType) {
                        return $jobType->job_type_small_no;
                    }, $jobSmallTypes)
                );
            }
            // 少職種
            if ($this->job_type_category) {
                /** @var JobTypeSmall[] $jobSmallTypes */
                $jobSmallTypes = JobTypeSmall::find()->joinWith([
                    'jobBigTypes',
                ])->where([
                    JobTypeSmall::tableName() . '.valid_chk'         => self::FLAG_ON,
                    JobTypeBig::tableName() . '.valid_chk'           => self::FLAG_ON,
                    JobTypeSmall::tableName() . '.job_type_small_no' => $this->job_type_category,
                ])->all();

                $query->joinWith(['jobType'])->andWhere([
                    JobType::tableName() . '.job_type_small_id' => array_map(function (JobTypeSmall $jobTypeSmall) {
                        return $jobTypeSmall->id;
                    }, $jobSmallTypes),
                ]);
            }
        }

        // キーワード検索
        if ($this->keyword) {
            // スペース区切りでAND検索
            $keywords      = $this->keywords;
            $searchColumns = array_map(function (JobColumnSet $columnSet) {
                return $columnSet->column_name;
            }, JobColumnSet::find()->where([
                'valid_chk'           => self::FLAG_ON,
                'freeword_search_flg' => self::FLAG_ON,
            ])->all());

            foreach ($keywords as $keyword) {
                $query->andFilterWhere(array_merge([
                    'or',
                ], array_map(function ($column) use ($keyword) {
                    return ['like', JobMaster::tableName() . ".{$column}", $keyword];
                }, $searchColumns)));
            }
        }

        // 自由検索2階層
        for ($i = 1; $i <= SearchCategory::CATEGORY_MAX; $i++) {
            $attributeName       = SearchCategory::TABLE_BASE . $i;
            $attributeNameParent = "{$attributeName}_parent";
            if ($this->{$attributeName} || $this->{$attributeNameParent}) {
                if ($this->{$attributeNameParent}) {
                    SearchCategory::setTableId($i);
                    $searchkeyCategoryIds = SearchCategory::find()
                        ->select(['id'])
                        ->where(['searchkey_category_no' => (array)$this->{$attributeNameParent}])
                        ->column();
                    SearchItem::setTableId($i);
                    $this->{$attributeName} = array_unique(array_merge(
                        (array)$this->{$attributeName},
                        array_map(function (SearchItem $item) {
                            return $item->searchkey_item_no;
                        }, SearchItem::find()->andWhere([
                            'valid_chk'             => self::FLAG_ON,
                            'searchkey_category_id' => $searchkeyCategoryIds,
                        ])->all())
                    ));
                }
                if ($this->{$attributeName}) {
                    $itemTableName = self::JOB_ITEM_TABLE_BASE . $i;
                    SearchItem::setTableId($i);
                    $searchkeyItemIds = SearchItem::find()
                        ->select(['id'])
                        ->where(['searchkey_item_no' => array_unique((array)$this->{$attributeName})])
                        ->column();
                    $query->joinWith(self::JOB_ITEM_MODEL_BASE . $i)
                        ->andWhere([
                            $itemTableName . '.searchkey_item_id' => $searchkeyItemIds,
                        ]);

                    // AND検索
                    if ($this->getSearchKeyByName($attributeName)->is_and_search == self::FLAG_ON) {
                        $grouping = true;
                        $query->addSelect([
                            "COUNT(DISTINCT {$itemTableName}.id) as {$itemTableName}_cnt",
                        ])->andHaving([
                            "{$itemTableName}_cnt" => count((array)$this->{$attributeName}),
                        ]);
                        // AND条件の検索キーが複数回検索されたときに、複数回'jobMaster.*'がselectに追加されるのを防いでいる
                        $jmSelect = JobMaster::tableName() . '.*';
                        if (!array_key_exists($jmSelect, array_flip($query->select))) {
                            $query->addSelect([$jmSelect]);
                        }
                    }
                }
            }
        }

        // 自由検索1階層
        for ($i = SearchCategory::CATEGORY_MAX + 1; $i <= SearchItem::ITEM_MAX; $i++) {
            $attributeName = SearchItem::TABLE_BASE . $i;
            $itemTableName = self::JOB_ITEM_TABLE_BASE . $i;
            SearchItem::setTableId($i);
            if ($this->{$attributeName}) {
                $searchkeyItemIds = SearchItem::find()
                    ->select(['id'])
                    ->where(['searchkey_item_no' => (array)$this->{$attributeName}])
                    ->column();
                $query->joinWith(self::JOB_ITEM_MODEL_BASE . $i)
                    ->andWhere([$itemTableName . '.searchkey_item_id' => $searchkeyItemIds]);
                // AND検索
                if ($this->getSearchKeyByName($attributeName)->is_and_search == self::FLAG_ON) {
                    $grouping = true;
                    $query->addSelect([
                        "COUNT(DISTINCT {$itemTableName}.id) as {$itemTableName}_cnt",
                    ])->andHaving([
                        "{$itemTableName}_cnt" => count((array)$this->{$attributeName}),
                    ]);
                    // AND条件の検索キーが複数回検索されたときに、複数回'jobMaster.*'がselectに追加されるのを防いでいる
                    $jmSelect = JobMaster::tableName() . '.*';
                    if (!array_key_exists($jmSelect, array_flip($query->select))) {
                        $query->addSelect([$jmSelect]);
                    }
                }
            }
        }

        //注目情報（hot-job）のdisp_type_sortで検索
        if ($this->disp_type_sort) {
            $query->andWhere([
                'in',
                'disp_type_sort',
                $this->disp_type_sort
            ]);
        }

        if ($grouping) {
            $query->groupBy([JobMasterDisp::tableName() . '.id']);
        }

        return $query;
    }

    /**
     * 駅検索キーが有効かどうか
     *
     * @return bool
     */
    public function hasStationKey()
    {
        $result = false;
        foreach ($this->searchKeys as $key) {
            if ($key->table_name == 'station') {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * 勤務地検索キーが有効かどうか
     *
     * @return bool
     */
    public function hasPrefKey()
    {
        $result = false;
        foreach ($this->searchKeys as $key) {
            if ($key->table_name == 'pref') {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * top画面の時、エリアを元にインスタンスを初期化する
     * @param Area $area
     */
    public function initTopScenario($area)
    {
        if ($area->id === 0) {
            $this->scenario = JobSearchForm::SCENARIO_TOP;
        } else {
            $this->scenario = JobSearchForm::SCENARIO_AREA_TOP;
            $this->area = $area->id;
        }
    }
}
