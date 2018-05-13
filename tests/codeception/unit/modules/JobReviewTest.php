<?php

use app\modules\manage\models\JobReview;
use app\modules\manage\models\Manager;
use tests\codeception\unit\JmTestCase;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\JobMaster;
use app\models\manage\JobReviewStatus;

/**
 * 審査モデルテスト
 */
class JobReviewTest extends JmTestCase
{
    /**
     * ルールテスト
     * ※ほとんどはJobReviewHistoryで実装しているので個別のもののみをテスト
     * ※審査ステータス更新チェックでcalcReviewStatusもテスト済み
     */
    public function testRules()
    {
        $jobMasterId = 101;

        $this->specify('必須チェック(loadチェック込み)', function () use ($jobMasterId) {
            $model = new JobReview();
            $this->setIdentity(Manager::OWNER_ADMIN);  // load内でログインIDをセットしているため
            $model->load([
                $model->formName() => [
                    'review' => null,
                    'admin_master_id' => null,
                    'job_master_id' => $jobMasterId,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('review'))->false();
            verify($model->hasErrors('admin_master_id'))->false();

            $model->scenario = JobReview::SCENARIO_REVIEW;
            $model->load([
                $model->formName() => [
                    'review' => null,
                    'admin_master_id' => null,
                    'job_master_id' => $jobMasterId,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('review'))->true();
            verify($model->hasErrors('admin_master_id'))->false();

            // loadを通さない場合
            $model = new JobReview();
            $model->review = null;
            $model->admin_master_id = null;
            $model->validate();

            verify($model->hasErrors('review'))->false();
            verify($model->hasErrors('admin_master_id'))->true();

            $model->scenario = JobReview::SCENARIO_REVIEW;
            $model->validate();
            verify($model->hasErrors('review'))->true();

        });
        $this->specify('booleanチェック', function () {
            $model = new JobReview();
            $model->review = 3;
            $model->validate();
            verify($model->hasErrors('review'))->true();
        });
        $this->specify('審査ステータス更新チェック', function () use ($jobMasterId) {
            $this->setIdentity(Manager::OWNER_ADMIN);
            // 未load時のチェック

            $model = new JobReview();
            $model->job_master_id = $jobMasterId;
            $model->job_review_status_id = JobReviewStatus::STEP_OWNER_REVIEW;
            // 求人の審査ステータスをSTEP_OWNER_REVIEW以外にする
            $jobMaster = JobMaster::findOne($jobMasterId);
            $jobMaster->job_review_status_id = JobReviewStatus::STEP_JOB_EDIT;
            $jobMaster->save(false);
            $model->validate();
            verify($model->hasErrors('job_review_status_id'))->true();

            // 求人の審査ステータスをSTEP_OWNER_REVIEWにする
            $jobMaster->job_review_status_id = JobReviewStatus::STEP_OWNER_REVIEW;
            $jobMaster->save(false);

            // ※モデルを更新してリレーションデータも更新する
            $model = new JobReview();
            $model->job_master_id = $jobMasterId;
            $model->job_review_status_id = JobReviewStatus::STEP_OWNER_REVIEW;
            $model->validate();
            verify($model->hasErrors('job_review_status_id'))->false();

            // ロード後のチェック

            // ※モデルを更新してリレーションデータも更新する
            $model = new JobReview();
            $model->load([
                $model->formName() => [
                    'job_master_id' => $jobMasterId,
                    'job_review_status_id' => JobReviewStatus::STEP_OWNER_REVIEW,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('job_review_status_id'))->false();

            // 求人の審査ステータスをSTEP_OWNER_REVIEW以外にする
            $jobMaster->job_review_status_id = JobReviewStatus::STEP_REVIEW_OK;
            $jobMaster->save(false);

            // ※モデルを更新してリレーションデータも更新する
            $model = new JobReview();
            $model->load([
                $model->formName() => [
                    'job_master_id' => $jobMasterId,
                    'job_review_status_id' => JobReviewStatus::STEP_OWNER_REVIEW,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('job_review_status_id'))->true();
        });
        $this->specify('正しいチェック', function () use ($jobMasterId) {
            $this->setIdentity(Manager::OWNER_ADMIN);

            // 求人の審査ステータスをSTEP_OWNER_REVIEWにする
            $jobMaster = JobMaster::findOne($jobMasterId);
            $jobMaster->job_review_status_id = JobReviewStatus::STEP_OWNER_REVIEW;
            $jobMaster->save(false);

            $model = new JobReview();
            $model->load([
                $model->formName() => [
                    'review' => 1,
                    'job_master_id' => $jobMasterId,
                    'job_review_status_id' => JobReviewStatus::STEP_OWNER_REVIEW,
                ],
            ]);

            $model->validate();
            verify($model->hasErrors('review'))->false();
            verify($model->hasErrors('job_master_id'))->false();
            verify($model->hasErrors('job_review_status_id'))->false();
        });
    }

    /**
     * 初期化メソッドテスト
     */
    public function testLoadJob()
    {
        $this->specify('審査依頼時', function () {
            /** @var JobMaster $jobMaster */
            $jobMaster = JobMaster::find()->one();
            $model = new JobReview();
            $model->loadJob($jobMaster, true);
            verify($model->scenario)->equals(JobReview::SCENARIO_DEFAULT);
            verify($model->job_master_id)->equals($jobMaster->id);
            verify($model->job_review_status_id)->equals($jobMaster->job_review_status_id);
        });
        $this->specify('審査時', function () {
            /** @var JobMaster $jobMaster */
            $jobMaster = JobMaster::find()->one();
            $model = new JobReview();
            $model->loadJob($jobMaster, false);
            verify($model->scenario)->equals(JobReview::SCENARIO_REVIEW);
            verify($model->job_master_id)->equals($jobMaster->id);
            verify($model->job_review_status_id)->equals($jobMaster->job_review_status_id);
        });
    }

    /**
     * 通知先説明テスト
     */
    public function testNotificationHint()
    {
        $jobId = 101;
        $mailLabel = Yii::$app->functionItemSet->job->attributeLabels['application_mail'];

        $this->specify('運営元管理者', function () use ($mailLabel, $jobId) {
            /** @var JobMaster $jobMaster */
            $jobMaster = JobMaster::findOne($jobId);
            // 運営元でログイン
            $this->setIdentity(Manager::OWNER_ADMIN);

            // 代理店審査あり
            $model = new JobReview();
            $model->job_master_id = $jobMaster->id;
            $clientMaster = ClientMaster::find()->one();
            CorpMaster::updateAll(['corp_review_flg' => true], ['id' => $jobMaster->clientMaster->corp_master_id]);
            $okMsg = "審査OK・・・原稿の{$mailLabel}に通知されます。";
            $ngMsg = "審査NG・・・所属する代理店・原稿の{$mailLabel}に通知されます。";
            $msg = '※通知先について<br>' . $okMsg . '<br>' . $ngMsg . '<br>';
            verify($model->notificationHint())->equals($msg);

            // 代理店審査なし
            $model = new JobReview();
            $model->job_master_id = $jobMaster->id;
            CorpMaster::updateAll(['corp_review_flg' => false], ['id' => $jobMaster->clientMaster->corp_master_id]);
            $ngMsg = "審査NG・・・原稿の{$mailLabel}に通知されます。";
            $msg = '※通知先について<br>' . $okMsg . '<br>' . $ngMsg . '<br>';
            verify($model->notificationHint())->equals($msg);
        });
        $this->specify('代理店管理者', function () use ($mailLabel, $jobId) {
            // 代理店でログイン
            $this->setIdentity(Manager::CORP_ADMIN);

            $model = new JobReview();
            $model->job_master_id = $jobId;
            $okMsg = "審査OK・・・運営元に通知されます。";
            $ngMsg = "審査NG・・・原稿の{$mailLabel}に通知されます。";
            $msg = '※通知先について<br>' . $okMsg . '<br>' . $ngMsg . '<br>';
            verify($model->notificationHint())->equals($msg);
        });
    }

    /**
     * フィールドテスト
     */
    public function testFields()
    {
        $model = new JobReview();
        $model->job_review_status_id = JobReviewStatus::STEP_CORP_REVIEW_NG;
        $fileds = $model->fields();
        verify(count($fileds))->equals(1);
        // 審査ステータスクロージャーテスト
        verify($fileds['job_review_status_id']($model))->equals($model->jobReviewStatus->name);
    }
}
