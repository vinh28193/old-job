<?php

namespace app\models\manage;

use app\common\Helper\JmUtils;
use app\modules\manage\models\Manager;
use proseeds\models\BaseModel;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use app\models\manage\searchkey\Pref;

/**
 * This is the model class for table "application_master".
 * application_masterのカラム
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $application_no
 * @property integer $job_master_id
 * @property string $name_sei
 * @property string $name_mei
 * @property string $kana_sei
 * @property string $kana_mei
 * @property string $sex
 * @property string $birth_date
 * @property integer $pref_id
 * @property string $address
 * @property string $tel_no
 * @property string $mail_address
 * @property integer $occupation_id
 * @property string $self_pr
 * @property string $created_at
 * @property string $option100
 * @property string $option101
 * @property string $option102
 * @property string $option103
 * @property string $option104
 * @property string $option105
 * @property string $option106
 * @property string $option107
 * @property string $option108
 * @property string $option109
 * @property integer $application_status_id
 * @property integer $carrier_type
 * @property string $application_memo
 * 名前関連getter
 * @property string $fullName
 * @property string $fullNameKana
 * 誕生日関連getter
 * @property integer $age
 * @property array $birthYearList
 * @property array $birthMonthList
 * @property array $birthDayList
 * @property string $birthDate
 * @property integer $birthDateYear
 * @property integer $birthDateMonth
 * @property integer $birthDateDay
 * カウント関連getter
 * @property integer $totalCount
 * @property integer $todayTotalCount
 * @property integer $todayPcCount
 * @property integer $todaySmartPhoneCount
 * relation
 * @property JobMaster $jobMaster
 * @property JobMasterBackup $jobMasterBackup
 * @property ClientMaster $clientMaster
 * @property CorpMaster $corpMaster
 * @property Occupation $occupation
 * @property Pref $pref
 * @property ClientChargePlan $clientChargePlan
 * relationもしくはnew modelを返すgetter
 * @property JobMaster $jobModel
 * @property ClientMaster $clientModel
 * @property CorpMaster $corpModel
 * @property ClientChargePlan $clientChargePlanModel
 * @property Pref $prefModel
 * @property Occupation $occupationModel
 * その他
 * @property string $prefName
 *
 * todo プロパティ追加、テーブル構造変更に合わせてrules等修正
 */
class ApplicationMaster extends BaseModel
{
    /** キャリア - PCorスマホ */
    const PC_CARRIER = 0;
    const SMART_PHONE_CARRIER = 1;
    /** 性別 - 男性or女性 */
    const SEX_MALE = 0;
    const SEX_FEMALE = 1;

    /** @var int $_totalCount 累計応募者数 */
    private $_totalCount;
    /** @var int $_todayPcCount 今日のPCでの応募者数 */
    private $_todayPcCount;
    /** @var int $todaySmartPhoneCount 今日のスマホでの応募者数数 */
    private $_todaySmartPhoneCount;
    /** @var int $_carrierArray 今日のアクセスのcarrier情報 */
    private $_todayCarrierArray;

    /** @var int $_todayPcCount 昨日のPCでの応募者数 */
    private $_yesterdayPcCount;
    /** @var int $todaySmartPhoneCount 昨日のスマホでの応募者数数 */
    private $_yesterdaySmartPhoneCount;
    /** @var int $_carrierArray 昨日のアクセスのcarrier情報 */
    private $_yesterdayCarrierArray;

    /** @var int 生年月日 */
    private $_birthDateYear;
    private $_birthDateMonth;
    private $_birthDateDay;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'application_master';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['application_memo', 'string'],
            ['application_status_id', 'required'],
            ['application_status_id', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(Yii::$app->functionItemSet->application->attributeLabels, [
            'application_memo' => Yii::t('app', '備考'),
        ]);
    }

