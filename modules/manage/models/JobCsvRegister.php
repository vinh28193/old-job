<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/09/09
 * Time: 11:34
 */

namespace app\modules\manage\models;

use app\models\manage\BaseColumnSet;
use app\models\manage\JobColumnSet;
use app\models\manage\JobMaster;
use app\models\manage\SearchkeyMaster;
use app\modules\manage\components\JobCsvLoader;
use yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use app\models\manage\searchkey\WageCategory;
use app\models\manage\searchkey\WageItem;
use app\common\Helper\JmUtils;

/**
 * Class JobCsvRegister
 * @package app\modules\manage\models
 */
class JobCsvRegister extends JobMaster
{
    /** @var JobCsvLoader */
    public $loader;

    /** @var string 給与検索キー用プロパティプレフィックス */
    const WAGE_NAME_COLUMN = 'wageItem_';

    /** @var array 市区町村No */
    public $dist;
    /** @var array 市区町村Id */
    private $_distId;

    /** @var string */
    public $jobTypeSmall;
    /** @var string|array job_type_small.id */
    private $_jobTypeSmallId;

    /** @var int 駅関連情報 */
    public $stationCd1;
    public $transportType1;
    public $transportTime1;
    public $stationCd2;
    public $transportType2;
    public $transportTime2;
    public $stationCd3;
    public $transportType3;
    public $transportTime3;

    /** @var integer 申し込みプランNo、掲載企業No */
    public $clientChargePlanNo;
    public $clientNo;

    /** @var string 申し込みプランのラベル名 */
    public $clientChargePlanLabel;

    /** @var string 申し込みプランNo、掲載企業No */
    public $dispFileName1;
    public $dispFileName2;
    public $dispFileName3;
    public $dispFileName4;
    public $dispFileName5;

    /** @var array keyをカテゴリidとしたカテゴリ毎の給与最大値 */
    private $_maxWage;
    /** @var array 登録するwageItemのid */
    private $_wageItem = [];

    /** @var array 汎用検索キーNo */
    private $_searchkeyItem1;
    private $_searchkeyItem2;
    private $_searchkeyItem3;
    private $_searchkeyItem4;
    private $_searchkeyItem5;
    private $_searchkeyItem6;
    private $_searchkeyItem7;
    private $_searchkeyItem8;
    private $_searchkeyItem9;
    private $_searchkeyItem10;
    private $_searchkeyItem11;
    private $_searchkeyItem12;
    private $_searchkeyItem13;
    private $_searchkeyItem14;
    private $_searchkeyItem15;
    private $_searchkeyItem16;
    private $_searchkeyItem17;
    private $_searchkeyItem18;
    private $_searchkeyItem19;
    private $_searchkeyItem20;

    /** @var array 汎用検索キーId */
    private $_searchkeyItem1Id;
    private $_searchkeyItem2Id;
    private $_searchkeyItem3Id;
    private $_searchkeyItem4Id;
    private $_searchkeyItem5Id;
    private $_searchkeyItem6Id;
    private $_searchkeyItem7Id;
    private $_searchkeyItem8Id;
    private $_searchkeyItem9Id;
    private $_searchkeyItem10Id;
    private $_searchkeyItem11Id;
    private $_searchkeyItem12Id;
    private $_searchkeyItem13Id;
    private $_searchkeyItem14Id;
    private $_searchkeyItem15Id;
    private $_searchkeyItem16Id;
    private $_searchkeyItem17Id;
    private $_searchkeyItem18Id;
    private $_searchkeyItem19Id;
    private $_searchkeyItem20Id;

    /**
     * 申し込みプランをモデルに持たせておく
     */
    public function init()
    {
        parent::init();
        $this->clientChargePlanLabel = Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label;
    }

