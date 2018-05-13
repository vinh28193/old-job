<?php
namespace models\manage;

use app\models\manage\AccessCount;
use app\modules\manage\models\Manager;
use tests\codeception\unit\fixtures\AccessLogFixture;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use tests\codeception\unit\fixtures\CorpMasterFixture;
use Yii;
use tests\codeception\unit\JmTestCase;
use yii\helpers\Json;

/**
 * @property AccessLogFixture $application_master
 * @property JobMasterFixture $job_master
 * @property ClientMasterFixture $client_master
 * @property CorpMasterFixture $corp_master
 */
class AccessCountTest extends JmTestCase
{
    /**
     * getTodayPcCountのテスト(運営元)
     */
    public function testGetTodayPcCountByOwner()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(1, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 0
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getTodayPcCountのテスト(代理店)
     */
    public function testGetTodayPcCountByCorp()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(2, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 0
            && $record['corp_master_id'] = Yii::$app->user->identity->corp_master_id
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getTodayPcCountのテスト(掲載企業)
     */
    public function testGetTodayPcCountByClient()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(3, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 0
            && $record['client_master_id'] = Yii::$app->user->identity->client_master_id
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getTodaySpCountのテスト(運営元)
     */
    public function testGetTodaySpCountByOwner()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(1, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 1
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getTodaySpCountのテスト(代理店)
     */
    public function testGetTodaySpCountByCorp()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(2, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 1
            && $record['corp_master_id'] = Yii::$app->user->identity->corp_master_id
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getTodaySpCountのテスト(掲載企業)
     */
    public function testGetTodaySpCountByClient()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(3, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 1
            && $record['client_master_id'] = Yii::$app->user->identity->client_master_id
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getYesterdayPcCountのテスト(運営元)
     */
    public function testGetYesterdayPcCountByOwner()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(1, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('yesterday')
            && $record['accessed_at'] < strtotime('today')
            && $record['carrier_type'] == 0
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getYesterdayPcCountのテスト(代理店)
     */
    public function testGetYesterdayPcCountByCorp()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(2, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('yesterday')
            && $record['accessed_at'] < strtotime('today')
            && $record['carrier_type'] == 0
            && $record['corp_master_id'] = Yii::$app->user->identity->corp_master_id
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getYesterdayPcCountのテスト(掲載企業)
     */
    public function testGetYesterdayPcCountByClient()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(3, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('yesterday')
            && $record['accessed_at'] < strtotime('today')
            && $record['carrier_type'] == 0
            && $record['client_master_id'] = Yii::$app->user->identity->client_master_id
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getYesterdaySpCountのテスト(運営元)
     */
    public function testGetYesterdaySpCountByOwner()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(1, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('yesterday')
            && $record['accessed_at'] < strtotime('today')
            && $record['carrier_type'] == 1
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getYesterdaySpCountのテスト(代理店)
     */
    public function testGetYesterdaySpCountByCorp()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(2, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('yesterday')
            && $record['accessed_at'] < strtotime('today')
            && $record['carrier_type'] == 1
            && $record['corp_master_id'] = Yii::$app->user->identity->corp_master_id
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getYesterdaySpCountのテスト(掲載企業)
     */
    public function testGetYesterdaySpCountByClient()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();

        Yii::$app->user->identity = Manager::findIdentity($this->id(3, 'access_log'));
        $targetRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('yesterday')
            && $record['accessed_at'] < strtotime('today')
            && $record['carrier_type'] == 1
            && $record['client_master_id'] = Yii::$app->user->identity->client_master_id
            && !empty($record['job_master_id'])
            && empty($record['application_master_id']);
        });
        verify($model->todayPcCount)->equals(count($targetRecords));
    }

    /**
     * getUserAgentDataのテスト(運営元)
     */
    public function testGetUserAgentDataByOwner()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();
        $userAgentData = '';

        Yii::$app->user->identity = Manager::findIdentity($this->id(1, 'access_log'));
        $targetPcRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 0;
        });

        $userAgents = [];
        $browserList = [];
        if (count($targetPcRecords) > 0) {
            foreach ($targetPcRecords as $targetRecord) {
                if (!in_array($targetRecord['access_browser'], $browserList)) {
                    $browserList[] = $targetRecord['access_browser'] . $targetRecord['carrier_type'];
                }
                $index = array_keys($browserList, $targetRecord['access_browser'] . $targetRecord['carrier_type']);
                if (empty($userAgents[$index[0]]['pvCount'])) {
                    $userAgents[$index[0]]['access_browser'] = $targetRecord['access_browser'];
                    $userAgents[$index[0]]['carrier_type'] = $targetRecord['carrier_type'];
                    $userAgents[$index[0]]['pvCount'] = 0;
                }
                $userAgents[$index[0]]['pvCount'] = (int)$userAgents[$index[0]]['pvCount'] + 1;
            }
        }

        $targetSpRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 1;
        });
        if (count($targetSpRecords) > 0) {
            foreach ($targetSpRecords as $targetRecord) {
                if (!in_array($targetRecord['access_browser'], $browserList)) {
                    $browserList[] = $targetRecord['access_browser'] . $targetRecord['carrier_type'];
                }
                $index = array_keys($browserList, $targetRecord['access_browser'] . $targetRecord['carrier_type']);
                if (empty($userAgents[$index[0]]['pvCount'])) {
                    $userAgents[$index[0]]['access_browser'] = $targetRecord['access_browser'];
                    $userAgents[$index[0]]['carrier_type'] = $targetRecord['carrier_type'];
                    $userAgents[$index[0]]['pvCount'] = 0;
                }
                $userAgents[$index[0]]['pvCount'] = (int)$userAgents[$index[0]]['pvCount'] + 1;
            }
        }

        if (count($userAgents)) {
            // カラム情報
            $chartData[] = ['ブラウザ名', 'PV数', [ 'role' => 'style' ],[ 'role' => 'annotation' ]];
            // データ情報
            foreach ($userAgents as $userAgent) {
                $chartData[] = [
                    $userAgent['access_browser'],
                    (int)$userAgent['pvCount'],
                    $userAgent['carrier_type'] == 0 ? '#4169e1' : '3cb371',
                    $userAgent['pvCount'] . 'PV',
                ];
            }
            $userAgentData = Json::encode($chartData);
        }
        verify($model->userAgentData)->equals($userAgentData);
    }

    /**
     * getUserAgentDataのテスト(代理店)
     */
    public function testGetUserAgentDataByCorp()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();
        $userAgentData = '';

        Yii::$app->user->identity = Manager::findIdentity($this->id(2, 'access_log'));
        $targetPcRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 0
            && $record['corp_master_id'] = Yii::$app->user->identity->corp_master_id;
        });
        $userAgents = [];
        $browserList = [];
        if (count($targetPcRecords) > 0) {
            foreach ($targetPcRecords as $targetRecord) {
                if (!in_array($targetRecord['access_browser'], $browserList)) {
                    $browserList[] = $targetRecord['access_browser'] . $targetRecord['carrier_type'];
                }
                $index = array_keys($browserList, $targetRecord['access_browser'] . $targetRecord['carrier_type']);
                if (empty($userAgents[$index[0]]['pvCount'])) {
                    $userAgents[$index[0]]['access_browser'] = $targetRecord['access_browser'];
                    $userAgents[$index[0]]['carrier_type'] = $targetRecord['carrier_type'];
                    $userAgents[$index[0]]['pvCount'] = 0;
                }
                $userAgents[$index[0]]['pvCount'] = (int)$userAgents[$index[0]]['pvCount'] + 1;
            }
        }

        $targetSpRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 1
            && $record['corp_master_id'] = Yii::$app->user->identity->corp_master_id;
        });
        if (count($targetSpRecords) > 0) {
            foreach ($targetSpRecords as $targetRecord) {
                if (!in_array($targetRecord['access_browser'], $browserList)) {
                    $browserList[] = $targetRecord['access_browser'] . $targetRecord['carrier_type'];
                }
                $index = array_keys($browserList, $targetRecord['access_browser'] . $targetRecord['carrier_type']);
                if (empty($userAgents[$index[0]]['pvCount'])) {
                    $userAgents[$index[0]]['access_browser'] = $targetRecord['access_browser'];
                    $userAgents[$index[0]]['carrier_type'] = $targetRecord['carrier_type'];
                    $userAgents[$index[0]]['pvCount'] = 0;
                }
                $userAgents[$index[0]]['pvCount'] = (int)$userAgents[$index[0]]['pvCount'] + 1;
            }
        }

        if (count($userAgents)) {
            // カラム情報
            $chartData[] = ['ブラウザ名', 'PV数', [ 'role' => 'style' ],[ 'role' => 'annotation' ]];
            // データ情報
            foreach ($userAgents as $userAgent) {
                $chartData[] = [
                    $userAgent['access_browser'],
                    (int)$userAgent['pvCount'],
                    $userAgent['carrier_type'] == 0 ? '#4169e1' : '3cb371',
                    $userAgent['pvCount'] . 'PV',
                ];
            }
            $userAgentData = Json::encode($chartData);
        }
        verify($model->userAgentData)->equals($userAgentData);
    }

    /**
     * getUserAgentDataのテスト(掲載企業)
     */
    public function testGetUserAgentDataByClient()
    {
        $model = new AccessCount();
        $allRecords = self::getFixtureInstance('access_log')->data();
        $userAgentData = '';

        Yii::$app->user->identity = Manager::findIdentity($this->id(3, 'access_log'));
        $targetPcRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 0
            && $record['client_master_id'] = Yii::$app->user->identity->client_master_id;
        });
        $userAgents = [];
        $browserList = [];
        if (count($targetPcRecords) > 0) {
            foreach ($targetPcRecords as $targetRecord) {
                if (!in_array($targetRecord['access_browser'], $browserList)) {
                    $browserList[] = $targetRecord['access_browser'] . $targetRecord['carrier_type'];
                }
                $index = array_keys($browserList, $targetRecord['access_browser'] . $targetRecord['carrier_type']);
                if (empty($userAgents[$index[0]]['pvCount'])) {
                    $userAgents[$index[0]]['access_browser'] = $targetRecord['access_browser'];
                    $userAgents[$index[0]]['carrier_type'] = $targetRecord['carrier_type'];
                    $userAgents[$index[0]]['pvCount'] = 0;
                }
                $userAgents[$index[0]]['pvCount'] = (int)$userAgents[$index[0]]['pvCount'] + 1;
            }
        }

        $targetSpRecords = array_filter($allRecords, function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id
            && $record['accessed_at'] >= strtotime('today')
            && $record['accessed_at'] < strtotime('+1 day')
            && $record['carrier_type'] == 1
            && $record['client_master_id'] = Yii::$app->user->identity->client_master_id;
        });
        if (count($targetSpRecords) > 0) {
            foreach ($targetSpRecords as $targetRecord) {
                if (!in_array($targetRecord['access_browser'], $browserList)) {
                    $browserList[] = $targetRecord['access_browser'] . $targetRecord['carrier_type'];
                }
                $index = array_keys($browserList, $targetRecord['access_browser'] . $targetRecord['carrier_type']);
                if (empty($userAgents[$index[0]]['pvCount'])) {
                    $userAgents[$index[0]]['access_browser'] = $targetRecord['access_browser'];
                    $userAgents[$index[0]]['carrier_type'] = $targetRecord['carrier_type'];
                    $userAgents[$index[0]]['pvCount'] = 0;
                }
                $userAgents[$index[0]]['pvCount'] = (int)$userAgents[$index[0]]['pvCount'] + 1;
            }
        }
        if (count($userAgents)) {
            // カラム情報
            $chartData[] = ['ブラウザ名', 'PV数', [ 'role' => 'style' ],[ 'role' => 'annotation' ]];
            // データ情報
            foreach ($userAgents as $userAgent) {
                $chartData[] = [
                    $userAgent['access_browser'],
                    (int)$userAgent['pvCount'],
                    $userAgent['carrier_type'] == 0 ? '#4169e1' : '3cb371',
                    $userAgent['pvCount'] . 'PV',
                ];
            }
            $userAgentData = Json::encode($chartData);
        }
        verify($model->userAgentData)->equals($userAgentData);
    }
}