    /**
     * 合計カウント数のgetter
     * 削除された原稿は除く
     * @return int|string
     */
    public function getTotalCount()
    {
        // private propertyに値がセットされていない場合
        if ($this->_totalCount === null) {
            $query = $this->find();
            $this->_totalCount = $this->addRoleQuery($query)->count();
        }
        return $this->_totalCount;
    }

    /**
     * 今日のPCアクセス数のgetter
     * @return int
     */
    public function getTodayPcCount()
    {
        if ($this->_todayPcCount === null) {
            $this->todayCarrierCount('pc');
        }
        return $this->_todayPcCount;
    }

    /**
     * 今日のスマホアクセス数のgetter
     * @return int
     */
    public function getTodaySmartPhoneCount()
    {
        if ($this->_todaySmartPhoneCount === null) {
            $this->todayCarrierCount('smartPhone');
        }
        return $this->_todaySmartPhoneCount;
    }

    /**
     * 今日の合計アクセス数のgetter
     * @return int
     */
    public function getTodayTotalCount()
    {
        return $this->todayPcCount + $this->todaySmartPhoneCount;
    }

    /**
     * キャリア別で今日のカウントをprivate変数にセットする
     * @param string $carrier
     */
    private function todayCarrierCount($carrier)
    {
        // 初めてカウントする場合
        if ($this->_todayCarrierArray === null) {
            $query = $this->find()->select('carrier_type')->where(['between', 'application_master.created_at', strtotime('today'), strtotime('tomorrow') - 1]);
            $this->_todayCarrierArray = $this->addRoleQuery($query)->column();
        }
        // キャリア別でカウント
        switch ($carrier) {
            case 'pc':
                $this->_todayPcCount = count(array_filter($this->_todayCarrierArray, function ($v) {
                    return $v == self::PC_CARRIER;
                }));
                break;
            case 'smartPhone':
                $this->_todaySmartPhoneCount = count(array_filter($this->_todayCarrierArray, function ($v) {
                    return $v == self::SMART_PHONE_CARRIER;
                }));
                break;
        }
    }

    /**
     * 昨日のPCアクセス数のgetter
     * @return int
     */
    public function getYesterdayPcCount()
    {
        if ($this->_yesterdayPcCount === null) {
            $this->yesterdayCarrierCount('pc');
        }
        return $this->_yesterdayPcCount;
    }

    /**
     * 昨日のスマホアクセス数のgetter
     * @return int
     */
    public function getYesterdaySmartPhoneCount()
    {
        if ($this->_yesterdaySmartPhoneCount === null) {
            $this->yesterdayCarrierCount('smartPhone');
        }
        return $this->_yesterdaySmartPhoneCount;
    }

    /**
     * 昨日の合計アクセス数のgetter
     * @return int
     */
    public function getYesterdayTotalCount()
    {
        return $this->yesterdayPcCount + $this->yesterdaySmartPhoneCount;
    }

    /**
     * キャリア別で昨日のカウントをprivate変数にセットする
     * @param string $carrier
     */
    private function yesterdayCarrierCount($carrier)
    {
        // 初めてカウントする場合
        if ($this->_yesterdayCarrierArray === null) {
            $query = $this->find()->select('carrier_type')->where(['between', 'application_master.created_at', strtotime('yesterday'), strtotime('today') - 1]);
            $this->_yesterdayCarrierArray = $this->addRoleQuery($query)->column();
        }
        // キャリア別でカウント
        switch ($carrier) {
            case 'pc':
                $this->_yesterdayPcCount = count(array_filter($this->_yesterdayCarrierArray, function ($v) {
                    return $v == self::PC_CARRIER;
                }));
                break;
            case 'smartPhone':
                $this->_yesterdaySmartPhoneCount = count(array_filter($this->_yesterdayCarrierArray, function ($v) {
                    return $v == self::SMART_PHONE_CARRIER;
                }));
                break;
        }
    }


