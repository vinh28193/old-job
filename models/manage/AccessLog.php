<?php

namespace app\models\manage;

use app\common\Helper\JmUtils;
use app\models\queries\AccessLogQuery;
use proseeds\models\BaseModel;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Url;

/**
 * This is the model class for table "access_log".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $accessed_at
 * @property integer $job_master_id
 * @property integer $application_master_id
 * @property integer $carrier_type
 * @property string $access_url
 * @property string $access_browser
 * @property string $access_user_agent
 * @property string $access_referrer
 *
 * @property JobMaster $jobMaster
 * @property ApplicationMaster $applicationMaster
 * @property ClientMaster $clientMaster
 * @property CorpMaster $corpMaster
 *
 * @property JobMaster $jobModel
 * @property ApplicationMaster $applicationModel
 * @property ClientMaster $clientModel
 * @property CorpMaster $corpModel
 *
 */
class AccessLog extends BaseModel
{
    /**
     * キャリア識別
     */
    const PC_CARRIER = 0;
    const SMART_PHONE_CARRIER = 1;

    /**
     * job_noのデータがない場合、0を挿入
     */
    const JOB_NO_NULL = 0;

    /** @var int 代理店ID. */
    public $corpMasterId;

    /** @var int 掲載企業ID. */
    public $clientMasterId;

    /** @var int PV数. */
    public $pvCount;

    /** @var int $_jobNo 求人原稿ナンバー */
    private $_jobNo;

    /** access_user_agentの最大入力文字数 **/
    const MAX_LENGTH_USER_AGENT = 255;

    /** access_referrerの最大入力文字数 **/
    const MAX_LENGTH_REFERRER = 255;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'access_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accessed_at','carrier_type'], 'required'],
            [['tenant_id', 'accessed_at', 'job_master_id', 'carrier_type'], 'integer'],
            [['access_url', 'access_browser', 'access_user_agent', 'access_referrer'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'accessDate' => Yii::t('app', 'アクセス日'),
            'id' => Yii::t('app', 'ID'),
        ];
    }

    /**
     * アクセス機器リストを取得する。
     * @return array 状態リスト
     */
    public static function getCarrierTypeList()
    {
        return [
            self::PC_CARRIER => Yii::t('app', 'PC'),
            self::SMART_PHONE_CARRIER => Yii::t('app', 'スマホ'),
        ];
    }

    /**
     * application_masterとのrelation
     * @return ActiveQuery
     */
    public function getApplicationMaster()
    {
        return $this->hasOne(ApplicationMaster::className(), ['id' => 'application_master_id']);
    }

    /**
     * job_masterとのrelation
     * @return ActiveQuery
     */
    public function getJobMaster()
    {
        return $this->hasOne(JobMaster::className(), ['id' => 'job_master_id'])->select(['id', 'tenant_id', 'job_no', 'client_master_id']);
    }

    /**
     * client_masterとのrelation
     * @return ActiveQuery
     */
    public function getClientMaster()
    {
        return $this->hasOne(ClientMaster::className(), ['id' => 'client_master_id'])->via('jobMaster');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCorpMaster()
    {
        return $this->hasOne(CorpMaster::className(), ['id' => 'corp_master_id'])->via('clientMaster');
    }

    /**
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return ApplicationMaster
     */
    public function getApplicationModel()
    {
        if ($this->applicationMaster) {
            return $this->applicationMaster;
        }
        return new ApplicationMaster();
    }

    /**
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return JobMaster
     */
    public function getJobModel()
    {
        if ($this->jobMaster) {
            return $this->jobMaster;
        }
        return new JobMaster();
    }

    /**
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return ClientMaster
     */
    public function getClientModel()
    {
        return $this->clientMaster ?: new ClientMaster();
    }

    /**
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return CorpMaster
     */
    public function getCorpModel()
    {
        return $this->corpMaster ?: new CorpMaster();
    }

    /**
     * job_noのgetter
     * @return int
     */
    public function getJobNo()
    {
        if (JmUtils::isEmpty($this->_jobNo) && $this->jobMaster) {
            $this->_jobNo = $this->jobMaster->job_no;
        }
        return $this->_jobNo;
    }

    /**
     * job_noのsetter
     * job_noを元にrelationもpopulateし、job_master_idも代入する
     */
    public function setJobNo($v)
    {
        $this->_jobNo = $v;
        if (JmUtils::isEmpty($v)) {
            $this->job_master_id = null;
            return;
        }
        $jobMaster = JobMaster::find()->select(['id', 'tenant_id', 'job_no', 'client_master_id'])->where(['job_no' => $v])->one();
        $this->populateRelation('jobMaster', $jobMaster);
        $this->job_master_id = $this->jobMaster->id ?? self::JOB_NO_NULL;
    }

    /**
     * @return AccessLogQuery
     */
    public static function find()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject(AccessLogQuery::className(), [get_called_class()]);
    }

    /**
     * @return AccessLogQuery
     */
    public static function authFind()
    {
        /** @var AccessLogQuery $query */
        $query = Yii::createObject(AccessLogQuery::className(), [get_called_class()]);
        return $query->addAuthQuery();
    }

}