    /**
     * 検索キーに関する専用のsetter
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (preg_match('/' . self::WAGE_NAME_COLUMN . '([\d]+)/', $name, $wageItemResults)) {
            $this->setMaxWage($wageItemResults[1], $value);
        } elseif (preg_match('/searchkeyItem([\d]+)/', $name, $searchkeyItemResults)) {
            $this->setSearchKeyItems($searchkeyItemResults[1], $value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * 検索キーに関する専用のgetter
     * @param string $name
     * @return array|int|mixed|null|yii\db\ActiveQuery
     */
    public function __get($name)
    {
        if (preg_match('/' . self::WAGE_NAME_COLUMN . '([\d]+)/', $name, $wageItemResults)) {
            return null;
        } elseif (preg_match('/searchkeyItem([\d]+)/', $name, $searchkeyItemResults)) {
            return $this->getSearchKeyItems($searchkeyItemResults[1]);
        } else {
            return parent::__get($name);
        }
    }

    /**
     * 先祖のloadメソッドを呼び出す
     * @param array $data
     * @param string $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        return ActiveRecord::load($data, $formName);
    }

    /** ▼▼ 各種検索キーのgetter、setter ▼▼ */
    /**
     * maxWageを代入
     * @param $id
     * @param $value
     */
    private function setMaxWage($id, $value)
    {
        if (!JmUtils::isEmpty($value)) {
            $this->_maxWage[$id] = $value;
        }
    }

    /**
     * maxWageの配列を返す
     * @return integer[]|null
     */
    public function getMaxWage()
    {
        return $this->_maxWage;
    }

    /**
     * 汎用検索キーに関するsetter（"|"(パイプ区切り)で半角英数が入っているので、分解して一次配列にしている）
     * @param $tableNo
     * @param $value
     */
    private function setSearchKeyItems($tableNo, $value)
    {
        $this->{'_searchkeyItem' . $tableNo} = $value;
    }

    /**
     * 汎用検索キーに関するgetter
     * @param $tableNo
     * @return integer[]|null
     */
    private function getSearchKeyItems($tableNo)
    {
        return $this->{'_searchkeyItem' . $tableNo};
    }
    /** ▲▲ 各種検索キーのgetter、setter ▲▲*/

    /**
     * CSV一括更新用にJobMasterからrules()をカスタマイズ
     * @return array
     */
    public function rules()
    {
        $dateWhen = function ($model) {
            // 運営元の時と自由プランが選択されている時は終了日の入力がある
            return Yii::$app->user->identity->myRole == Manager::OWNER_ADMIN || (isset($this->clientChargePlan) && $this->clientChargePlan->period == null);
        };
        $compareWhen = function ($model) {
            // 運営元の時もしくは自由プランが選択されている時、disp_start_dateが適切な値ならば終了日の比較validationが走る
            return $this->dispStartDate !== false && (Yii::$app->user->identity->myRole == Manager::OWNER_ADMIN || (isset($this->clientChargePlan) && $this->clientChargePlan->period == null));
        };

        $jobMasterRules = ArrayHelper::merge(Yii::$app->functionItemSet->job->rules, [
            [
                [
                    'created_at',
                    'updated_at',
                    'disp_type_sort',
                    'media_upload_id_1',
                    'media_upload_id_2',
                    'media_upload_id_3',
                    'media_upload_id_4',
                    'media_upload_id_5',
                ],
                'number',
            ],
            [
                'disp_start_date', 'date', 'timestampAttribute' => 'disp_start_date',
                'min' => '1920/01/01',
                'tooSmall' => Yii::t('app', '{attribute}は{min}以降の日付にしてください.'),
                'max' => '2037/12/31',
                'tooBig' => Yii::t('app', '{attribute}は{max}以前の日付にしてください.'),
            ],
            [
                'disp_end_date',
                'date',
                'timestampAttribute' => 'disp_end_date',
                'when' => $dateWhen,
                'min' => '1920/01/01',
                'tooSmall' => Yii::t('app', '{attribute}は{min}以降の日付にしてください.'),
                'max' => '2037/12/31',
                'tooBig' => Yii::t('app', '{attribute}は{max}以前の日付にしてください.'),
            ],
            ['disp_end_date', 'compare', 'compareAttribute' => 'dispStartDate', 'operator' => '>=', 'message' => Yii::t('app', '{attribute}は{compareAttribute}より後の日付にしてください.'), 'when' => $compareWhen],
            [['valid_chk', 'disp_start_date'], 'required'],
            ['valid_chk', 'boolean'],
        ]);

        /** @var SearchkeyMaster $wageSearchkeyMaster */
        $wageSearchkeyMaster = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'wage_category');
        /** @var WageCategory[] $cates */
        $cates = $wageSearchkeyMaster->searchKeyModels ?? [];
        $wageItems = array_map(function (WageCategory $cate) {
            return self::wageColumnRename($cate->id);
        }, $cates);

