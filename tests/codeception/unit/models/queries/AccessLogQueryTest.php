<?php
namespace models\queries;

use app\models\manage\AccessLog;
use app\models\queries\AccessLogQuery;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;

class AccessLogQueryTest extends JmTestCase
{
    /**
     * @return AccessLogQuery
     */
    private function getQuery()
    {
        return new AccessLogQuery(AccessLog::className());
    }

    /**
     * addAuthQueryのtest
     * 権限用の各管理者のレコードが最低一つあることが前提
     */
    public function testAddAuthQuery()
    {
        /** @var AccessLog[] $models */
        $this->setIdentity('owner_admin');
        $models = $this->getQuery()->addAuthQuery()->all();
        verify($models)->notEmpty();
        $count = AccessLog::find()->count();
        verify($models)->count((int)$count);

        $this->setIdentity('corp_admin');
        $models = $this->getQuery()->addAuthQuery()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify($model->clientMaster->corp_master_id)->equals($this->getIdentity()->corp_master_id);
        }

        $this->setIdentity('client_admin');
        $models = $this->getQuery()->addAuthQuery()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify($model->clientMaster->id)->equals($this->getIdentity()->client_master_id);
        }
    }

    /**
     * onlyJobDetailのtest
     */
    public function testOnlyJobDetail()
    {
        $this->setIdentity('owner_admin');
        $models = $this->getQuery()->onlyJobDetail()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify(empty($model->job_master_id))->false();
            verify(empty($model->application_master_id))->true();
        }
    }

    /**
     * todayのtest
     */
    public function testToday()
    {
        $this->setIdentity('owner_admin');
        $models = $this->getQuery()->today()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify(ArrayHelper::getValue($model, 'accessed_at'))->greaterOrEquals(strtotime('today'));
            verify(ArrayHelper::getValue($model, 'accessed_at'))->lessOrEquals(strtotime('+1 day'));
        }
    }

    /**
     * yesterdayのtest
     */
    public function testYesterday()
    {
        $this->setIdentity('owner_admin');
        $models = $this->getQuery()->yesterday()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify(ArrayHelper::getValue($model, 'accessed_at'))->greaterOrEquals(strtotime('yesterday'));
            verify(ArrayHelper::getValue($model, 'accessed_at'))->lessOrEquals(strtotime('today'));
        }
    }
}