    /**
     * queryにrole別のwhere条件を付与して返す
     * @param ActiveQuery $q
     * @return ActiveQuery
     */
    private function addRoleQuery(ActiveQuery $q)
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        if ($identity->myRole == Manager::CORP_ADMIN) {
            $q->joinWith(['jobMaster', 'clientMaster'])->andWhere(['client_master.corp_master_id' => $identity->corp_master_id]);
        } elseif ($identity->myRole == Manager::CLIENT_ADMIN) {
            $q->joinWith('jobMaster')->andFilterWhere(['job_master.client_master_id' => $identity->client_master_id]);
        }
        return $q;
    }

    /**
     * job_masterとのrelation
     * @return ActiveQuery
     */
    public function getJobMaster()
    {
        return $this->hasOne(JobMaster::className(), ['id' => 'job_master_id']);
    }

    /**
     * job_master_backupとのrelation
     * @return ActiveQuery
     */
    public function getJobMasterBackup()
    {
        return $this->hasOne(JobMasterBackup::className(), ['id' => 'job_master_id']);
    }

    /**
     * client_masterとのrelation
     * @return $this
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
     * @return \yii\db\ActiveQuery
     */
    public function getClientChargePlan()
    {
        return $this->hasOne(ClientChargePlan::className(), ['id' => 'client_charge_plan_id'])->via('jobMaster');
    }

    /**
     * 都道府県
     * pref_idリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getPref()
    {
        return $this->hasOne(Pref::className(), ['id' => 'pref_id']);
    }

    /**
     * 属性
     * occupationとリレーション
     * @return ActiveQuery
     */
    public function getOccupation()
    {
        return $this->hasOne(Occupation::className(), ['id' => 'occupation_id']);
    }

    /**
     * 状況
     * application_statusとリレーション
     * @return ActiveQuery
     */
    public function getApplicationStatus()
    {
        return $this->hasOne(ApplicationStatus::className(), ['id' => 'application_status_id', 'tenant_id' => 'tenant_id']);
    }

    /**
     * 応募管理履歴
     * application_response_log
     * @return ActiveQuery
     */
    public function getApplicationResponseLog()
    {
        return $this->hasMany(ApplicationResponseLog::className(), ['application_id' => 'id']);
    }

    /**
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return JobMaster
     */
    public function getJobModel()
    {
        if ($this->jobMaster) {
            return $this->jobMaster;
        } elseif ($this->jobMasterBackup) {
            return $this->jobMasterBackup;
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
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return ClientChargePlan
     */
    public function getClientChargePlanModel()
    {
        return $this->clientChargePlan ?: new ClientChargePlan();
    }

    /**
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return ClientChargePlan
     */
    public function getPrefModel()
    {
        return $this->pref ?: new Pref();
    }

    /**
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return ClientChargePlan
     */
    public function getOccupationModel()
    {
        return $this->occupation ?: new Occupation();
    }

    /**
     * application_masterのpref_idに対する都道府県名取得
     * @return \yii\db\ActiveQuery
     */
    public function getPrefName()
    {
        // prefのpref_nameを取得
        return (is_object($this->pref)) ? $this->pref->pref_name : '';
    }

    /**
     * @return mixed|null
     */
    public function getBirthDateYear()
    {
        if ($this->_birthDateYear) {
            return $this->_birthDateYear;
        }
        return $this->birth_date ? ArrayHelper::getValue(explode('-', $this->birth_date), 0) : null;
    }

    /**
     * @return mixed|null
     */
    public function getBirthDateMonth()
    {
        if ($this->_birthDateMonth) {
            return $this->_birthDateMonth;
        }
        return $this->birth_date ? ArrayHelper::getValue(explode('-', $this->birth_date), 1) : null;
    }

    /**
     * @return mixed|null
     */
    public function getBirthDateDay()
    {
        if ($this->_birthDateDay) {
            return $this->_birthDateDay;
        }
        return $this->birth_date ? sprintf('%02d', ArrayHelper::getValue(explode('-', $this->birth_date), 2)) : null;
    }

    /**
     * @param $value
     */
    public function setBirthDateYear($value)
    {
        $this->_birthDateYear = $value;
    }

    /**
     * @param $value
     */
    public function setBirthDateMonth($value)
    {
        $this->_birthDateMonth = $value;
    }

    /**
     * @param $value
     */
    public function setBirthDateDay($value)
    {
        $this->_birthDateDay = $value;
    }

    /**
     * 生年月日から年齢を取得
     * @return \yii\db\ActiveQuery
     */
    public function getAge()
    {
        // 生年月日から年齢を取得
        $birthDate = $this->birth_date;
        $datetime1 = new \DateTime($birthDate);
        $datetime2 = new \DateTime();
        $diff = $datetime1->diff($datetime2);
        return $diff->y;
    }

    /**
     * 年のリストを取得する
     * @return array
     */
    public static function getBirthYearList()
    {
        // 16歳から
        $startYear = date('Y') - 16;
        // 65歳まで
        $endYear = date('Y') - 65;

        $birthYearArray = [];

        for ($i = $startYear; $i >= $endYear; $i--) {
            $birthYearArray[$i] = $i;
        }

        return $birthYearArray;
    }

    /**
     * 月のリストを取得する
     * @return array
     */
    public static function getBirthMonthList()
    {
        $startMonth = 1;
        $endMonth = 12;
        $birthMonthArray = [];

        for ($i = $startMonth; $i <= $endMonth; $i++) {
            $value = sprintf('%02d', $i);
            $birthMonthArray[$value] = $value;
        }

        return $birthMonthArray;
    }

    /**
     * 日のリストを取得する
     * @return array
     */
    public static function getBirthDayList()
    {
        $startDay = 1;
        $endDay = 31;
        $birthDayArray = [];

        for ($i = $startDay; $i <= $endDay; $i++) {
            $value = sprintf('%02d', $i);
            $birthDayArray[$value] = $value;
        }

        return $birthDayArray;
    }

    /**
     * 氏名を一つにあわせて返す
     * @return string
     */
    public function getFullName()
    {
        return JmUtils::removeWhitespace($this->name_sei) . ' ' . JmUtils::removeWhitespace($this->name_mei);
    }

    /**
     * 氏名を一つにあわせて返す
     * @return string
     */
    public function getFullNameKana()
    {
        return JmUtils::removeWhitespace($this->kana_sei) . ' ' . JmUtils::removeWhitespace($this->kana_mei);
    }

    /**
     * @return array
     */
    public function fields()
    {
        return parent::fields() + ['fullName' => 'fullName', 'fullNameKana' => 'fullNameKana'];
    }

    public function getFormatTable()
    {
        return [
            'carrier_type' => [
                self::PC_CARRIER => Yii::t('app', 'PC'),
                self::SMART_PHONE_CARRIER => Yii::t('app', 'スマートフォン'),
            ],
        ];
    }

    /**
     * 誕生日検索用の文字列を生成する
     * @return string
     */
    public function getBirthDate()
    {
        if (($this->birthDateYear != 'all' && !empty($this->birthDateYear)) || ($this->birthDateMonth != 'all' && !empty($this->birthDateMonth)) || ($this->birthDateDay != 'all' && !empty($this->birthDateDay))) {
            // 生年月日を検索するために、条件を生成
            $birthYear = ($this->birthDateYear != 'all') ? $this->birthDateYear : '%';
            $birthMonth = ($this->birthDateMonth != 'all') ? $this->birthDateMonth : '%';
            $birthDay = ($this->birthDateDay != 'all') ? $this->birthDateDay : '%';
            $birthDate = sprintf('%s-%s-%s', $birthYear, $birthMonth, $birthDay);
        } else {
            $birthDate = '';
        }
        return $birthDate;
    }
}
