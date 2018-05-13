<?php

namespace app\models\manage;

use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\JobType;
use app\models\manage\searchkey\JobTypeBig;
use app\models\manage\searchkey\JobTypeCategory;
use app\models\manage\searchkey\JobTypeSmall;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\SearchkeyCategory;
use app\models\manage\searchkey\Station;
use app\models\manage\searchkey\SearchkeyItem;
use app\models\manage\searchkey\WageCategory;
use app\models\manage\searchkey\WageItem;
use app\modules\manage\models\JobCsvRegister;
use proseeds\models\BaseModel;
use proseeds\models\Tenant;
use yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\BadRequestHttpException;
use app\modules\manage\controllers\secure\CsvHelperController;
use app\common\Helper\JmUtils;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "searchkey_master".
 *
 * @property integer                                                                               $id
 * @property integer                                                                               $tenant_id
 * @property integer                                                                               $searchkey_no
 * @property string                                                                                $table_name
 * @property string                                                                                $searchkey_name
 * @property integer                                                                               $first_hierarchy_cd
 * @property integer                                                                               $second_hierarchy_cd
 * @property integer                                                                               $third_hierarchy_cd
 * @property integer                                                                               $is_category_label
 * @property integer                                                                               $is_and_search
 * @property integer                                                                               $sort
 * @property integer                                                                               $search_input_tool
 * @property integer                                                                               $is_more_search
 * @property integer                                                                               $is_on_top
 * @property integer                                                                               $valid_chk
 * @property string                                                                                $job_relation_table
 * @property boolean                                                                               $icon_flg
 * @property boolean                                                                               $principal_flg
 *
 * @property Pref[]|JobTypeCategory[]|WageCategory[]|Station[]|SearchkeyCategory[]|SearchkeyItem[] $searchKeyModels
 * @property string                                                                                $modelName
 * @property string                                                                                $modelFullName
 * @property string                                                                                $jobRelationModelAttribute
 * @property string                                                                                $jobRelationModelName
 * @property string                                                                                $jobRelationName
 * @property string                                                                                $withString
 * @property ActiveRecord[]                                                                        $itemModels
 * @property int[]                                                                                 $itemNos
 * @property string                                                                                $columnNameOfItemNo
 * @property array                                                                                 $seachkeyCsvAttributes
 * @property string                                                                                $searchkeyItemModelFullName
 */
class SearchkeyMaster extends BaseModel
{
    /** 状態 - 有効or無効 */
    const FLAG_VALID   = 1;
    const FLAG_INVALID = 0;

    /** 検索キーのmodelのあるディレクトリのpath */
    const MODEL_BASE_PATH = 'app\models\manage\searchkey\\';

    /** 表示形式 - モーダルorチェックボックスorドロップダウン */
    const SEARCH_INPUT_TOOL_MODAL    = 1;
    const SEARCH_INPUT_TOOL_CHECKBOX = 2;
    const SEARCH_INPUT_TOOL_DROPDOWN = 3;

    /** カテゴリ選択 - 可or不可 */
    const CATEGORY_UNSELECTABLE = 1;
    const CATEGORY_SELECTABLE   = 0;

    /** 検索方式 - AND or OR */
    const IS_SEARCH_AND = 1;
    const IS_SEARCH_OR  = 0;

    /** 表示条件 - 最初から表示 or ボタンを押すと表示 */
    const DISPLAY_FIRST             = 0;
    const DISPLAY_WHEN_PRESS_BUTTON = 1;

    /** 表示ヵ所 - 検索一覧にのみ表示 or トップにも表示 */
    const DISPLAY_IN_SEARCH_ONLY = 0;
    const DISPLAY_IN_TOP_PAGE    = 1;

    /** アイコン表示 - 表示する or 表示しない */
    const ICON_FLG_VALID   = 1;
    const ICON_FLG_INVALID = 0;

    /** is_and_search, search_input_tool, is_category_labelが変更不可能な検索キー */
    const STATIC_KEYS = ['pref', 'station', 'wage_category', 'job_type_category'];

    /** icon_flgが変更不可能な検索キー */
    const ICON_STATIC_KEYS = ['pref', 'station', 'wage_category'];

    /** フリーワード検索で利用している検索コード */
    const FREE_WORD_PREFIX = 'FW';

