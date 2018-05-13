<?php

namespace app\models\manage;

use app\common\ColumnSet;
use app\models\manage\searchkey\JobDist;
use app\models\manage\searchkey\JobPref;
use app\models\manage\searchkey\JobSearchkeyItem;
use app\models\manage\searchkey\JobSearchkeyItem1;
use app\models\manage\searchkey\JobSearchkeyItem2;
use app\models\manage\searchkey\JobSearchkeyItem3;
use app\models\manage\searchkey\JobSearchkeyItem4;
use app\models\manage\searchkey\JobSearchkeyItem5;
use app\models\manage\searchkey\JobSearchkeyItem6;
use app\models\manage\searchkey\JobSearchkeyItem7;
use app\models\manage\searchkey\JobSearchkeyItem8;
use app\models\manage\searchkey\JobSearchkeyItem9;
use app\models\manage\searchkey\JobSearchkeyItem10;
use app\models\manage\searchkey\JobSearchkeyItem11;
use app\models\manage\searchkey\JobSearchkeyItem12;
use app\models\manage\searchkey\JobSearchkeyItem13;
use app\models\manage\searchkey\JobSearchkeyItem14;
use app\models\manage\searchkey\JobSearchkeyItem15;
use app\models\manage\searchkey\JobSearchkeyItem16;
use app\models\manage\searchkey\JobSearchkeyItem17;
use app\models\manage\searchkey\JobSearchkeyItem18;
use app\models\manage\searchkey\JobSearchkeyItem19;
use app\models\manage\searchkey\JobSearchkeyItem20;
use app\models\manage\searchkey\JobStationInfo;
use app\models\manage\searchkey\JobType;
use app\models\manage\searchkey\JobWage;
use app\modules\manage\models\Manager;
use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\JobAccessRecommend;
use yii\helpers\Inflector;
use yii\validators\DateValidator;

/**
 * This is the model class for table "job_master".
 *
 * @property integer            $id
 * @property integer            $tenant_id
 * @property integer            $job_no
 * @property integer            $client_master_id
 * @property string             $corp_name_disp
 * @property string             $job_pr
 * @property string             $main_copy
 * @property string             $job_comment
 * @property integer            $media_upload_id_1
 * @property integer            $media_upload_id_2
 * @property integer            $media_upload_id_3
 * @property integer            $media_upload_id_4
 * @property string             $job_type_text
 * @property string             $work_place
 * @property string             $station
 * @property string             $transport
 * @property string             $wage_text
 * @property string             $requirement
 * @property string             $conditions
 * @property string             $holidays
 * @property string             $work_period
 * @property string             $work_time_text
 * @property string             $application
 * @property string             $application_tel_1
 * @property string             $application_tel_2
 * @property string             $application_mail
 * @property string             $application_place
 * @property string             $application_staff_name
 * @property string             $agent_name
 * @property string             $disp_start_date
 * @property string             $disp_end_date
 * @property string             $created_at
 * @property integer            $valid_chk
 * @property string             $job_search_number
 * @property string             $job_pict_text_3
 * @property string             $job_pict_text_4
 * @property string             $map_url
 * @property string             $mail_body
 * @property string             $updated_at
 * @property string             $job_pict_text_5
 * @property integer            $media_upload_id_5
 * @property string             $main_copy2
 * @property string             $job_pr2
 * @property string             $option100
 * @property string             $option101
 * @property string             $option102
 * @property string             $option103
 * @property string             $option104
 * @property string             $option105
 * @property string             $option106
 * @property string             $option107
 * @property string             $option108
 * @property string             $option109
 * @property integer            $import_site_job_id
 * @property integer            $client_charge_plan_id
 * @property integer            $job_review_status_id
 * @property integer            $sample_pict_flg_1
 * @property integer            $sample_pict_flg_2
 * @property integer            $sample_pict_flg_3
 * @property integer            $sample_pict_flg_4
 * @property integer            $sample_pict_flg_5
 * @property integer            $disp_type_sort
 * hasOne relation
 * @property ClientChargePlan   $clientChargePlan
 * @property ClientMaster       $clientMaster
 * @property JobReviewStatus    $jobReviewStatus
 * @property jobAccessRecommend $jobAccessRecommend
 * @property MediaUpload        $mediaUpload1
 * @property MediaUpload        $mediaUpload2
 * @property MediaUpload        $mediaUpload3
 * @property MediaUpload        $mediaUpload4
 * @property MediaUpload        $mediaUpload5
 * hasMany relation(汎用検索キーは省略しています)
 * @property JobDist[]          $jobDist
 * @property JobStationInfo[]   $jobStation
 * @property JobWage[]          $jobWage
 * @property JobType[]          $jobType
 * @property JobPref[]          $jobPref
 * @property JobReviewHistory   $jobReviewHistory
 * model getter todo 検索キーの値はわざわざmodelに入れずproperty作ってそこに入れる方法も検討
 * @property ClientMaster       $clientModel
 * @property JobDist            $jobDistModel
 * @property JobStationInfo[]   $jobStationModel
 * @property JobWage            $jobWageModel
 * @property JobType            $jobTypeModel
 * @property JobPref            $jobPrefModel
 * @property JobAccessRecommend $jobAccessRecommendModel
 * その他
 * @property int                $corpMasterId
 *
 */
