<?php

namespace models\manage;

use app\models\manage\ApplicationMaster;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\JobMaster;
use app\models\manage\Occupation;
use app\models\manage\searchkey\Pref;
use tests\codeception\fixtures\PrefFixture;
use tests\codeception\unit\JmTestCase;
use yii;
use tests\codeception\unit\fixtures\ApplicationMasterFixture;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use tests\codeception\unit\fixtures\CorpMasterFixture;
use yii\helpers\ArrayHelper;

/**
 * @property ApplicationMasterFixture $application_master
 * @property JobMasterFixture $job_master
 * @property ClientMasterFixture $client_master
 * @property CorpMasterFixture $corp_master
 * @property PrefFixture $pref
 */
class ApplicationMasterTest extends JmTestCase
{
    /**
     * 一応書いたけど流石に不要かも
     */
    public function testTableName()
    {
        verify(ApplicationMaster::tableName())->equals('application_master');
    }

    /**
     * attribute labels test
     */
    public function testAttributeLabels()
    {
        $model = new ApplicationMaster();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * rules test
     */
    public function testRules()
    {
        $this->specify('statusは空白ではいけない', function () {
            $model = new ApplicationMaster();
            $model->load([
                'ApplicationMaster' => [
                    'application_status_id' => '',
                    ],
            ]);
            $model->validate();
            verify($model->hasErrors('application_status_id'))->true();
        });

        $this->specify('statusは数字でなければいけない', function () {
            $model = new ApplicationMaster();
            $model->load([
                'ApplicationMaster' => [
                    'application_status_id' => '文字列',
                    ],
            ]);
            $model->validate();
            verify($model->hasErrors('application_status_id'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new ApplicationMaster();
            $model->load([
                'ApplicationMaster' => [
                    'application_status_id' => 1,
                    'application_memo' => '応募者備考メモ',
                    ],
            ]);
            verify($model->validate())->true();
        });
    }

    /**
     * 累計応募者カウントテスト（運営元権限）
     */
    public function testGetTotalCountByOwner()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('owner_admin');
        $targetRecords = array_filter(self::getFixtureInstance('application_master')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });
        verify($model->totalCount)->equals(count($targetRecords));
    }
    /** 累計応募者カウント（代理店権限） */
    public function testGetTotalCountByCorp()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('corp_admin');
        $count = 0;
        // Fixtureから数える
        $clients = $this->findRecordsByForeignKey(self::getFixtureInstance('client_master'), 'corp_master_id', $this->getIdentity()->corp_master_id);
        foreach ($clients as $client) {
            $jobs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $client['id']);
            foreach ($jobs as $job) {
                $applications = $this->findRecordsByForeignKey(self::getFixtureInstance('application_master'), 'job_master_id', $job['id']);
                $count += count($applications);
            }
        }
        verify($model->totalCount)->equals($count);
    }
    /** 累計応募者カウント掲載企業権限 */
    public function testGetTotalCountByClient()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('client_admin');
        $count = 0;
        // Fixtureから数える
        $jobs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $this->getIdentity()->client_master_id);
        foreach ($jobs as $job) {
            $applications = $this->findRecordsByForeignKey(self::getFixtureInstance('application_master'), 'job_master_id', $job['id']);
            $count += count($applications);
        }
        verify($model->totalCount)->equals($count);
    }

    /**
     * 本日のPC応募者カウントテスト（運営元権限）
     */
    public function testGetTodayPcCountByOwner()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('owner_admin');
        $targetRecords = array_filter(self::getFixtureInstance('application_master')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['created_at'] >= strtotime('today')
            && $record['created_at'] <= strtotime('tomorrow') - 1
            && $record['carrier_type'] == ApplicationMaster::PC_CARRIER;
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }
    /** 本日のPC応募者カウント（代理店権限） */
    public function testGetTodayPcCountByCorp()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('corp_admin');
        $count = 0;
        // Fixtureから数える
        $clients = $this->findRecordsByForeignKey(self::getFixtureInstance('client_master'), 'corp_master_id', $this->getIdentity()->corp_master_id);
        foreach ($clients as $client) {
            $jobs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $client['id']);
            foreach ($jobs as $job) {
                $applications = $this->findRecordsByForeignKey(self::getFixtureInstance('application_master'), 'job_master_id', $job['id']);
                $applications = array_filter($applications, function ($application) {
                    return $application['carrier_type'] === ApplicationMaster::PC_CARRIER
                    && $application['created_at'] >= strtotime('today')
                    && $application['created_at'] <= strtotime('tomorrow') - 1;
                });
                $count += count($applications);
            }
        }
        verify($model->todayPcCount)->equals($count);
    }
    /** 本日のPC応募者カウント（掲載企業権限） */
    public function testGetTodayPcCountByClient()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('client_admin');
        $count = 0;
        // Fixtureから数える
        $jobs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $this->getIdentity()->client_master_id);
        foreach ($jobs as $job) {
            $applications = $this->findRecordsByForeignKey(self::getFixtureInstance('application_master'), 'job_master_id', $job['id']);
            $applications = array_filter($applications, function ($application) {
                return $application['carrier_type'] === ApplicationMaster::PC_CARRIER
                && $application['created_at'] >= strtotime('today')
                && $application['created_at'] <= strtotime('tomorrow') - 1;
            });
            $count += count($applications);
        }
        verify($model->todayPcCount)->equals($count);
    }

    /**
     * 本日のスマホ応募者カウントテスト（運営元権限）
     */
    public function testGetTodaySmartPhoneCountByOwner()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('owner_admin');
        $targetRecords = array_filter(self::getFixtureInstance('application_master')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['created_at'] >= strtotime('today')
            && $record['created_at'] <= strtotime('tomorrow') - 1
            && $record['carrier_type'] == ApplicationMaster::SMART_PHONE_CARRIER;
        });
        verify($model->todaySmartPhoneCount)->equals(count($targetRecords));
    }
    /** 本日のスマホ応募者カウント（代理店権限） */
    public function testGetTodaySmartPhoneCountByCorp()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('corp_admin');
        $count = 0;
        // Fixtureから数える
        $clients = $this->findRecordsByForeignKey(self::getFixtureInstance('client_master'), 'corp_master_id', $this->getIdentity()->corp_master_id);
        foreach ($clients as $client) {
            $jobs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $client['id']);
            foreach ($jobs as $job) {
                $applications = $this->findRecordsByForeignKey(self::getFixtureInstance('application_master'), 'job_master_id', $job['id']);
                $applications = array_filter($applications, function ($application) {
                    return $application['carrier_type'] === ApplicationMaster::SMART_PHONE_CARRIER
                    && $application['created_at'] >= strtotime('today')
                    && $application['created_at'] <= strtotime('tomorrow') - 1;
                });
                $count += count($applications);
            }
        }
        verify($model->todaySmartPhoneCount)->equals($count);
    }
    /** 本日のスマホ応募者カウント（掲載企業権限） */
    public function testGetTodaySmartPhoneCountByClient()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('client_admin');
        $count = 0;
        // Fixtureから数える
        $jobs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $this->getIdentity()->client_master_id);
        foreach ($jobs as $job) {
            $applications = $this->findRecordsByForeignKey(self::getFixtureInstance('application_master'), 'job_master_id', $job['id']);
            $applications = array_filter($applications, function ($application) {
                return $application['carrier_type'] === ApplicationMaster::SMART_PHONE_CARRIER
                && $application['created_at'] >= strtotime('today')
                && $application['created_at'] <= strtotime('tomorrow') - 1;
            });
            $count += count($applications);
        }
        verify($model->todaySmartPhoneCount)->equals($count);
    }

    /**
     * getTodayTotalCountのtest
     */
    public function testGetTodayTotalCount()
    {
        $this->setIdentity('owner_admin');
        $model = new ApplicationMaster();
        verify($model->todayTotalCount)->equals($model->todayPcCount + $model->todaySmartPhoneCount);
    }

    /**
     * 昨日のPC応募者カウントテスト（運営元権限）
     */
    public function testGetYesterdayPcCountByOwner()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('owner_admin');
        $targetRecords = array_filter(self::getFixtureInstance('application_master')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['created_at'] >= strtotime('yesterday')
            && $record['created_at'] <= strtotime('today') - 1
            && $record['carrier_type'] == ApplicationMaster::PC_CARRIER;
        });
        verify($model->yesterdayPcCount)->equals(count($targetRecords));
    }
    /** 昨日のPC応募者カウント（代理店権限） */
    public function testGetYesterdayPcCountByCorp()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('corp_admin');
        $count = 0;
        // Fixtureから数える
        $clients = $this->findRecordsByForeignKey(self::getFixtureInstance('client_master'), 'corp_master_id', $this->getIdentity()->corp_master_id);
        foreach ($clients as $client) {
            $jobs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $client['id']);
            foreach ($jobs as $job) {
                $applications = $this->findRecordsByForeignKey(self::getFixtureInstance('application_master'), 'job_master_id', $job['id']);
                $applications = array_filter($applications, function ($application) {
                    return $application['carrier_type'] === ApplicationMaster::PC_CARRIER
                    && $application['created_at'] >= strtotime('yesterday')
                    && $application['created_at'] <= strtotime('today') - 1;
                });
                $count += count($applications);
            }
        }
        verify($model->yesterdayPcCount)->equals($count);
    }
    /** 昨日のPC応募者カウント（掲載企業権限） */
    public function testGetYesterdayPcCountByClient()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('client_admin');
        $count = 0;
        // Fixtureから数える
        $jobs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $this->getIdentity()->client_master_id);
        foreach ($jobs as $job) {
            $applications = $this->findRecordsByForeignKey(self::getFixtureInstance('application_master'), 'job_master_id', $job['id']);
            $applications = array_filter($applications, function ($application) {
                return $application['carrier_type'] === ApplicationMaster::PC_CARRIER
                && $application['created_at'] >= strtotime('yesterday')
                && $application['created_at'] <= strtotime('today') - 1;
            });
            $count += count($applications);
        }
        verify($model->yesterdayPcCount)->equals($count);
    }

    /**
     * 昨日のスマホ応募者カウントテスト（運営元権限）
     */
    public function testGetYesterdaySmartPhoneCountByOwner()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('owner_admin');
        $targetRecords = array_filter(self::getFixtureInstance('application_master')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['created_at'] >= strtotime('yesterday')
            && $record['created_at'] <= strtotime('today') - 1
            && $record['carrier_type'] == ApplicationMaster::SMART_PHONE_CARRIER;
        });
        verify($model->yesterdaySmartPhoneCount)->equals(count($targetRecords));
    }
    /** 昨日のスマホ応募者カウント（代理店権限） */
    public function testGetYesterdaySmartPhoneCountByCorp()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('corp_admin');
        $count = 0;
        // Fixtureから数える
        $clients = $this->findRecordsByForeignKey(self::getFixtureInstance('client_master'), 'corp_master_id', $this->getIdentity()->corp_master_id);
        foreach ($clients as $client) {
            $jobs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $client['id']);
            foreach ($jobs as $job) {
                $applications = $this->findRecordsByForeignKey(self::getFixtureInstance('application_master'), 'job_master_id', $job['id']);
                $applications = array_filter($applications, function ($application) {
                    return $application['carrier_type'] === ApplicationMaster::SMART_PHONE_CARRIER
                    && $application['created_at'] >= strtotime('yesterday')
                    && $application['created_at'] <= strtotime('today') - 1;
                });
                $count += count($applications);
            }
        }
        verify($model->yesterdaySmartPhoneCount)->equals($count);
    }
    /** 昨日のスマホ応募者カウント（掲載企業権限） */
    public function testGetYesterdaySmartPhoneCountByClient()
    {
        $model = new ApplicationMaster();
        $this->setIdentity('client_admin');
        $count = 0;
        // Fixtureから数える
        $jobs = $this->findRecordsByForeignKey(self::getFixtureInstance('job_master'), 'client_master_id', $this->getIdentity()->client_master_id);
        foreach ($jobs as $job) {
            $applications = $this->findRecordsByForeignKey(self::getFixtureInstance('application_master'), 'job_master_id', $job['id']);
            $applications = array_filter($applications, function ($application) {
                return $application['carrier_type'] === ApplicationMaster::SMART_PHONE_CARRIER
                && $application['created_at'] >= strtotime('yesterday')
                && $application['created_at'] <= strtotime('today') - 1;
            });
            $count += count($applications);
        }
        verify($model->yesterdaySmartPhoneCount)->equals($count);
    }

    /**
     * getYesterdayTotalCountのtest
     */
    public function testGetYesterdayTotalCount()
    {
        $this->setIdentity('owner_admin');
        $model = new ApplicationMaster();
        verify($model->yesterdayTotalCount)->equals($model->yesterdayPcCount + $model->yesterdaySmartPhoneCount);
    }

    /**
     * 各種モデルを返すgetter
     * (本当はrelationレコードの有無で場合分けして値のチェックもした方がより正確だが今回はこうする)
     */
    public function testGetJobModel()
    {
        $model = ApplicationMaster::findOne($this->id(99, 'application_master'));
        verify($model->jobModel)->isInstanceOf(JobMaster::className());
    }
    public function testGetClientModel()
    {
        $model = ApplicationMaster::findOne($this->id(98, 'application_master'));
        verify($model->clientModel)->isInstanceOf(ClientMaster::className());
    }
    public function testGetCorpModel()
    {
        $model = ApplicationMaster::findOne($this->id(102, 'application_master'));
        verify($model->corpModel)->isInstanceOf(CorpMaster::className());
    }
    public function testGetClientChargePlanModel()
    {
        $model = ApplicationMaster::findOne($this->id(103, 'application_master'));
        verify($model->clientChargePlanModel)->isInstanceOf(ClientChargePlan::className());
    }
    public function testGetPrefModel()
    {
        $model = ApplicationMaster::findOne($this->id(95, 'application_master'));
        verify($model->prefModel)->isInstanceOf(Pref::className());
    }
    public function testGetOccupationModel()
    {
        $model = ApplicationMaster::findOne($this->id(114, 'application_master'));
        verify($model->occupationModel)->isInstanceOf(Occupation::className());
    }
    public function testGetPrefName()
    {
        $model = ApplicationMaster::findOne($this->id(51, 'application_master'));
        $prefNames = ArrayHelper::getColumn(self::getFixtureInstance('pref')->data(), 'pref_name');
        $prefNames = array_merge([''], $prefNames);
        verify(in_array($model->prefName, $prefNames))->true();
    }
    /**
     * 誕生日関連getter,setter検証
     */
    public function testGetBirthDate()
    {
        $model = new ApplicationMaster();
        $year = 1989;
        $month = 2;
        $day = 12;
        $model->birth_date = $year . '-' . $month . '-' . $day;

        verify($model->birthDateYear)->equals($year);
        verify($model->birthDateMonth)->equals($month);
        verify($model->birthDateDay)->equals($day);
    }
    public function testSetBirthDate()
    {
        $model = new ApplicationMaster();
        $year = 1995;
        $month = 1;
        $day = 17;
        $model->birthDateYear = $year;
        $model->birthDateMonth = $month;
        $model->birthDateDay = $day;
        verify($model->birthDateYear)->equals($year);
        verify($model->birthDateMonth)->equals($month);
        verify($model->birthDateDay)->equals($day);
    }

    /**
     * 生年月日から年齢を計算するテスト
     * 年月の経過で通らなくなります
     */
    public function testAge()
    {
        $application = new ApplicationMaster();
        $application->birth_date = '1980-12-03';
        verify($application->getAge())->equals('36');
    }

    /**
     * 生年月日に提供する年月日リストテスト
     */
    public function testGetBirthYearList()
    {
        $application = new ApplicationMaster();
        // 50年分取得している
        verify($application->getBirthYearList())->count(50);
        // 1年は12ヶ月あります
        verify($application->getBirthMonthList())->count(12);
        // 1ヶ月は最大31日あります
        verify($application->getBirthDayList())->count(31);
    }
    /**
     * getBirthDateメソッドの検証
     */
    public function testBirthDate()
    {
        $application = new ApplicationMaster();
        verify($application->getBirthDate())->equals('');
        $application->birthDateYear = '1976';
        $application->birthDateMonth = 'all';
        $application->birthDateDay = 'all';
        verify($application->getBirthDate())->equals('1976-%-%');
    }

    /**
     * getFullNameメソッドの検証
     */
    public function testGetFullName()
    {
        $model = new ApplicationMaster();
        $model->name_sei = 'あああ';
        $model->name_mei = 'いいい';
        verify($model->fullName)->equals('あああ いいい');
    }

    /**
     * getFullNameKanaメソッドの検証
     */
    public function testGetFullNameKana()
    {
        $model = new ApplicationMaster();
        $model->kana_sei = 'あああ';
        $model->kana_mei = 'いいい';
        verify($model->fullNameKana)->equals('あああ いいい');
    }
}
