<?php

namespace app\models\queries;

use app\models\manage\AccessLog;
use app\models\manage\ClientMaster;
use app\models\manage\JobMaster;
use app\modules\manage\models\Manager;
use Exception;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class AccessLogQuery
 * @package app\models\queries
 */
class AccessLogQuery extends ActiveQuery
{
    /**
     * カウントの時はjob_masterやclient_masterの
     * 情報の取得が不要なのでwithせずにjoinしている
     * @return $this
     * @throws Exception
     */
    public function addAuthQuery()
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        switch ($identity->myRole) {
            case Manager::OWNER_ADMIN:
                return $this;
                break;
            case Manager::CORP_ADMIN:
                return $this->leftJoin('job_master', 'access_log.job_master_id = job_master.id')
                    ->leftJoin('client_master', 'job_master.client_master_id = client_master.id')
                    ->andWhere([ClientMaster::tableName() . '.corp_master_id' => $identity->corp_master_id]);
                break;
            case Manager::CLIENT_ADMIN:
                return $this->leftJoin('job_master', 'access_log.job_master_id = job_master.id')
                    ->andWhere([JobMaster::tableName() . '.client_master_id' => $identity->client_master_id]);
                break;
            default :
                throw new Exception("{$identity->myRole} is not a valid role");
                break;
        }
    }

    /**
     * 求人詳細画面に絞るqueryを追加する
     * @return $this
     */
    public function onlyJobDetail()
    {
        return $this->andWhere([
            'and',
            ['not', [AccessLog::tableName() . '.job_master_id' => null]],
            [AccessLog::tableName() . '.application_master_id' => null],
        ]);
    }

    /**
     * 今日に絞るqueryを追加する
     * @return $this
     */
    public function today()
    {
        return $this->andWhere(['between', AccessLog::tableName() . '.accessed_at', strtotime('today'), strtotime('+1 day') - 1]);
    }

    /**
     * 昨日に絞るqueryを追加する
     * @return $this
     */
    public function yesterday()
    {
        return $this->andWhere(['between', AccessLog::tableName() . '.accessed_at', strtotime('yesterday'), strtotime('today') - 1]);
    }
}