class JobMaster extends BaseModel
{
    /** 配列の先頭 */
    const ARRAY_FIRST = 0;
    /** 状態 - 有効 or 無効 */
    const FLAG_VALID   = 1;
    const FLAG_INVALID = 0;
    /** シナリオdispTypeNo=1～3 */
    const TYPE_1 = 'type1';
    const TYPE_2 = 'type2';
    const TYPE_3 = 'type3';
    /** シナリオajaxValidation */
    const SCENARIO_AJAX_VALIDATION = 'ajaxValidation';
    /** 画像が登録されていないときの代わりの画像ファイルパス(管理画面用) */
    const NO_IMAGE_PATH = '/pict/dummy.jpg';

    /** @var int コピー元id */
    public $sourceId;
    /** @var int getter用 */
    private $_corpMasterId;
    private $_jobDistModel;
    private $_jobStationModel;
    private $_jobWageModel;
    private $_jobTypeModel;
    private $_jobSearchkeyItemModel;

    /** @var JobReviewStatus 審査ステータス用 */
    private $_jobReviewStatus = null;

    /**
     * 動的に汎用検索キーrelationのgetterを生成する
     * @param string $name
     * @return mixed|\yii\db\ActiveQuery
     */
    public function __get($name)
    {
        if (!preg_match('/jobSearchkeyItem\d+Model/', $name)) {
            return parent::__get($name);
        }
        return $this->getJobSearchkeyItemModel(str_replace('Model', '', $name));
    }

    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'job_master';
    }

    /**
     * @inheritdoc
     * 保存前処理
     * 新規登録時、求人原稿番号をMAX+1して挿入しています。
     * @param boolean $insert INSERT判別
     * @return boolean
     */
    public function beforeSave($insert)
    {
        $this->formatCheckBoxOptions();
        if (parent::beforeSave($insert)) {
            $this->disp_type_sort = $this->clientChargePlan->dispType->disp_type_no;
            // 新規登録の時のみの処理
            if ($insert) {
//                $this->job_no = self::find()->max('job_no') + 1;
                $this->job_no = max(self::find()->max('job_no') + 1, JobMasterBackup::find()->max('job_no') + 1);
                // 運営元以外かつ有効日数付きプランの場合はそれらを元に終了日をセットする
                if ($this->clientChargePlan->period && Yii::$app->user->identity->myRole != Manager::OWNER_ADMIN) {
                    $this->disp_end_date = strtotime('+' . ($this->clientChargePlan->period - 1) . 'days', $this->disp_start_date);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * loadAuthParamを追加
     * @return bool
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate() && $this->loadAuthParam()) {
            return true;
        }
        return false;
    }

    /**
     * 入力がチェックボックスなoptionの値が配列だった時に文字列に変換する
     */
    public function formatCheckBoxOptions()
    {
        /** @var ColumnSet $columnSet */
        $columnSet = Yii::$app->functionItemSet->job;
        foreach ($columnSet->optionAttributes as $item) {
            if(is_array($this->{$item})){
                $this->{$item} = implode(',', $this->{$item});
            }
        }
    }

    /**
     * ルールの設定。
     * @return array ルール設定
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
        return ArrayHelper::merge(Yii::$app->functionItemSet->job->rules, [
            [[
                'created_at',
                'updated_at',
                'corpMasterId',
                'client_master_id',
                'disp_type_sort',
                'media_upload_id_1',
                'media_upload_id_2',
                'media_upload_id_3',
                'media_upload_id_4',
                'media_upload_id_5',
                'job_review_status_id',
            ], 'number'],
            [
                'disp_start_date', 'date', 'timestampAttribute' => 'disp_start_date',
                'min' => '1920/01/01', 'tooSmall' => Yii::t('app', '{attribute}は{min}以降の日付にしてください.'),
                'max' => '2037/12/31', 'tooBig' => Yii::t('app', '{attribute}は{max}以前の日付にしてください.'),
            ],
            [
                'disp_end_date', 'date', 'timestampAttribute' => 'disp_end_date', 'when' => $dateWhen,
                'min' => '1920/01/01', 'tooSmall' => Yii::t('app', '{attribute}は{min}以降の日付にしてください.'),
                'max' => '2037/12/31', 'tooBig' => Yii::t('app', '{attribute}は{max}以前の日付にしてください.'),
            ],
            ['disp_end_date', 'compare', 'compareAttribute' => 'dispStartDate', 'operator' => '>=',
                'message' => Yii::t('app', '{attribute}は{compareAttribute}より後の日付にしてください.'), 'when' => $compareWhen],
            [['valid_chk', 'disp_start_date', 'corpMasterId', 'client_master_id', 'client_charge_plan_id', 'job_review_status_id'], 'required'],
            ['valid_chk', 'boolean'],
            [['job_pict_text_3', 'job_pict_text_4', 'job_pict_text_5'], 'string', 'max' => 255],
            ['client_charge_plan_id', function ($attribute, $params) {
                if ($this->isPlanOver()) {
                    $this->addError($attribute, Yii::t('app', 'プランの上限数を超過してしまいます'));
                }
            }],
        ]);
    }

    /**
     * 要素のラベル名を設定。
     * function_item_setテーブルから名前を取得し、カラムごとに適用しています。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(Yii::$app->functionItemSet->job->attributeLabels, [
            'corpMasterId'    => Yii::$app->functionItemSet->job->items['corpLabel']->label,
            'valid_chk'       => Yii::t('app', '状態'),
            'jobStation'      => Yii::$app->searchKey->label('job_station_info'),
            'job_pict_text_3' => Yii::t('app', '画像3テキスト'),
            'job_pict_text_4' => Yii::t('app', '画像4テキスト'),
            'job_pict_text_5' => Yii::t('app', '画像5テキスト'),
            'disp_type_sort'    => Yii::t('app', 'おすすめ順'),
            'dispStartDate'   => Yii::$app->functionItemSet->job->items['disp_start_date']->label,
            'job_review_status_id' => JobReviewStatus::attributeLabel(),
        ]);
    }

    //-----------------------------------------------
    // リレーション設定
    //-----------------------------------------------

    /**
     * 掲載企業テーブルへのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getClientMaster()
    {
        return $this->hasOne(ClientMaster::className(), ['id' => 'client_master_id']);
    }

    /**
     * 掲載プランリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getClientChargePlan()
    {
        return $this->hasOne(ClientChargePlan::className(), ['id' => 'client_charge_plan_id']);
    }

    /**
     * 勤務地リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobDist()
    {
        return $this->hasMany(JobDist::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }

    /**
     * 路線リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobStation()
    {
        return $this->hasMany(JobStationInfo::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }

    /**
     * 給与リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobWage()
    {
        return $this->hasMany(JobWage::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }

    /**
     * 職種リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobType()
    {
        return $this->hasMany(JobType::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }

    /**
     * 汎用キーリレーション群
     * @return \yii\db\ActiveQuery
     */
    public function getJobSearchkeyItem1()
    {
        return $this->hasMany(JobSearchkeyItem1::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem2()
    {
        return $this->hasMany(JobSearchkeyItem2::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem3()
    {
        return $this->hasMany(JobSearchkeyItem3::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem4()
    {
        return $this->hasMany(JobSearchkeyItem4::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem5()
    {
        return $this->hasMany(JobSearchkeyItem5::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem6()
    {
        return $this->hasMany(JobSearchkeyItem6::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem7()
    {
        return $this->hasMany(JobSearchkeyItem7::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem8()
    {
        return $this->hasMany(JobSearchkeyItem8::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem9()
    {
        return $this->hasMany(JobSearchkeyItem9::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem10()
    {
        return $this->hasMany(JobSearchkeyItem10::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem11()
    {
        return $this->hasMany(JobSearchkeyItem11::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem12()
    {
        return $this->hasMany(JobSearchkeyItem12::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem13()
    {
        return $this->hasMany(JobSearchkeyItem13::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem14()
    {
        return $this->hasMany(JobSearchkeyItem14::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem15()
    {
        return $this->hasMany(JobSearchkeyItem15::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem16()
    {
        return $this->hasMany(JobSearchkeyItem16::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem17()
    {
        return $this->hasMany(JobSearchkeyItem17::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem18()
    {
        return $this->hasMany(JobSearchkeyItem18::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem19()
    {
        return $this->hasMany(JobSearchkeyItem19::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }
    public function getJobSearchkeyItem20()
    {
        return $this->hasMany(JobSearchkeyItem20::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }

    /**
     * 都道府県リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobPref()
    {
        return $this->hasMany(JobPref::className(), ['job_master_id' => 'id', 'tenant_id' => 'tenant_id']);
    }

    /**
     * 審査履歴リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobReviewHistory()
    {
        return $this->hasMany(JobReviewHistory::className(), ['job_master_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobAccessRecommend()
    {
        return $this->hasOne(JobAccessRecommend::className(), ['job_master_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaUpload1()
    {
        return $this->hasOne(MediaUpload::className(), ['id' => 'media_upload_id_1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaUpload2()
    {
        return $this->hasOne(MediaUpload::className(), ['id' => 'media_upload_id_2']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaUpload3()
    {
        return $this->hasOne(MediaUpload::className(), ['id' => 'media_upload_id_3']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaUpload4()
    {
        return $this->hasOne(MediaUpload::className(), ['id' => 'media_upload_id_4']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaUpload5()
    {
        return $this->hasOne(MediaUpload::className(), ['id' => 'media_upload_id_5']);
    }

    /**
     * JobMasterに保存されている、media_upload_idから直接
     * イメージサーバーの画像のパスを取得する。
     * @param integer $id
     * @return string
     */
    public function getJobImagePath($id)
    {
        /** @var null|MediaUpload $mediaUpload */
        $mediaUpload = $this->{'mediaUpload' . $id};
        // 画像が取得でき、その原稿で使える画像の場合pathを、そうでなければ空文字を返す
        return (
            $mediaUpload !== null
            && (!isset($mediaUpload->client_master_id) || $mediaUpload->client_master_id == $this->client_master_id)
        ) ? $mediaUpload->srcUrl() : '';
    }

    /**
     * 掲載企業リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return ClientMaster
     */
    public function getClientModel()
    {
        return $this->clientMaster ?: new ClientMaster();
    }

    /**
     * @return JobAccessRecommend
     */
    public function getJobAccessRecommendModel()
    {
        return $this->jobAccessRecommend ?: new JobAccessRecommend();
    }

    /**
     * 勤務地リレーショナルデータを保持したモデルを返す
     * @return JobDist
     */
    public function getJobDistModel()
    {
        if (!$this->_jobDistModel) {
            $this->_jobDistModel = new JobDist();
            $this->_jobDistModel->itemIds = ArrayHelper::getColumn($this->jobDist, 'dist_id');
        }

        return $this->_jobDistModel;
    }

    /**
     * 路線駅リレーショナルデータを保持したモデルを返す
     * 必ず3つ返す
     * @return JobStationInfo
     */
    public function getJobStationModel()
    {
        if (!$this->_jobStationModel) {
            $count = count((array)$this->jobStation);
            switch ($count) {
                case 0:
                    $this->_jobStationModel = [new JobStationInfo(), new JobStationInfo(), new JobStationInfo()];
                    break;
                case 1:
                    $this->_jobStationModel = array_merge((array)$this->jobStation, [new JobStationInfo(), new JobStationInfo()]);
                    break;
                case 2:
                    $this->_jobStationModel = array_merge((array)$this->jobStation, [new JobStationInfo()]);
                    break;
                default:
                    $this->_jobStationModel = $this->jobStation;
            }
        }
        return $this->_jobStationModel;
    }

    /**
     * 給与リレーショナルデータを保持したモデルを返す
     * @return JobWage
     */
    public function getJobWageModel()
    {
        if (!$this->_jobWageModel) {
            $this->_jobWageModel = new JobWage();
            $this->_jobWageModel->itemIds = ArrayHelper::getColumn($this->jobWage, 'wage_item_id');
        }
        return $this->_jobWageModel;
    }

    /**
     * 職種リレーショナルデータを保持したモデルを返す
     * @return JobType
     */
    public function getJobTypeModel()
    {
        if (!$this->_jobTypeModel) {
            $this->_jobTypeModel = new JobType();
            $this->_jobTypeModel->itemIds = ArrayHelper::getColumn($this->jobType, 'job_type_small_id');
        }
        return $this->_jobTypeModel;
    }

    /**
     * 汎用リレーショナルデータを保持したモデルを返す
     * @param $name
     * @return JobSearchkeyItem
     * @throws \yii\base\InvalidConfigException
     */
    public function getJobSearchkeyItemModel($name)
    {
        if (!isset($this->_jobSearchkeyItemModel[$name])) {
            $this->_jobSearchkeyItemModel[$name] = Yii::createObject(['class' => SearchkeyMaster::MODEL_BASE_PATH . Inflector::camelize($name)]);
            $this->_jobSearchkeyItemModel[$name]->itemIds = ArrayHelper::getColumn((array)$this->$name, 'searchkey_item_id');
        }
        return $this->_jobSearchkeyItemModel[$name];
    }

    /**
     * 審査ステータスモデルを返す
     * @return JobReviewStatus
     */
    public function getJobReviewStatus()
    {
        if ($this->_jobReviewStatus === null) {
            $this->_jobReviewStatus = new JobReviewStatus();
        }
        $this->_jobReviewStatus->id = $this->job_review_status_id;
        return $this->_jobReviewStatus;
    }

    /* ------------- リレーションここまで ------------- */

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
     * 求人原稿関連モデルの保存
     * @param array $post POSTデータ
     */
    public function saveRelationalModels($post)
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        $this->tenant_id = $identity->tenant_id;

        //職種
        $jobType = new JobType();
        if ($jobType->load($post)) {
            $this->link('jobType', $jobType);
        };
        //勤務地
        $jobDist = new JobDist();
        if ($jobDist->load($post)) {
            $this->link('jobDist', $jobDist);
            $jobPref = Yii::createObject(['class' => JobPref::className(), 'itemIds' => $jobDist->prefIds]);
            $this->link('jobPref', $jobPref);
        };
        //給与
        $jobWage = new JobWage();
        if ($jobWage->load($post)) {
            // 選択されていない入力を排除
            $jobWage->itemIds = array_filter($jobWage->itemIds);
            if ($jobWage->itemIds) {
                $this->link('jobWage', $jobWage);
            }
        };
        //路線
        $jobStations = $this->jobStationModel;
        self::loadMultiple($jobStations, $post);
        foreach ($jobStations as $jobStation) {
            if ($jobStation->station_id) {
                $this->link('jobStation', $jobStation);
            }
        }
        // 汎用
        for ($i = 1; $i <= 20; $i++) {
            /** @var JobSearchkeyItem $model */
            $model = Yii::createObject(['class' => JobSearchkeyItem::className() . $i]);
            if (!$model->load($post)) {
                continue;
            };
            $this->link('jobSearchkeyItem' . $i, $model);
        }
    }

    /**
     * 登録時に使う掲載プラン別のシナリオを出力する
     * disp_type_noは1～3以外は入らない想定
     * @return string
     */
    public function getTypeScenario()
    {
        return 'type' . $this->clientChargePlan->dispType->disp_type_no;
    }

    /**
     * 権限を元に検索条件をロードする
     * @return bool
     */
    protected function loadAuthParam()
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
                $this->client_master_id = $identity->client_master_id;
                return true;
                break;
            default :
                return false;
                break;
        }
    }

    /**
     * プランが上限を超えているかどうかを調べる
     * @return bool
     */
    public function isPlanOver()
    {
        $limit = ClientCharge::find()->select('limit_num')->where(['client_charge_plan_id' => $this->client_charge_plan_id, 'client_master_id' => $this->client_master_id])->scalar();
        if (!$limit) {
            return false;
        }
        $count = self::find()->where(['client_master_id' => $this->client_master_id, 'client_charge_plan_id' => $this->client_charge_plan_id])->count();
        if ($this->getOldAttribute('client_charge_plan_id') == $this->client_charge_plan_id) {
            $count = $count - 1;
        }
        return $count >= $limit;
    }

    /**
     * @return string
     */
    public function getDispStartDate()
    {
        $validator = new DateValidator();
        if (is_int($this->disp_start_date)) {
            return $this->disp_start_date;
        } elseif ($validator->validate($this->disp_start_date)) {
            return Yii::$app->formatter->asTimestamp($this->disp_start_date);
        } else {
            return false;
        }
    }

    /**
     * loadメソッド拡張
     * 入力により値を変更するカラムがあるため。
     * @param array $data
     * @param string $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            $this->job_review_status_id = JobReviewStatus::jobRegisterReviewStatus(Yii::$app->user->identity->myRole);
            return true;
        }
        return false;
    }

    /**
     * 求人が審査対象かどうかのチェック
     * @return boolean
     */
    public function isReview()
    {
        // 審査対象ステータスを取得
        $role = Yii::$app->user->identity->myRole;
        $corpReviewFlg = $this->clientMaster->corpMaster->corp_review_flg;
        $reviewStatuses = JobReviewStatus::reviewTargetStatusesByRole($role, $corpReviewFlg);

        // 審査対象かどうかを返す
        return in_array($this->job_review_status_id, $reviewStatuses);
    }

    /**
     * 表示画面で審査が行われるべきかどうかをチェック
     * @return boolean
     */
    public function useReview()
    {
        return Yii::$app->tenant->tenant->review_use && !$this->isNewRecord;
    }

    /**
     * 確認メッセージの切り分け用
     * 代理店と運営元のみを対象とするため別メソッドに切り出し。
     * @return bool
     */
    public function isNotReviewer():bool
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        return ($this->job_review_status_id == JobReviewStatus::STEP_CORP_REVIEW  && !$identity->isCorp()) ||
        ($this->job_review_status_id == JobReviewStatus::STEP_OWNER_REVIEW  && !$identity->isOwner());
    }
}