    /** @var ActiveRecord[] 各検索キーのモデル */
    private $_searchKeyModels;

    /** @var ActiveRecord[] 各検索キーアイテムのモデル */
    private $_itemModels;

    /** @var array 各検索キーアイテムのNo */
    private $_itemNos = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'searchkey_master';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'searchkey_no', 'search_input_tool', 'principal_flg'], 'integer',],
            [
                [
                    'valid_chk',
                    'icon_flg',
                    'is_category_label',
                    'is_on_top',
                    'is_and_search',
                ],
                'boolean',
            ],
            [['table_name', 'searchkey_name'], 'string', 'max' => 50],
            [['first_hierarchy_cd', 'second_hierarchy_cd', 'third_hierarchy_cd'], 'string', 'max' => 10],
            [['searchkey_name', 'valid_chk', 'is_on_top', 'sort'], 'required'],
            ['sort', 'integer', 'min' => 1, 'max' => 99],
            [
                ['first_hierarchy_cd', 'second_hierarchy_cd', 'third_hierarchy_cd'],
                function ($attribute, $params) {
                    // "FW"は求職者原稿検索結果画面のフリーワード検索で使用しているため
                    if ($this->$attribute == self::FREE_WORD_PREFIX) {
                        $this->addError($attribute, Yii::t('app', '"' . self::FREE_WORD_PREFIX . '"以外の文字列を使用してください。'));
                    }
                },
            ],
            [
                ['is_and_search', 'search_input_tool'],
                'required',
                'when' => function ($model, $attribute) {
                    return !ArrayHelper::isIn($model->table_name, self::STATIC_KEYS);
                },
            ],
            [
                'is_category_label',
                'required',
                'when' => function ($model, $attribute) {
                    return (!ArrayHelper::isIn($model->table_name, self::STATIC_KEYS) && $model->second_hierarchy_cd !== null);
                },
            ],
            [
                'icon_flg',
                'required',
                'when' => function ($model, $attribute) {
                    return !ArrayHelper::isIn($model->table_name, self::ICON_STATIC_KEYS);
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                  => Yii::t('app', '主キー'),
            'tenant_id'           => Yii::t('app', 'テナントID'),
            'searchkey_no'        => Yii::t('app', '表示用主キー'),
            'table_name'          => Yii::t('app', 'テーブル名'),
            'searchkey_name'      => Yii::t('app', '検索キー名'),
            'first_hierarchy_cd'  => Yii::t('app', '第一階層URLコード'),
            'second_hierarchy_cd' => Yii::t('app', '第二階層URLコード'),
            'third_hierarchy_cd'  => Yii::t('app', '第三階層URLコード'),
            'sort'                => Yii::t('app', '表示順'),
            'is_on_top'           => Yii::t('app', '表示場所（PCのみ）'),
            'is_and_search'       => Yii::t('app', '検索条件'),
            'search_input_tool'   => Yii::t('app', '入力方法（PCのみ）'),
            'is_category_label'   => Yii::t('app', 'カテゴリ選択（PCのみ）'),
            'valid_chk'           => Yii::t('app', '公開状況'),
            'job_relation_table'  => Yii::t('app', 'job_masterとの中間テーブル'),
            'icon_flg'            => Yii::t('app', '求人詳細画面アイコン表示'),
            'principal_flg'       => Yii::t('app', '優先キーフラグ'),
        ];
    }

    /**
     * 初期化
     */
    public function init()
    {
        $this->on(self::EVENT_AFTER_UPDATE, function () {
            $this->saveRouteSetting();
        });
        $this->on(self::EVENT_AFTER_INSERT, function () {
            $this->saveRouteSetting();
        });
        $this->on(self::EVENT_AFTER_DELETE, function () {
            $this->saveRouteSetting();
        });

        parent::init();
    }

    /**
     * @param $tableName
     * @return array|null|self
     * @internal param int $id
     */
    public static function findName($tableName)
    {
        return self::find()->where(['table_name' => $tableName])->one();
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getSearchKeyModels()
    {
        if (!$this->_searchKeyModels) {
            /** @var ActiveRecord $modelName */
            $modelName = $this->modelFullName;
            /** @var ActiveQuery $query */
            if ($this->table_name == 'station' || $this->table_name == 'pref') {
                $query = $modelName::find();
            } else {
                $query = $modelName::find()->where([$modelName::tableName() . '.valid_chk' => self::FLAG_VALID]);
            }
            $this->setQueryParam($query);
            $this->_searchKeyModels = $query->all();
        }
        return $this->_searchKeyModels;
    }

    /**
     * @return string
     */
    public function getModelFullName()
    {
        return self::MODEL_BASE_PATH . $this->modelName;
    }

    /**
     * @return string
     */
    public function getModelName()
    {
        return Inflector::camelize($this->table_name);
    }

    /**
     * 有効な検索キーを返す
     *
     * @return SearchkeyMaster[]
     */
    public static function findSearchKeys()
    {
        return ArrayHelper::index(self::find()->where([
            'and',
            ['valid_chk' => self::FLAG_VALID],
            ['not', ['job_relation_table' => null]],
        ])->orderBy(['sort' => SORT_ASC])->all(), 'table_name');
    }

    /**
     * JobMasterの検索キーの○○Modelを返す
     *
     * @return string
     */
    public function getJobRelationModelAttribute()
    {
        return Inflector::variablize($this->job_relation_table) . 'Model';
    }

    /**
     * JobMasterの検索キーrelation名を返す
     *
     * @return string
     */
    public function getJobRelationName()
    {
        if ($this->job_relation_table == 'job_station_info') {
            return 'jobStation';
        }
        return Inflector::variablize($this->job_relation_table);
    }

    /**
     * job_masterとリレーションしているテーブル名と、小項目のテーブル名とを.でつなげて出力する
     *
     * @return string
     */
    public function getWithString()
    {
        switch ($this->job_relation_table) {
            case 'job_dist';
                return $this->jobRelationName . '.dist';
                break;
            case 'job_station_info';
                return $this->jobRelationName . '.station';
                break;
            case 'job_wage';
                return $this->jobRelationName . '.wageItem';
                break;
            // 職種検索キーは全サイトで無効になるがソースは残す。
            case 'job_type';
                return $this->jobRelationName . '.jobTypeSmall';
                break;
            default:
                return $this->jobRelationName . '.searchKeyItem';
                break;
        }
    }

    /**
     * 中間テーブルの名前を返す
     *
     * @return string
     */
    public function getJobRelationModelName()
    {
        return self::MODEL_BASE_PATH . Inflector::camelize($this->job_relation_table);
    }

    /**
     * @param ActiveQuery $query
     */
    protected function setQueryParam($query)
    {
        switch ($this->table_name) {
            case 'pref':
                $query->select([
                    Pref::tableName() . '.id',
                    Pref::tableName() . '.pref_no',
                    Pref::tableName() . '.pref_name',
                ])->with('distLite')->joinWith('area')->where([Area::tableName() . '.valid_chk' => self::FLAG_VALID]);
                break;
            case 'wage_category':
                $query->innerJoinWith([
                    'wageItem' => function (ActiveQuery $q) {
                        $q->where([WageItem::tableName() . '.valid_chk' => self::FLAG_VALID]);
                    },
                ])->orderBy(WageCategory::tableName() . '.sort');
                break;
            // 職種検索キーは全サイトで無効になるがソースは残す。
            case 'job_type_category':
                $query->innerJoinWith([
                    'jobTypeBig' => function (ActiveQuery $q) {
                        $q->where([JobTypeBig::tableName() . '.valid_chk' => self::FLAG_VALID,])
                            ->innerJoinWith([
                                'jobTypeSmall' => function (ActiveQuery $q) {
                                    $q->where([JobTypeSmall::tableName() . '.valid_chk' => self::FLAG_VALID]);
                                },
                            ]);
                    },
                ]);
                break;
            default:
                if (strpos($this->table_name, 'searchkey_category') !== false) {
                    /** @var SearchkeyItem $itemModel */
                    $itemModel = self::MODEL_BASE_PATH . str_replace('Category', 'Item', $this->modelName);
                    $query->innerJoinWith([
                        'items' => function (ActiveQuery $q) use ($itemModel) {
                            $q->where([$itemModel::tableName() . '.valid_chk' => self::FLAG_VALID])->orderBy('sort');
                        },
                    ]);
                }
                break;
        }
    }

    /**
     * 有効なitemのモデルのインスタンスの配列を返す
     * todo: switch祭りが嫌なので各検索キーのmodelに処理の一部を移動
     *
     * @return Pref[]|WageItem[]|JobTypeSmall[]|Station[]|SearchkeyItem[]
     */
    public function getItemModels()
    {
        if (!$this->_itemModels) {
            switch ($this->table_name) {
                case 'pref':
                    $this->_itemModels = $this->children($this->searchKeyModels, 'distLite');
                    break;
                case 'wage_category':
                    $this->_itemModels = $this->children($this->searchKeyModels, 'wageItem');
                    break;
                // 職種検索キーは全サイトで無効になるがソースは残す。
                case 'job_type_category':
                    /** @var JobTypeBig[] $jobTypeBig */
                    $jobTypeBig        = $this->children($this->searchKeyModels, 'jobTypeBig');
                    $this->_itemModels = $this->children($jobTypeBig, 'jobTypeSmall');
                    break;
                case 'station':
                    $this->_itemModels = $this->searchKeyModels;
                    break;
                default:
                    if (strpos($this->table_name, 'searchkey_category') !== false) {
                        $this->_itemModels = $this->children($this->searchKeyModels, 'items');
                    } else {
                        $this->_itemModels = $this->searchKeyModels;
                    }
                    break;
            }
        }
        return $this->_itemModels;
    }

    /**
     * 親の配列を元に全ての子供を返す
     * todo: utilにstaticなメソッドとして用意した方がいいかも
     *
     * @param $parents      Pref[]|JobTypeCategory[]|WageCategory[]|Station[]|SearchkeyCategory[]
     * @param $relationName string
     * @return array
     */
    private function children($parents, $relationName)
    {
        $items = [];
        foreach ($parents as $model) {
            $items = array_merge($items, $model->$relationName);
        }
        return $items;
    }

    /**
     * itemのnoやcdのカラム名を返す
     * todo: switch祭りが嫌なので各検索キーのmodelに処理を移動
     *
     * @return string
     */
    public function getColumnNameOfItemNo()
    {
        switch ($this->table_name) {
            case 'pref':
                return 'dist_cd';
                break;
            case 'wage_category':
                return 'wage_item_name';
                break;
            // 職種検索キーは全サイトで無効になるがソースは残す。
            case 'job_type_category':
                return 'job_type_small_no';
                break;
            case 'station':
                return 'station_no';
                break;
            default:
                return 'searchkey_item_no';
                break;
        }
    }

    /**
     * 有効なitemのnoやcdのkeyを持つ配列を返す
     *
     * @return array
     */
    public function getItemNos()
    {
        if ($this->_itemNos === null) {
            $array = [];
            $i     = 0;
            foreach ($this->itemModels as $itemModel) {
                if ($this->table_name === 'station') {
                    $array[$itemModel->{$this->columnNameOfItemNo}] = $i;
                } else {
                    $array[$itemModel->{$this->columnNameOfItemNo}] = $itemModel->id;
                }
                $i++;
            }
            $this->_itemNos = $array;
        }
        return $this->_itemNos;
    }

    /**
     * テナント独自のRoutingを保存する
     *
     * @return bool
     * @throws BadRequestHttpException
     */
    public static function saveRouteSetting()
    {
        $regex = '[\d,]+';

        $searchKeys = self::find()
            ->where([
                'valid_chk' => self::FLAG_VALID,
            ])->all();

        /** @var Tenant $tenant */
        $tenant = Tenant::findOne([
            'tenant_id' => Yii::$app->tenant->id,
        ]);

        $jobDir = $tenant->getAttribute('kyujin_detail_dir');

        $areaRules = ArrayHelper::map(
            Area::find()->where([
                'valid_chk' => self::FLAG_VALID,
            ])->all(),
            'area_dir',
            'id'
        );

        $firstRules = array_map(function (SearchkeyMaster $master) use ($regex) {
            return $master->first_hierarchy_cd;
        }, array_filter($searchKeys, function (SearchkeyMaster $master) {
            return !empty($master->first_hierarchy_cd);
        }));

        $secondRules = array_map(function (SearchkeyMaster $master) use ($regex) {
            return $master->second_hierarchy_cd;
        }, array_filter($searchKeys, function (SearchkeyMaster $master) {
            return !empty($master->second_hierarchy_cd);
        }));

        $thirdRules = array_map(function (SearchkeyMaster $master) use ($regex) {
            return $master->third_hierarchy_cd;
        }, array_filter($searchKeys, function (SearchkeyMaster $master) {
            return !empty($master->third_hierarchy_cd);
        }));

        $rules = [
            'job'        => $jobDir,
            'areas'      => $areaRules,
            'conditions' => array_values(array_unique(array_merge(
                $firstRules,
                $secondRules,
                $thirdRules
            ))),
        ];

        $saveDir = Yii::getAlias('@runtime') . '/setting';
        if (!file_exists($saveDir)) {
            if (!mkdir($saveDir)) {
                throw new BadRequestHttpException('URL設定ディレクトリの作成に失敗しました');
            }
        }

        if (!file_put_contents($saveDir . '/' . $tenant->tenant_code . '.json', json_encode($rules))) {
            throw new BadRequestHttpException('URL設定ファイルの保存に失敗しました');
        }

        return true;
    }

    /**
     * 初期設定一覧ページに表示するデータを取得する
     *
     * @return \app\models\manage\SearchkeyMaster[]
     */
    public function getSettingMenus()
    {
        $excludeTables = $this->getExcludeTableNames();

        return $this->find()->where([
            'and',
            ['not in', 'table_name', $excludeTables],
        ])->orderBy([
            'sort' => SORT_ASC,
        ])->all();
    }

    /**
     * 初期設定一覧ページで表示しないtable_name取得する
     *
     * @return string[]
     */
    private function getExcludeTableNames()
    {
        return [
            Pref::tableName(),
            Station::tableName(),
            JobTypeCategory::tableName(),
        ];
    }

    /**
     * 求人原稿CSV一括登録用CSV入力規則の説明をArrayで返す
     *
     * @return array
     */
    public static function csvDescription()
    {
        $searchKeysDispColumnRules = [];
        $description               = '{LABEL}の{KEY_NAME}を入力してください。複数入力される場合は"|"(パイプ)で区切って入力してください（例：1002|1003）。{KEY_NAME}の一覧は{LINK}を参照してください。';
        $url                       = 'secure/csv-helper/job';
        /** @var SearchkeyMaster $searchkeyMaster */
        foreach (Yii::$app->searchKey->searchkeys as $searchkeyMaster) {
            switch ($searchkeyMaster->table_name) {
                case 'station':
                    for ($i = 1; $i <= 3; $i++) {
                        $jobCsvRegister              = new JobCsvRegister();//やむを得ず
                        $searchKeysDispColumnRules[] = [
                            $jobCsvRegister->getAttributeLabel('stationCd' . $i),
                            JmUtils::rulesText(
                                $jobCsvRegister->getAttributeLabel('stationCd' . $i),
                                [$url, 'helperType' => CsvHelperController::STATION],
                                Yii::t(
                                    'app',
                                    '{NAME}の検索キーコードを入力してください。複数入力される場合は"|"(パイプ)で区切って入力してください（例：1002|1003）。検索キーコードの一覧は{LINK}を参照してください。',
                                    ['NAME' => $searchkeyMaster->searchkey_name]
                                ),
                                $searchkeyMaster->table_name
                            ),
                        ];
                        $searchKeysDispColumnRules[] = [
                            $jobCsvRegister->getAttributeLabel('transportType' . $i),
                            Yii::t('app', '駅からの交通手段を入力してください。徒歩の場合は0、車・バスの場合は1を入力してください。'),
                        ];
                        $searchKeysDispColumnRules[] = [
                            $jobCsvRegister->getAttributeLabel('transportTime' . $i),
                            Yii::t('app', '駅からの移動の時間を分単位で入力してください。'),
                        ];
                    }
                    break;
                case 'wage_category':
                    $description = '{LABEL}の{KEY_NAME}を入力してください。{LABEL}カテゴリの中から、1つを選択してください。{KEY_NAME}の一覧は{LINK}を参照してください。';
                    /** @var SearchkeyMaster $searchkeyMaster */
                    $searchkeyMaster = ArrayHelper::getValue(Yii::$app->searchKey->searchkeys, 'wage_category');
                    $cates           = $searchkeyMaster->searchKeyModels;
                    if (!$cates) {
                        break;
                    }
                    /** @var WageCategory $cate */
                    foreach ($cates as $cate) {
                        $searchKeysDispColumnRules[] = [
                            Yii::t('app', '{categoryName}(金額)', ['categoryName' => $cate->wage_category_name]),
                            JmUtils::rulesText(
                                $cate->wage_category_name,
                                [$url, 'helperType' => CsvHelperController::WAGE],
                                $description,
                                $searchkeyMaster->table_name,
                                Yii::t('app', '金額')
                            ),
                        ];
                    }
                    break;
                case 'area':
                case 'pref_dist_master':
                    break;
                case 'pref':
                    //勤務地のみ必須項目であるため、文字列を追加して対応している。
                    $prefDescription             = $description . Yii::t('app', '{LABEL}は必須項目です。', ['LABEL' => $searchkeyMaster->searchkey_name]);
                    $searchKeysDispColumnRules[] = [
                        $searchkeyMaster->searchkey_name . Yii::t('app', '(必須)'),
                        JmUtils::rulesText(
                            $searchkeyMaster->searchkey_name,
                            [$url, 'helperType' => CsvHelperController::DIST],
                            $prefDescription,
                            $searchkeyMaster->table_name
                        ),
                    ];
                    break;
                // 職種検索キーは全サイトで無効になるがソースは残す。
                case 'job_type_small':
                    $searchKeysDispColumnRules[] = [
                        $searchkeyMaster->searchkey_name,
                        JmUtils::rulesText($searchkeyMaster->searchkey_name, [$url, 'helperType' => CsvHelperController::JOB_TYPE], $description, $searchkeyMaster->table_name),
                    ];
                    break;
                default:
                    $searchKeysDispColumnRules[] = [
                        $searchkeyMaster->searchkey_name,
                        JmUtils::rulesText(
                            $searchkeyMaster->searchkey_name,
                            [$url, 'helperType' => CsvHelperController::toHelperNo($searchkeyMaster->searchkey_no)],
                            $description,
                            $searchkeyMaster->table_name
                        ),
                    ];
                    break;
            }
        }
        return $searchKeysDispColumnRules;
    }

    public static function getValidArray()
    {
        return [
            self::FLAG_VALID   => Yii::t('app', '有効'),
            self::FLAG_INVALID => Yii::t('app', '無効'),
        ];
    }

    public static function getIsCategoryLabel()
    {
        return [
            self::CATEGORY_SELECTABLE   => Yii::t('app', '選択する'),
            self::CATEGORY_UNSELECTABLE => Yii::t('app', '選択しない'),
        ];
    }

    public static function getIsAndSearch()
    {
        return [
            self::IS_SEARCH_AND => Yii::t('app', 'and'),
            self::IS_SEARCH_OR  => Yii::t('app', 'or'),
        ];
    }

    public static function getSearchInputTool()
    {
        return [
            self::SEARCH_INPUT_TOOL_MODAL    => Yii::t('app', 'モーダル'),
            self::SEARCH_INPUT_TOOL_CHECKBOX => Yii::t('app', 'チェックボックス'),
            self::SEARCH_INPUT_TOOL_DROPDOWN => Yii::t('app', 'プルダウン'),
        ];
    }

    public static function getIsMoreSearch()
    {
        return [
            self::DISPLAY_FIRST             => Yii::t('app', '最初から表示'),
            self::DISPLAY_WHEN_PRESS_BUTTON => Yii::t('app', 'ボタンを押すと表示'),
        ];
    }

    public static function getIsOnTop()
    {
        return [
            self::DISPLAY_IN_TOP_PAGE    => Yii::t('app', '詳細検索およびPCトップに表示'),
            self::DISPLAY_IN_SEARCH_ONLY => Yii::t('app', '詳細検索にのみ表示'),
        ];
    }

    public static function getIconFlg()
    {

        return [
            self::ICON_FLG_VALID   => Yii::t('app', '表示する'),
            self::ICON_FLG_INVALID => Yii::t('app', '表示しない'),
        ];
    }

    /**
     * @return array
     */
    public function getFormatTable()
    {
        return [
            'is_category_label' => self::getIsCategoryLabel() + [null => Yii::t('app', '選択する')],
            'is_and_search'     => self::getIsAndSearch() + [null => Yii::t('app', 'or')],
            'search_input_tool' => self::getSearchInputTool() + [null => Yii::t('app', 'モーダル')],
            'icon_flg'          => self::getIconFlg() + [null => Yii::t('app', '表示しない')],
            'valid_chk'         => self::getValidArray(),
        ];
    }

    /**
     * 検索キーコードのCSVダウンロード用
     * CSVのソースを生成
     * 生成されるCSVにより分岐
     *
     * @return yii\data\ActiveDataProvider
     */
    public function keyCsvSearch()
    {
        $query = $this->csvDownloadQuery();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => $this->keyCsvSearchSortKey()]
        ]);
        return $dataProvider;
    }

    /**
     * 検索キーコードCSVダウンロード用のソートキー取得
     * 生成されるCSVにより分岐
     *
     * @return array
     */
    private function keyCsvSearchSortKey()
    {
        $keys = [];
        if ($this->table_name === 'station') {
            //stationはsortkeyが2つ。都道府県コードを最初に追加する
            $keys['pref_no'] = SORT_ASC;
        }
        $keys[$this->columnNameOfItemNo] = SORT_ASC;
        return $keys;
    }

    /**
     * 検索キーコードのCSVダウンロード用
     * 生成されるCSVにより分岐
     *
     * @return ActiveQuery
     */
    public function csvDownloadQuery()
    {
        switch ($this->table_name) {
            case 'pref':
                return Dist::find()->where(['exists',
                    Pref::find()
                        ->joinWith(Area::tableName())
                        ->where(Pref::tableName() . '.pref_no = ' . Dist::tableName() . '.pref_no')
                        ->andWhere([Area::tableName() . '.valid_chk' => Area::FLAG_VALID,])
                ]);
                break;
            case 'wage_category':
                return WageItem::find()->where(['valid_chk' => WageItem::FLAG_VALID]);
                break;
            // 職種検索キーは全サイトで無効になるがソースは残す。
            case 'job_type_category':
                return JobTypeSmall::find()->where(['valid_chk' => JobTypeSmall::FLAG_VALID]);
                break;
            case 'station':
                return Station::find()->where(['exists',
                    Pref::find()
                        ->joinWith(Area::tableName())
                        ->where(Pref::tableName() . '.pref_no = ' . Station::tableName() . '.pref_no')
                        ->andWhere([Area::tableName() . '.valid_chk' => Area::FLAG_VALID,])
                ]);
                break;
            default:
                /** @var ActiveRecord $baseModel */
                $model = Yii::createObject($this->modelFullName);
                if ($model->hasProperty('itemModelName')) {
                    /** @var ActiveRecord $itemModel */
                    $itemModel = $model->itemModelName;
                    return $itemModel::find()->where(['valid_chk' => SearchkeyItem::FLAG_VALID]);
                } else {
                    return $model::find()->where(['valid_chk' => self::FLAG_VALID]);
                }
                break;
        }
    }

    /**
     * 検索キーコードのcsvダウンロード用にattributeの配列を生成する
     * 生成されるCSVにより分岐
     *
     * @return array
     */
    public function searchkeyCsvAttributes()
    {
        switch ($this->table_name) {
            case 'pref':
                return (new Dist())->searchkeyCsvAttributes();
                break;
            case 'wage_category':
                return (new WageItem())->searchkeyCsvAttributes();
                break;
            // 職種検索キーは全サイトで無効になるがソースは残す。
            case 'job_type_category':
                return (new JobTypeSmall())->searchkeyCsvAttributes();
                break;
            case 'station':
                return (new Station())->searchkeyCsvAttributes();
                break;
            default:
                /** @var ActiveRecord $model */
                $model = Yii::createObject($this->modelFullName);
                return $model->searchkeyCsvAttributes();
                break;
        }
    }

    /**
     * 検索キーコードのcsvダウンロード用
     * csvのファイル名を生成
     *
     * @return string
     */
    public function csvFileName()
    {
        $tableName = Yii::createObject($this->modelFullName)->tablename();
        $csvFileName = $tableName . 'List_' . date('YmdHi') . '.csv';
        return $csvFileName;
    }

}
