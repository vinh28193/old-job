<?php

/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/03
 * Time: 18:27
 */

use app\models\JobMasterDisp;
use app\models\manage\JobMaster;
use app\models\manage\JobReviewStatus;
use app\models\queries\JobDispQuery;

class JobDispQueryTest extends \tests\codeception\unit\JmTestCase
{
    /*
     * JobDispQuery::active()のtest
     */
    public function testActive()
    {
        $time = time();
        /** @var JobMasterDisp[] $models */
        $models = JobMasterDisp::find()->active()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify($model->valid_chk)->equals(JobMasterDisp::FLAG_VALID);
            verify($model->job_review_status_id)->equals(JobReviewStatus::STEP_REVIEW_OK);
            verify($model->clientMaster->valid_chk)->equals(JobMasterDisp::FLAG_VALID);
            verify($model->clientMaster->corpMaster->valid_chk)->equals(JobMasterDisp::FLAG_VALID);
            verify($model->clientChargePlan->valid_chk)->equals(JobMasterDisp::FLAG_VALID);
            verify($model->clientChargePlan->dispType->valid_chk)->equals(JobMasterDisp::FLAG_VALID);

            if($model->disp_start_date !== null){
                verify($model->disp_start_date)->lessOrEquals($time);
            }

            if($model->disp_end_date !== null){
                verify($model->disp_end_date)->greaterOrEquals(strtotime('today'));
            }
        }
    }

    // test不可
//    public function testCount(){}

    public function testFindOne(){
        /** @var JobMaster $jobMaster */
        $jobMaster = JobMaster::find()->one();
        /** @var JobMasterDisp $jobMasterDisp */
        $jobMasterDisp = (new JobDispQuery(JobMasterDisp::className()))->findOne($jobMaster->job_no)->one();

        foreach ($jobMaster->attributes as $name => $value) {
            verify($jobMasterDisp->$name)->equals($value);
        }
        verify($jobMasterDisp->isRelationPopulated('jobPref'))->true();
        foreach ($jobMasterDisp->jobPref as $jobPref) {
            verify($jobPref->isRelationPopulated('pref'))->true();
        }
        verify($jobMasterDisp->isRelationPopulated('jobDist'))->true();
        foreach ($jobMasterDisp->jobDist as $jobDist) {
            verify($jobDist->isRelationPopulated('dist'))->true();
        }
    }
}