        $optionItems = array_keys(Yii::$app->functionItemSet->job->optionAttributeLabels);

        return ArrayHelper::merge($jobMasterRules, [
            [
                [
                    'clientChargePlanNo',
                    'clientNo',
                    'stationCd1',
                    'transportTime1',
                    'stationCd2',
                    'transportTime2',
                    'stationCd3',
                    'transportTime3',
                    'import_site_job_id',
                ],
                'number',
            ],
            [['transportType1', 'transportType2', 'transportType3'], 'boolean'],
            [['dispFileName1', 'dispFileName2', 'dispFileName3', 'dispFileName4', 'dispFileName5'], 'string'],
            [['clientChargePlanNo', 'clientNo', 'dist'], 'required'],
            [$wageItems, 'safe'],
            [
                'job_no',
                function ($attribute, $params) {
                    $this->jobNoExistCheck();
                },
                'skipOnEmpty' => false,
            ],
            [
                'clientNo',
                function ($attribute, $params) {
                    $this->clientNoExistCheck();
                },
            ],
            [
                'clientChargePlanNo',
                function ($attribute, $params) {
                    $this->clientChargePlanNoCheck();
                },
            ],
            [
                ['dispFileName1', 'dispFileName2', 'dispFileName3', 'dispFileName4', 'dispFileName5'],
                function ($attribute, $params) {
                    $this->dispFileNameExistCheck($attribute);
                },
                'skipOnEmpty' => false,
            ],
            [
                $optionItems,
                function ($attribute, $params) {
                    $this->subsetCheck($attribute);
                },
            ],
            [
                [
                    'dist',
                    'jobTypeSmall',
                    'searchkeyItem1',
                    'searchkeyItem2',
                    'searchkeyItem3',
                    'searchkeyItem4',
                    'searchkeyItem5',
                    'searchkeyItem6',
                    'searchkeyItem7',
                    'searchkeyItem8',
                    'searchkeyItem9',
                    'searchkeyItem10',
                    'searchkeyItem11',
                    'searchkeyItem12',
                    'searchkeyItem13',
                    'searchkeyItem14',
                    'searchkeyItem15',
                    'searchkeyItem16',
                    'searchkeyItem17',
                    'searchkeyItem18',
                    'searchkeyItem19',
                    'searchkeyItem20',
                ],
                function ($attribute, $params) {
                    $this->searchkeyCheck($attribute);
                },
            ],
            [
                ['stationCd1', 'stationCd2', 'stationCd3'],
                function ($attribute, $params) {
                    $this->stationCodeCheck($attribute);
                },
            ],
            [
                'maxWage',
                function ($attribute, $params) {
                    $this->wageItemCheck();
                },
            ],
        ]);
    }

    /**
     * validate前にシナリオをセットする
     * @return bool
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->initScenario();
            return true;
        }
        return false;
    }

    /**
     * エラーがなく既存原稿かつ掲載終了日が空の場合、nullを許可しているdisp_end_dateを明示的に
     * nullにしておく（変更の場合、掲載終了日が空だと上書きされない。）
     */
    public function afterValidate()
    {
        if (!$this->hasErrors() && !$this->isNewRecord && !isset($this->disp_end_date)) {
            $this->disp_end_date = null;
        }
        parent::afterValidate();
    }

    /**
     * ラベル設定（JobMasterを継承）
     * JobMasterSearchとラベル名が結構違っていたため、分けている
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'pref' => Yii::$app->searchKey->label('job_dist'),
            'dist' => Yii::$app->searchKey->label('job_dist'),
            'stationCd1' => Yii::t('app', '駅-1'),
            'transportType1' => Yii::t('app', '交通手段-1'),
            'transportTime1' => Yii::t('app', '所要時間-1(分)'),
            'stationCd2' => Yii::t('app', '駅-2'),
            'transportType2' => Yii::t('app', '交通手段-2'),
            'transportTime2' => Yii::t('app', '所要時間-2(分)'),
            'stationCd3' => Yii::t('app', '駅-3'),
            'transportType3' => Yii::t('app', '交通手段-3'),
            'transportTime3' => Yii::t('app', '所要時間-3(分)'),
            'jobTypeSmall' => Yii::$app->searchKey->label('job_type'),// 職種検索キーは全サイトで無効になるがソースは残す。
            'searchkeyItem1' => Yii::$app->searchKey->label('job_searchkey_item1'),
            'searchkeyItem2' => Yii::$app->searchKey->label('job_searchkey_item2'),
            'searchkeyItem3' => Yii::$app->searchKey->label('job_searchkey_item3'),
            'searchkeyItem4' => Yii::$app->searchKey->label('job_searchkey_item4'),
            'searchkeyItem5' => Yii::$app->searchKey->label('job_searchkey_item5'),
            'searchkeyItem6' => Yii::$app->searchKey->label('job_searchkey_item6'),
            'searchkeyItem7' => Yii::$app->searchKey->label('job_searchkey_item7'),
            'searchkeyItem8' => Yii::$app->searchKey->label('job_searchkey_item8'),
            'searchkeyItem9' => Yii::$app->searchKey->label('job_searchkey_item9'),
            'searchkeyItem10' => Yii::$app->searchKey->label('job_searchkey_item10'),
            'searchkeyItem11' => Yii::$app->searchKey->label('job_searchkey_item11'),
            'searchkeyItem12' => Yii::$app->searchKey->label('job_searchkey_item12'),
            'searchkeyItem13' => Yii::$app->searchKey->label('job_searchkey_item13'),
            'searchkeyItem14' => Yii::$app->searchKey->label('job_searchkey_item14'),
            'searchkeyItem15' => Yii::$app->searchKey->label('job_searchkey_item15'),
            'searchkeyItem16' => Yii::$app->searchKey->label('job_searchkey_item16'),
            'searchkeyItem17' => Yii::$app->searchKey->label('job_searchkey_item17'),
            'searchkeyItem18' => Yii::$app->searchKey->label('job_searchkey_item18'),
            'searchkeyItem19' => Yii::$app->searchKey->label('job_searchkey_item19'),
            'searchkeyItem20' => Yii::$app->searchKey->label('job_searchkey_item20'),
            'clientChargePlanNo' => Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label,
            'clientNo' => Yii::$app->functionItemSet->job->items['client_master_id']->label,
            'maxWage' => Yii::$app->searchKey->label('wage_category'),//エラー表示用
            'media_upload_id_1' => Yii::$app->functionItemSet->job->items['media_upload_id_1']->label,
            'media_upload_id_2' => Yii::$app->functionItemSet->job->items['media_upload_id_2']->label,
            'media_upload_id_3' => Yii::$app->functionItemSet->job->items['media_upload_id_3']->label,
            'media_upload_id_4' => Yii::$app->functionItemSet->job->items['media_upload_id_4']->label,
            'media_upload_id_5' => Yii::$app->functionItemSet->job->items['media_upload_id_5']->label,
            'dispFileName1' => Yii::$app->functionItemSet->job->items['media_upload_id_1']->label,
            'dispFileName2' => Yii::$app->functionItemSet->job->items['media_upload_id_2']->label,
            'dispFileName3' => Yii::$app->functionItemSet->job->items['media_upload_id_3']->label,
            'dispFileName4' => Yii::$app->functionItemSet->job->items['media_upload_id_4']->label,
            'dispFileName5' => Yii::$app->functionItemSet->job->items['media_upload_id_5']->label,
            'import_site_job_id' => Yii::t('app', '他サイト連携ID'),
        ]);
    }

    /**
     * 仕事ID存在チェック
     */
    private function jobNoExistCheck()
    {
        if (JmUtils::isEmpty($this->job_no)) {
            // job_noが空⇒新規登録
            $this->job_no = $this->loader->newJobNo++;
        } elseif (isset($this->loader->jobNos2Ids[$this->job_no])) {
            // job_noが存在する⇒更新（主キーの値を追加し、既存原稿にしておく。）
            $this->id = $this->loader->jobNos2Ids[$this->job_no];
        } else {
            // job_noの入力はあるが存在しない⇒エラー
            $this->addError('job_no', Yii::t('app', '{LABEL}が存在しておりません。', ['LABEL' => $this->getAttributeLabel('job_no')]));
        }
    }

    /**
     * 掲載企業No存在チェック
     */
    private function clientNoExistCheck()
    {
        if ($this->clientNo && isset($this->loader->clientNos2Ids[$this->clientNo])) {
            // 掲載企業Noが入力されており、それが存在するとき
            $this->client_master_id = $this->loader->clientNos2Ids[$this->clientNo];
        } else {
            // それ以外はエラーを返す
            $this->addError('clientNo', Yii::t('app', '{LABEL}IDがありません。', ['LABEL' => $this->getAttributeLabel('clientNo')]));
        }
    }

    /**
     * 料金プラン存在チェック
     */
    private function clientChargePlanNoCheck()
    {
        //ClientNoに関してエラーがある場合はスキップ
        if ($this->hasErrors('clientNo')) {
            return;
        }

        if (!$this->clientChargePlanNo || !isset($this->loader->planNos2Ids[$this->clientChargePlanNo])) {
            $this->addError('clientChargePlanNo', Yii::t('app', '{LABEL}IDがありません。', ['LABEL' => $this->getAttributeLabel('clientChargePlanNo')]));
            return;
        }
        $this->client_charge_plan_id = $this->loader->planNos2Ids[$this->clientChargePlanNo];

        if (!array_key_exists($this->client_charge_plan_id, $this->loader->planLimits[$this->client_master_id])) {
            $this->addError('clientChargePlanNo', Yii::t(
                'app',
                '選択された{CLIENT_NO_LABEL}に紐づく{CLIENT_CHARGE_PLAN_LABEL}がありません。',
                [
                    'CLIENT_NO_LABEL' => $this->getAttributeLabel('clientNo'),
                    'CLIENT_CHARGE_PLAN_LABEL' => $this->getAttributeLabel('clientChargePlanNo'),
                ]
            ));
            return;
        }

        // 変更の場合、現状の料金プラン割り当て数を減らす
        if (!$this->isNewRecord) {
            $oldClientId = $this->loader->jobIds2Plans[$this->id]['client_master_id'];
            $oldPlanId = $this->loader->jobIds2Plans[$this->id]['client_charge_plan_id'];
            $this->loader->plans[$oldClientId][$oldPlanId] -= 1;
        }

        if (isset($this->loader->plans[$this->client_master_id][$this->client_charge_plan_id])) {
            $this->loader->plans[$this->client_master_id][$this->client_charge_plan_id] += 1;
        } else {
            $this->loader->plans[$this->client_master_id][$this->client_charge_plan_id] = 1;
        }

        //変更を行った料金プラン割り当てのみ判別するため変更のあったもののみ値を保存
        $this->loader->changedPlans[$this->client_master_id][] = $this->client_charge_plan_id;
    }

    /**
     * 画像ファイル名存在チェック
     * @param $attribute
     */
    private function dispFileNameExistCheck($attribute)
    {
        if ($this->$attribute && isset($this->loader->fileNames2Ids[$this->$attribute])) {
            $mediaUploadAttributeName = 'media_upload_id_' . str_replace('dispFileName', '', $attribute);
            $this->$mediaUploadAttributeName = $this->loader->fileNames2Ids[$this->$attribute];
        } elseif (JmUtils::isEmpty($this->$attribute)){
            //既存原稿かつ、画像ファイル名がnullの場合、明示的に画像ファイルIDをnullにしておく
            //既存の画像ファイル名を削除できるようにするため
            $mediaUploadAttributeName = 'media_upload_id_' . str_replace('dispFileName', '', $attribute);
            $this->$mediaUploadAttributeName = null;
        } else {
            $this->addError($attribute, Yii::t('app', '{LABEL}に関して、画像ファイル名がありません。', ['LABEL' => $this->getAttributeLabel($attribute)]));
        }
    }

    /**
     * オプション項目の選択形式の選択肢チェック
     * @param $attribute
     */
    private function subsetCheck($attribute)
    {
        // 入力無しはエラーチェックをしない(必須項目の必須チェックは別で行っている)
        if (JmUtils::isEmpty($this->$attribute)) {
            return;
        }

        /** @var JobColumnSet $jobColumnSet */
        $jobColumnSet = Yii::$app->functionItemSet->job->items[$attribute];
        switch ($jobColumnSet->data_type) {
            case BaseColumnSet::DATA_TYPE_RADIO:
                // 単一選択の場合は入力がitemsに一つでも一致すればtrueを返す
                $result = false;
                foreach ($jobColumnSet->subsetItems as $i => $item) {
                    if ($this->$attribute == $item->subset_name) {
                        $result = true;
                        break;
                    }
                }
                break;
            case BaseColumnSet::DATA_TYPE_CHECK:
                // 複数選択の場合は一つでもitemsのどれかに一致しないものがあればfalseを返し、重複は削除する
                $result = true;
                $inputs = array_unique(explode(',', $this->$attribute));
                $items = ArrayHelper::getColumn($jobColumnSet->subsetItems, 'subset_name');
                foreach ($inputs as $input) {
                    if (!in_array($input, $items)) {
                        $result = false;
                        break;
                    }
                }
                $this->$attribute = implode(',', $inputs);
                break;
            default:
                // その他の場合はエラーチェックをしない(形式チェック等は別で行っている)
                return;
                break;
        }

        if (!$result) {
            $this->addError($attribute, Yii::t('app', '{LABEL}に関して、求人原稿項目設定で設定されている選択肢の中から選んでください。', ['LABEL' => $this->getAttributeLabel($attribute)]));
        }
    }

    /**
     * 取得した検索キーNoが存在しているか
     * 存在していれば、検索キーNoから登録用の検索キーIdを取得しているので注意
     * @param string $attribute
     */
    private function searchkeyCheck($attribute)
    {
        // 値がないものに関しては、エラーチェックをしない。(勤務地の必須チェックは別で行っているので問題ない)
        if (JmUtils::isEmpty($this->$attribute)) {
            return;
        }
        $nos = array_unique(explode('|', $this->$attribute));

        /** @var SearchkeyMaster $searchkeyMaster */
        $searchkeyMaster = $this->findSearchkeyMaster($attribute);
        $itemNos = $searchkeyMaster->itemNos;

        $result = true;
        $ids = [];
        foreach ($nos as $no) {
            if (isset($itemNos[$no])) {
                $ids[] = $itemNos[$no];
            } else {
                $result = false;
                break;
            }
        }

        if ($result === false) {
            $this->addError($attribute, Yii::t('app', '{LABEL}に関して、存在しないキーが含まれています。', ['LABEL' => $this->getAttributeLabel($attribute)]));
        } else {
            $this->{'_' . $attribute . 'Id'} = $ids;
        }
    }

    /**
     * 駅コード存在チェック
     * @param string $attribute
     */
    private function stationCodeCheck($attribute)
    {
        if (JmUtils::isEmpty($this->$attribute)) {
            return;
        }

        $no = str_replace('stationCd', '', $attribute);

        if ($this->{'transportTime' . $no} == '' || $this->{'transportType' . $no} == '') {
            $this->addError($attribute, Yii::t('app', '{STATION_CD}を入力する際は、{TIME}・{TYPE}は必須です。', [
                'STATION_CD' => $this->getAttributeLabel($attribute),
                'TIME' => $this->getAttributeLabel('transportTime' . $no),
                'TYPE' => $this->getAttributeLabel('transportType' . $no),
            ]));
        }

        $stationNos = $this->findSearchkeyMaster('station')->itemNos;
        if (!array_key_exists($this->$attribute, $stationNos)) {
            $this->addError($attribute, Yii::t('app', '{STATION_CD}に関して、存在しないキーが含まれています。', [
                'STATION_CD' => $this->getAttributeLabel($attribute),
            ]));
        }
    }

    /**
     * 給与検索キー存在チェック
     * @return bool
     */
    private function wageItemCheck()
    {
        if (JmUtils::isEmpty($this->maxWage)) {
            return;
        }

        /** @var SearchkeyMaster $searchkeyModel */
        $searchkeyModel = $this->findSearchkeyMaster('maxWage');

        $wageCateModels = $searchkeyModel->searchKeyModels;

        foreach ($wageCateModels as $cateModel) {
            if (array_key_exists($cateModel->id, $this->maxWage)) {
                $itemNames = ArrayHelper::getColumn($cateModel->wageItemValid, 'wage_item_name');

                if (!isset(array_flip($itemNames)[$this->maxWage[$cateModel->id]])) {
                    $this->addError(self::wageColumnRename($cateModel->id), Yii::t('app', '{LABEL}に関して、対応する金額がございません。', ['LABEL' => $cateModel->wage_category_name]));
                } else {
                    $items = array_filter($cateModel->wageItemValid, function (WageItem $item) use ($cateModel) {
                        return $this->maxWage[$cateModel->id] == $item->wage_item_name;
                    });
                    $this->_wageItem = array_merge($this->_wageItem, ArrayHelper::getColumn($items, 'id'));
                }
            }
        }
    }

    /**
     * 検索キーに関するモデルを保存
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->unlinkAll('jobDist', true);
        $this->unlinkAll('jobStation', true);
        $this->unlinkAll('jobWage', true);
        $this->unlinkAll('jobType', true);
        $this->unlinkAll('jobPref', true);
        for ($i = 1; $i <= 20; $i++) {
            $this->unlinkAll('jobSearchkeyItem' . $i, true);
        }
        $alterPost = [];
        if (isset($this->_distId)) {
            $alterPost['JobDist'] = ['itemIds' => $this->_distId];
        }
        // $this->_wageItem は初期値を持つため
        if (!JmUtils::isEmpty($this->_wageItem)) {
            $alterPost['JobWage'] = ['itemIds' => $this->_wageItem];
        }
        if (isset($this->_jobTypeSmallId)) {
            $alterPost['JobType'] = ['itemIds' => $this->_jobTypeSmallId];
        }
        for ($i = 1; $i <= 3; $i++) {
            if (isset($this->{'stationCd' . $i})) {
                $alterPost['JobStationInfo'][] = [
                    'station_id' => $this->{'stationCd' . $i},
                    'transport_type' => $this->{'transportType' . $i},
                    'transport_time' => $this->{'transportTime' . $i},
                ];
            }
        }
        for ($i = 1; $i <= 20; $i++) {
            if (isset($this->{'_searchkeyItem' . $i . 'Id'})) {
                $alterPost['JobSearchkeyItem' . $i] = ['itemIds' => $this->{'_searchkeyItem' . $i . 'Id'}];
            }
        }

        $this->saveRelationalModels($alterPost);

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * CSV一括登録で使われるattributeの配列を生成する
     * 修正する場合、models/manage/JobMasterSearch::csvAttributes
     * と配列が一致するように修正する必要があるので注意
     * @return array
     */
    public static function csvAttributes()
    {
        return ArrayHelper::merge(
            ['valid_chk'],
            self::csvJobAttributes(),
            self::csvSearchKeyAttributes(),
            ['import_site_job_id']
        );
    }

    /**
     * 求人情報のを取得する
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
                    $attributes[] = $name;
                    break;
                case 'client_master_id':
                    $attributes[] = 'clientNo';
                    break;
                case 'client_charge_plan_id':
                    $attributes[] = 'clientChargePlanNo';
                    break;
                case 'corpLabel':
                    break;
                case 'media_upload_id_1':
                case 'media_upload_id_2':
                case 'media_upload_id_3':
                case 'media_upload_id_4':
                case 'media_upload_id_5':
                    $attributes[] = 'dispFileName' . str_replace('media_upload_id_', '', $name);
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
                    $attributes[] = 'dist';
                    break;
                case 'job_station_info';
                    for ($i = 0; $i <= 2; $i++) {
                        $attributes[] = 'stationCd' . ($i + 1);
                        $attributes[] = 'transportType' . ($i + 1);
                        $attributes[] = 'transportTime' . ($i + 1);
                    }
                    break;
                case 'job_wage';
                    $attributes = array_merge($attributes, JobCsvRegister::wageColumn($searchKey));
                    break;
                case 'job_type';
                    $attributes[] = 'jobTypeSmall';
                    break;
                default:
                    $relationName = $searchKey->jobRelationName;
                    if (strpos($relationName, 'SearchkeyItem')) {
                        $attributes[] = str_replace('jobS', 's', $relationName);
                    } else {
                        $attributes[] = $relationName;
                    }
                    break;
            }
        }
        return $attributes;
    }

    /**
     * attributeに対応するSearchkeyMasterインスタンスを返す
     * @param $attribute
     * @return SearchkeyMaster|null
     */
    private function findSearchkeyMaster($attribute)
    {
        switch ($attribute) {
            case 'dist':
                return ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'pref');
                break;
            case 'jobTypeSmall':
                return ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'job_type_category');
                break;
            case 'maxWage':
                return ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'wage_category');
                break;
            default:
                if (strpos($attribute, 'station') !== false) {
                    return ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'station');
                }
                $items = [];
                for ($i = 1; $i <= 10; $i++) {
                    $items[] = 'searchkeyItem' . $i;
                }
                if (in_array($attribute, $items)) {
                    return ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'searchkey_category' . str_replace('searchkeyItem', '', $attribute));
                } else {
                    return ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, Inflector::underscore($attribute));
                }
                break;
        }
    }

    /**
     * SearchkeyMasterモデルから、給与検索キー用のattribute名の配列を返す。
     * @param $searchKey SearchkeyMaster
     * @return array
     */
    private static function wageColumn($searchKey)
    {
        /** @var WageCategory[] $cates */
        $cates = $searchKey->searchKeyModels;
        $columns = [];
        if ($cates) {
            foreach ($cates as $cate) {
                $columns[] = self::wageColumnRename($cate->id);
            }
        }
        return $columns;
    }

    /**
     * 給与検索キー用のカラム名を返す。
     * @param $id
     * @return string
     */
    public static function wageColumnRename($id)
    {
        return self::WAGE_NAME_COLUMN . $id;
    }

    /**
     * dispTypeNoを元にシナリオをセットする
     */
    private function initScenario()
    {
        $dispNo = $this->dispTypeNo();
        if ($dispNo) {
            $this->scenario = 'type' . $dispNo;
        }
    }

    /**
     * dispTypeNoを取得する
     * @return null
     */
    private function dispTypeNo()
    {
        return $this->loader->planNos2DispNos[$this->clientChargePlanNo] ?? null;
    }
}
