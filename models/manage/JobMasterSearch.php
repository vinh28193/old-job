<?php

namespace app\models\manage;

use app\common\SearchModelTrait;
use app\models\manage\searchkey\JobSearchkeyItem;
use app\models\manage\searchkey\JobWage;
use app\modules\manage\models\JobCsvRegister;
use yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use app\models\manage\searchkey\WageCategory;

/**
 * JobMasterSearchモデル
 * 検索用のモデル
 *
 * @property int corpMasterId
 * @property int $deleted_at
 * @property string $prefCsvCell
 * @property string $distCsvCell
 * @property string $stationCsvCell
 * @property string $wageItemCsvCell
 * @property string $jobTypeSmallCsvCell
 */
class JobMasterSearch extends JobMaster
{
    use SearchModelTrait;
    /** キーワード検索対象のデフォルト値 */
    const DEFAULT_SEARCH_ITEM = 'all';
    /** 非掲載or掲載中 */
    const DISP_INVALID = 0;
    const DISP_VALID = 1;
    /**
     * CSVダウンロード時の(ExportHelper::outputAsCSVの)処理件数
     */
    const STRESS_MODE_PAGE_SIZE = 1000;
    /** @var string キーワード検索対象・キーワード */
    public $searchItem;
    public $searchText;
    /** @var bool 掲載状況 */
    public $isDisplay;
    /** @var string|int 掲載開始・終了日 */
    public $startFrom;
    public $startTo;
    public $endFrom;
    public $endTo;

    /**
     * ルール設定（親からは独立）
     * @return array
     */
    public function rules()
    {
        return array_merge([
            [['searchItem', 'searchText'], 'string'],
            [['corpMasterId', 'client_master_id', 'client_charge_plan_id', 'job_review_status_id'], 'integer'],
            [['isDisplay', 'valid_chk'], 'boolean'],
            ['startFrom', 'date', 'timestampAttribute' => 'startFrom'],
            ['startTo', 'date', 'timestampAttribute' => 'startTo'],
            ['endFrom', 'date', 'timestampAttribute' => 'endFrom'],
            ['endTo', 'date', 'timestampAttribute' => 'endTo'],
        ], $this->getCsvSearchRules());
    }

