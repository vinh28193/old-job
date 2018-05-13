<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2015/12/24
 * Time: 12:38
 */

namespace models\manage;

use app\models\manage\AccessLogMonthly;
use tests\codeception\unit\fixtures\AccessLogMonthlyFixture;
use tests\codeception\unit\JmTestCase;

class AccessLogMonthlyTest extends JmTestCase
{
    public function testRules()
    {
        $model = new AccessLogMonthly();
        verify($model->rules())->notEmpty();
        // Giiの生成からほぼ変更無しかつ書き込み処理が未実装なため省略
    }

    public function testAttributeLabels()
    {
        $model = new AccessLogMonthly();
        verify($model->attributeLabels())->notEmpty();
        // Giiの生成からほぼ変更無しかつラベルを使う処理が未実装なため省略
    }

    public function testGetApplicationCountTotal()
    {
        /** @var AccessLogMonthly $model */
        $model = AccessLogMonthly::findOne($this->id(1, 'access_log_monthly'));
        $fixture = self::getFixtureInstance('access_log_monthly')->data();
        $applicationCountPc = $fixture[$this->id(0, 'access_log_monthly')]['application_count_pc'];
        $applicationCountSmart =$fixture[$this->id(0, 'access_log_monthly')]['application_count_smart'];

        verify($model->applicationCountTotal)->equals($applicationCountPc + $applicationCountSmart);
    }

    public function testGetMemberCountTotal()
    {
        /** @var AccessLogMonthly $model */
        $model =AccessLogMonthly::findOne($this->id(1, 'access_log_monthly'));
        $fixture = self::getFixtureInstance('access_log_monthly')->data();
        $memberCountPc = $fixture[$this->id(0, 'access_log_monthly')]['member_count_pc'];
        $memberCountSmart =$fixture[$this->id(0, 'access_log_monthly')]['member_count_smart'];

        verify($model->memberCountTotal)->equals($memberCountPc + $memberCountSmart);
    }

    public function testGetDetailCountTotal()
    {
        /** @var AccessLogMonthly $model */
        $model = AccessLogMonthly::findOne($this->id(1, 'access_log_monthly'));
        $fixture = self::getFixtureInstance('access_log_monthly')->data();
        $detailCountPc = $fixture[$this->id(0, 'access_log_monthly')]['detail_count_pc'];
        $detailCountSmart =$fixture[$this->id(0, 'access_log_monthly')]['detail_count_smart'];

        verify($model->detailCountTotal)->equals($detailCountPc + $detailCountSmart);
    }

    public function testFindYesterdayRecord()
    {
        $yesterdayLog = AccessLogMonthly::findYesterdayRecord();
        $fixture = self::getFixtureInstance('access_log_monthly')->data();
        // 昨日かつテナントidが1であるレコードの情報
        $targetRecord = $fixture[$this->id(2, 'access_log_monthly')];

        verify($yesterdayLog->access_date)->equals(date('Y-m-d', strtotime('yesterday')));
        verify($yesterdayLog->id)->equals($targetRecord['id']);
        verify($yesterdayLog->detail_count_pc)->equals($targetRecord['detail_count_pc']);
        verify($yesterdayLog->application_count_pc)->equals($targetRecord['application_count_pc']);
        verify($yesterdayLog->member_count_pc)->equals($targetRecord['member_count_pc']);
        verify($yesterdayLog->detail_count_smart)->equals($targetRecord['detail_count_smart']);
        verify($yesterdayLog->application_count_smart)->equals($targetRecord['application_count_smart']);
        verify($yesterdayLog->member_count_smart)->equals($targetRecord['member_count_smart']);
    }
}