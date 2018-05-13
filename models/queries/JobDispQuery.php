<?php

namespace app\models\queries;

use app\models\JobMasterDisp;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\DispType;
use app\models\manage\CorpMaster;
use Yii;
use yii\db\ActiveQuery;
use app\models\manage\JobReviewStatus;

/**
 * Class JobDispQuery
 * @package app\models\queries
 */
class JobDispQuery extends ActiveQuery
{
    /**
     * 有効な求人のみのqueryを返す
     * @return $this
     */
    public function active()
    {
        $this->andWhere([
            JobMasterDisp::tableName() . '.valid_chk'    => JobMasterDisp::FLAG_VALID,
            JobMasterDisp::tableName() . '.job_review_status_id' => JobReviewStatus::STEP_REVIEW_OK,
            ClientMaster::tableName() . '.valid_chk'     => JobMasterDisp::FLAG_VALID,
            CorpMaster::tableName() . '.valid_chk'       => JobMasterDisp::FLAG_VALID,
            ClientChargePlan::tableName() . '.valid_chk' => JobMasterDisp::FLAG_VALID,
            DispType::tableName() . '.valid_chk'         => JobMasterDisp::FLAG_VALID,
        ])->andWhere([
            'or',
            ['<=', JobMasterDisp::tableName() . '.disp_start_date', time()],
            [JobMasterDisp::tableName() . '.disp_start_date' => null],
        ])->andWhere([
            'or',
            ['>=', JobMasterDisp::tableName() . '.disp_end_date', strtotime('today')],
            [JobMasterDisp::tableName() . '.disp_end_date' => null],
        ]);

        return $this;
    }

    public function count($q = '*', $db = null)
    {
        if ($this->select === null) {
            $this->select([JobMasterDisp::tableName() . '.id', JobMasterDisp::tableName() . '.tenant_id']);
        }
        return parent::count($q, $db);
    }

    /**
     * job_noを元に一つの仕事情報に絞り、
     * パン屑で使う検索キー情報をwithする。
     * @param int $jobNo 仕事No.
     * @return $this
     */
    public function findOne($jobNo)
    {
        return $this->andWhere([
            'job_no' => $jobNo,
        ])->with([
            'jobPref.pref',
            'jobDist.dist',
        ]);
    }
}