    /**
     * ラベル設定（親を継承）
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'searchItem' => Yii::t('app', 'すべて'),
            'searchText' => Yii::t('app', 'キーワード'),
            'isDisplay' => Yii::t('app', '掲載状況'),
            'prefCsvCell' => Yii::$app->searchKey->label('job_dist') . Yii::t('app', '(都道府県コード)'),
            'distCsvCell' => Yii::$app->searchKey->label('job_dist') . Yii::t('app', '(市区町村コード)'),
            'stationCsvCell1' => Yii::t('app', '駅-1(駅コード)'),
            'transportCsvCell1' => Yii::t('app', '交通手段-1(徒歩=0,車・バス=1)'),
            'necessaryTimeCsvCell1' => Yii::t('app', '所要時間-1(分)'),
            'stationCsvCell2' => Yii::t('app', '駅-2(駅コード)'),
            'transportCsvCell2' => Yii::t('app', '交通手段-2(徒歩=0,車・バス=1)'),
            'necessaryTimeCsvCell2' => Yii::t('app', '所要時間-2(分)'),
            'stationCsvCell3' => Yii::t('app', '駅-3(駅コード)'),
            'transportCsvCell3' => Yii::t('app', '交通手段-3(徒歩=0,車・バス=1)'),
            'necessaryTimeCsvCell3' => Yii::t('app', '所要時間-3(分)'),
            // 職種検索キーは全サイトで無効になるがソースは残す。
            'jobTypeSmallCsvCell' => Yii::$app->searchKey->label('job_type') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem1CsvCell' => Yii::$app->searchKey->label('job_searchkey_item1') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem2CsvCell' => Yii::$app->searchKey->label('job_searchkey_item2') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem3CsvCell' => Yii::$app->searchKey->label('job_searchkey_item3') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem4CsvCell' => Yii::$app->searchKey->label('job_searchkey_item4') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem5CsvCell' => Yii::$app->searchKey->label('job_searchkey_item5') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem6CsvCell' => Yii::$app->searchKey->label('job_searchkey_item6') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem7CsvCell' => Yii::$app->searchKey->label('job_searchkey_item7') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem8CsvCell' => Yii::$app->searchKey->label('job_searchkey_item8') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem9CsvCell' => Yii::$app->searchKey->label('job_searchkey_item9') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem10CsvCell' => Yii::$app->searchKey->label('job_searchkey_item10') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem11CsvCell' => Yii::$app->searchKey->label('job_searchkey_item11') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem12CsvCell' => Yii::$app->searchKey->label('job_searchkey_item12') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem13CsvCell' => Yii::$app->searchKey->label('job_searchkey_item13') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem14CsvCell' => Yii::$app->searchKey->label('job_searchkey_item14') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem15CsvCell' => Yii::$app->searchKey->label('job_searchkey_item15') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem16CsvCell' => Yii::$app->searchKey->label('job_searchkey_item16') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem17CsvCell' => Yii::$app->searchKey->label('job_searchkey_item17') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem18CsvCell' => Yii::$app->searchKey->label('job_searchkey_item18') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem19CsvCell' => Yii::$app->searchKey->label('job_searchkey_item19') . Yii::t('app', '(検索キーコード)'),
            'jobSearchkeyItem20CsvCell' => Yii::$app->searchKey->label('job_searchkey_item20') . Yii::t('app', '(検索キーコード)'),
            'import_site_job_id' => Yii::t('app', '他サイト連携ID'),
        ]);
    }

    /**
     * Grid用dataProviderを生成
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // メイン検索クエリ
        $query = self::find()->with(['clientMaster.corpMaster', 'clientChargePlan']);
        // プロバイダ作成
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'job_no' => SORT_DESC,
                ]
            ]
        ]);
        // sort設定
        $dataProvider->sort->attributes['client_master_id'] = [
            'asc' => [ClientMaster::tableName() . '.client_name' => SORT_ASC],
            'desc' => [ClientMaster::tableName() . '.client_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes += [
            'corpLabel' => [
                'asc' => [CorpMaster::tableName() . '.corp_name' => SORT_ASC],
                'desc' => [CorpMaster::tableName() . '.corp_name' => SORT_DESC],
            ]
        ];
        // load処理(loadAuthParamはbeforeValidate内に含まれている)
        if (!$this->load($params) || !$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }
        $this->searchWhere($query, $params);
        return $dataProvider;
    }

    /**
     * ロード処理 ※先祖を呼び出す。
     *
     * @param array $data
     * @param string $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        return ActiveRecord::load($data, $formName = null);
    }

    /**
     * csv用検索
     * @param $params
     * @return ActiveDataProvider
     */
    public function csvSearch($params)
    {
        // メイン検索クエリ
        $query = self::find();
        $searchKeys = Yii::$app->searchKey->searchKeys;

        foreach ($searchKeys as $tableName => $searchKey) {
            $relationName = $searchKey->withString;
            $query->with($relationName);
        }

        $query->with([
            'clientMaster.corpMaster',
            'clientChargePlan',
            'mediaUpload1',
            'mediaUpload2',
            'mediaUpload3',
            'mediaUpload4',
            'mediaUpload5',
        ]);

        // プロバイダ作成
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 0],
            'sort' => [
                'defaultOrder' => ['job_no' => SORT_DESC]
            ]
        ]);

        $params = $this->parse($params, $this->formName());

        // load処理(loadAuthParamはbeforeValidate内に含まれている)
        if (!$this->load($params) || !$this->validate()) {
            return $dataProvider;
        }
        $this->searchWhere($query, $params);

        $this->selected($query);
        return $dataProvider;
    }

    /**
     * 削除するレコードのmodelを取得
     * @param $params
     * @return array|bool|\yii\db\ActiveRecord[]
     */
    public function deleteSearch($params)
    {
        $query = self::find();
        // gridの、json形式の値を使いやすい形に変換
        $params = $this->parse($params, $this->formName());
        // load処理
        // 何もloadできなかった場合(初期状態)はfalseを返す
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
     * @param JobMasterSearch[] $models
     * @return int 削除件数
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function backupAndDelete($models)
    {
        $columnNames = JobMasterBackup::getTableSchema()->columnNames;
        $values = [];
        foreach ($models as $model) {
            /** @var JobMasterSearch $model */
            $value = [];
            foreach ($columnNames as $columnName) {
                $value[] = $model->$columnName;
            }
            $values[] = $value;
        }
        Yii::$app->db->createCommand()->batchInsert(JobMasterBackup::tableName(), $columnNames, $values)->execute();
        return $this->deleteAll(['id' => ArrayHelper::getColumn($models, 'id')]);
    }

    /**
     * ActiveQueryに検索条件を追加
     * @param $query \yii\Db\ActiveQuery
     * @param $params array
     */
    private function searchWhere($query, $params)
    {
        // キーワード検索
        if ($this->searchItem == self::DEFAULT_SEARCH_ITEM) {
            $orWhere = ['or'];
            foreach (Yii::$app->functionItemSet->job->searchableByKeyWord as $attribute) {
                $orWhere[] = ['like', self::tableName() . '.' . $attribute->column_name, $this->searchText];
            }
            $query->andFilterWhere($orWhere);
        } else {
            $query->andFilterWhere(['like', self::tableName() . '.' . $this->searchItem, $this->searchText]);
        }
        // join分岐（検索とソートのパラメータを見て必要十分なjoinをする）
        if (!$this->isEmpty($this->isDisplay)) {
            if ($this->isDisplay == 1) {
                $query->innerJoinWith(['clientMaster.corpMaster', 'clientChargePlan.dispType'], false)
                    ->andWhere([
                        self::tableName() . '.valid_chk' => self::FLAG_VALID,
                        self::tableName() . '.job_review_status_id' => JobReviewStatus::STEP_REVIEW_OK,
                        ClientMaster::tableName() . '.valid_chk' => self::FLAG_VALID,
                        CorpMaster::tableName() . '.valid_chk' => self::FLAG_VALID,
                        ClientChargePlan::tableName() . '.valid_chk' => self::FLAG_VALID,
                        DispType::tableName() . '.valid_chk' => self::FLAG_VALID,
                    ])->andWhere([
                        'or',
                        ['<=', self::tableName() . '.disp_start_date', time()],
                        [self::tableName() . '.disp_start_date' => null],
                    ])->andWhere([
                        'or',
                        ['>=', self::tableName() . '.disp_end_date', time() - (60 * 60 * 24)],
                        [self::tableName() . '.disp_end_date' => null],
                    ]);
            } elseif ($this->isDisplay == 0) {
                $query->joinWith(['clientMaster.corpMaster', 'clientChargePlan.dispType'], false)
                    ->andWhere(['or',
                        [self::tableName() . '.valid_chk' => self::FLAG_INVALID],
                        ['not', [self::tableName() . '.job_review_status_id' => JobReviewStatus::STEP_REVIEW_OK]],
                        [ClientMaster::tableName() . '.valid_chk' => self::FLAG_INVALID],
                        [ClientMaster::tableName() . '.id' => null],
                        [CorpMaster::tableName() . '.valid_chk' => self::FLAG_INVALID],
                        [CorpMaster::tableName() . '.id' => null],
                        [ClientChargePlan::tableName() . '.valid_chk' => self::FLAG_INVALID],
                        [DispType::tableName() . '.valid_chk' => self::FLAG_INVALID],
                        ['>=', self::tableName() . '.disp_start_date', time()],
                        ['and',
                            ['<=', self::tableName() . '.disp_end_date', time() - (60 * 60 * 24)],
                            ['not', [self::tableName() . '.disp_end_date' => null]],
                        ],
                        [],
                    ]);
            }
        } elseif (strpos(ArrayHelper::getValue($params, 'sort'), 'corpLabel') !== false) {
            $query->joinWith('clientMaster.corpMaster');
        } elseif (!$this->isEmpty($this->corpMasterId) || strpos(ArrayHelper::getValue($params, 'sort'), 'client_master_id') !== false) {
            $query->joinWith('clientMaster');
        }
        // 各項目検索
        $query->andFilterWhere([ClientMaster::tableName() . '.corp_master_id' => $this->corpMasterId])
            ->andFilterWhere([self::tableName() . '.client_master_id' => $this->client_master_id])
            ->andFilterWhere([self::tableName() . '.client_charge_plan_id' => $this->client_charge_plan_id])
            ->andFilterWhere([self::tableName() . '.job_review_status_id' => $this->job_review_status_id])
            ->andFilterWhere([self::tableName() . '.valid_chk' => $this->valid_chk])
            ->andFilterWhere(['>=', self::tableName() . '.disp_start_date', $this->startFrom])
            ->andFilterWhere(['<=', self::tableName() . '.disp_start_date', $this->startTo])
            ->andFilterWhere(['>=', self::tableName() . '.disp_end_date', $this->endFrom])
            ->andFilterWhere(['<=', self::tableName() . '.disp_end_date', $this->endTo]);
    }

    /**
     * カラム名を取得します。
     * リレーション関係のものは対応するリレーション先のカラム名から取得します。
     * @param string $attr attribute
     * @return string value値
     */
    public static function getColumnName($attr)
    {
        switch ($attr) {
            case 'client_master_id':
                return 'clientMaster.client_name';
                break;
            case 'corpLabel':
                return 'clientMaster.corpMaster.corp_name';
                break;
            case 'client_charge_plan_id':
                return 'clientChargePlan.plan_name';
                break;
            case 'job_review_status_id':
                return function ($model) {
                    /** @var JobMaster $model */
                    return $model->jobReviewStatus->name;
                };
                break;
            default :
                return $attr;
                break;
        }
    }

    /**
     * csvダウンロードで使われるattributeの配列を生成する
     * 修正する場合、modules/manage/models/JobCsvRegister::csvAttributes
     * と配列が一致するように修正する必要があるので注意
     * @return array
     */
    public function csvAttributes()
    {
        return ArrayHelper::merge(
            ['valid_chk'],
            self::csvJobAttributes(),
            self::csvSearchKeyAttributes(),
            ['import_site_job_id']
        );
    }

    /**
     * 求人情報の配列を生成する
     * @return array
     */
    private static function csvJobAttributes()
    {
        $attributes = [];
        /** @var string[] $names */
        $names = ArrayHelper::getColumn(Yii::$app->functionItemSet->job->items, 'column_name');
        foreach ($names as $name) {
            switch ($name) {
                case 'disp_start_date':
                case 'disp_end_date':
                    $attributes[] = $name . ':date';
                    break;
                case 'client_master_id':
                    $attributes[$name] = 'clientMaster.client_no';
                    break;
                case 'client_charge_plan_id':
                    $attributes[$name] = 'clientChargePlan.client_charge_plan_no';
                    break;
                case 'corpLabel':
                    break;
                case 'media_upload_id_1':
                case 'media_upload_id_2':
                case 'media_upload_id_3':
                case 'media_upload_id_4':
                case 'media_upload_id_5':
                    $attributes[$name] = str_replace('Id', '', Inflector::variablize($name)) . '.disp_file_name';
                    break;
                default:
                    $attributes[] = $name;
                    break;
            }
        }

        return $attributes;
    }

    /**
     * 検索キーの配列を生成する
     * @return array
     */
    private static function csvSearchKeyAttributes()
    {
        $attributes = [];
        /** @var SearchkeyMaster[] $searchKeys */
        $searchKeys = Yii::$app->searchKey->searchKeys;
        foreach ($searchKeys as $searchKey) {
            switch ($searchKey->job_relation_table) {
                case 'job_dist';
                    $attributes['distCsvCell'] = 'distCsvCell';
                    break;
                case 'job_station_info';
                    for ($i = 0; $i <= 2; $i++) {
                        $attributes['stationCsvCell' . ($i + 1)] = function (JobMasterSearch $model) use ($i) {
                            return ArrayHelper::getValue($model, 'jobStationModel.' . $i . '.station.station_no');
                        };
                        $attributes['transportCsvCell' . ($i + 1)] = function (JobMasterSearch $model) use ($i) {
                            return ArrayHelper::getValue($model, 'jobStationModel.' . $i . '.transport_type');
                        };
                        $attributes['necessaryTimeCsvCell' . ($i + 1)] = function (JobMasterSearch $model) use ($i) {
                            return ArrayHelper::getValue($model, 'jobStationModel.' . $i . '.transport_time');
                        };
                    }
                    break;
                case 'job_wage';
                    foreach (WageCategory::find()->where(['valid_chk' => self::FLAG_VALID])->orderBy('sort')->all() as $cate) {
                        $attributes[JobCsvRegister::WAGE_NAME_COLUMN . $cate->id] = [
                            'label' => $cate->wage_category_name . Yii::t('app', '(金額)'),
                            'attribute' => function (JobMasterSearch $model) use ($cate) {
                                return $model->cateMaxWage($cate->id);
                            }];
                    }
                    break;
                // 職種検索キーは全サイトで無効になるがソースは残す。
                case 'job_type';
                    $attributes['jobTypeSmallCsvCell'] = 'jobTypeSmallCsvCell';
                    break;
                default:
                    $relationName = $searchKey->jobRelationName;
                    $attributes[$relationName . 'CsvCell'] = function (JobMasterSearch $model) use ($relationName) {
                        return $model->getJobSearchKeyItemCsvCell($relationName);
                    };
                    break;
            }
        }
        return $attributes;
    }

    /**
     * 掲載状況リスト
     * @return array
     */
    public static function getDispStatusList()
    {
        return [
            '' => Yii::t('app', 'すべて'),
            self::DISP_INVALID => Yii::t('app', '非掲載'),
            self::DISP_VALID => Yii::t('app', '掲載中'),
        ];
    }

    /**
     * 一括削除時にdeleted_atを取得するためのgetter
     * @return int
     */
    public function getDeleted_at()
    {
        return time();
    }

    /**
     * 市区町村検索キーcsvファイルのcellに出力する値を取得する
     * @return string
     */
    public function getDistCsvCell()
    {
        if ($this->jobDist) {
            $distCds = [];
            foreach ($this->jobDist as $jobDist) {
//                if (isset($jobDist->dist->pref->area) && $jobDist->dist->pref->area->valid_chk === 1) {
                if ($jobDist->dist !== null && $jobDist->dist->pref->area->valid_chk === 1) {
                    $distCds[] = $jobDist->dist->dist_cd;
                }
            }
            asort($distCds);
            return implode('|', $distCds);
        }
        return '';
    }

    /**
     * 給与検索キーcsvファイルのcellに出力する値を取得する
     * @param int $categoryId
     * @return string
     */
    public function cateMaxWage($categoryId)
    {
        if ($this->jobWage) {
            // 該当カテゴリのみに絞る
            $items = array_filter($this->jobWage, function ($item) use ($categoryId) {
                /** @var JobWage $item */
                if (!isset($item->wageItem) || $item->wageItem->valid_chk === 0) {
                    return false;
                }
                if (!isset($item->wageItem->wageCategory) || $item->wageItem->wageCategory->valid_chk === 0) {
                    return false;
                }
                return ArrayHelper::getValue($item, 'wageItem.wage_category_id') == $categoryId;
            });
            if ($items) {
                ArrayHelper::multisort($items, 'wageItem.wage_item_name', SORT_DESC);
                return $items[0]->wageItem->wage_item_name;
            }
        }
        return '';
    }

    /**
     * 職種検索キーcsvファイルのcellに出力する値を取得する
     * （職種検索キーは全サイトで無効になるがソースは残す。）
     * @return string
     */
    public function getJobTypeSmallCsvCell()
    {
        if ($this->jobType) {
            $jobTypeSmallNos = [];
            foreach ($this->jobType as $jobType) {
                if (!isset($jobType->jobTypeSmall) || $jobType->jobTypeSmall->valid_chk === 0) {
                    continue;
                }
                if (!isset($jobType->jobTypeSmall->jobTypeBig) ||
                    $jobType->jobTypeSmall->jobTypeBig->valid_chk === 0) {
                    continue;
                }
                if (!isset($jobType->jobTypeSmall->jobTypeBig->jobTypeCategory) ||
                    $jobType->jobTypeSmall->jobTypeBig->jobTypeCategory->valid_chk === 0) {
                    continue;
                }
                $jobTypeSmallNos[] = $jobType->jobTypeSmall->job_type_small_no;
            }
            asort($jobTypeSmallNos);
            return implode('|', $jobTypeSmallNos);
        }
        return '';
    }

    /**
     * 汎用検索キーcsvファイルのcellに出力する値を取得する
     * @param string $relationName
     * @return string
     */
    public function getJobSearchKeyItemCsvCell($relationName)
    {
        if ($this->$relationName) {
            $searchKeyItemNos = [];
            foreach ($this->$relationName as $jobSearchKeyItem) {
                /** @var JobSearchkeyItem $jobSearchKeyItem */
                if ($jobSearchKeyItem->searchKeyItem && $jobSearchKeyItem->searchKeyItem->valid_chk === 1) {
                    $searchKeyItemNos[] = $jobSearchKeyItem->searchKeyItem->searchkey_item_no;
                }
            }
            asort($searchKeyItemNos);
            return implode('|', $searchKeyItemNos);
        }
        return '';
    }
}