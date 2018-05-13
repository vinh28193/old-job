<?php

namespace tests\codeception\_pages\manage\job;

use app\models\manage\JobMaster;
use app\models\manage\JobReviewStatus;
use Yii;
use tests\codeception\_pages\manage\BaseGridPage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class JobSearchPage extends BaseGridPage
{
    public $route = 'manage/secure/job/list';

    /**
     * 審査ステータス検索および、検索結果の審査ステータス名を確認する
     * @param integer $jobId
     */
    public function checkReviewStatus()
    {
        $this->actor->amGoingTo('審査ステータスでの検索処理');
        $statuses = JobReviewStatus::reviewStatuses();

        /** @var \app\modules\manage\models\Manager $identity */
        $identity = Yii::$app->user->identity;
        $reviewTargetStatuses = JobReviewStatus::reviewTargetStatusesByRole($identity->myRole, false);

        // ステータス分ループ
        foreach ($statuses as $statusId => $statusName) {
            // 初期化
            $this->actor->click('クリア');
            $this->actor->wait(2);

            // 一番上の求人IDを取得してステータスIDを更新
            $jobId = $this->grabTableRowId(1);
            $jobMaster = JobMaster::findOne($jobId);
            $jobMaster->job_review_status_id = $statusId;
            $jobMaster->save(false);

            // 更新した値の検索条件で検索
            $this->actor->selectOption('#jobmastersearch-job_review_status_id', $statusId);
            $this->actor->click('この条件で表示する');
            $this->actor->wait(3);

            //ログイン管理者の審査対象に合わせてチェック対象セレクタを変更
            if (in_array($statusId, $reviewTargetStatuses)) {
                $this->actor->see($statusName, '//div[@id="grid_id"]//table/tbody/tr[1]/td[10]/a');
            } else {
                $this->actor->see($statusName, '//div[@id="grid_id"]//table/tbody/tr[1]/td[10]');
            }
        }
    }

    /**
     * 公開／非公開の切り替え
     * @param integer $row
     */
    public function checkChangeDisplay($row)
    {
        $this->actor->wantTo('公開／非公開の切り替えテスト');

        $this->actor->amOnPage($this->getUrl());
        $this->actor->click('クリア');
        $this->actor->wait(3);
        $jobId = $this->grabTableRowId($row);

        // 審査OKを担保
        JobMaster::updateAll(['job_review_status_id' => JobReviewStatus::STEP_REVIEW_OK, 'valid_chk' => 1], ['id' => $jobId]);

        $this->actor->click('クリア');
        $this->actor->wait(3);

        $this->actor->see('公開', '//div[@id="grid_id"]//table/tbody/tr[1]/td[position()=last()-1]');
        $this->changeDisplay($row);
        $this->actor->see('非公開', '//div[@id="grid_id"]//table/tbody/tr[1]/td[position()=last()-1]');
        $this->changeDisplay($row);
        $this->actor->see('公開', '//div[@id="grid_id"]//table/tbody/tr[1]/td[position()=last()-1]');
    }

    /**
     * 公開／非公開の切り替え
     * ※審査OKの求人に対して行うこと。
     * @param integer $row
     */
    public function changeDisplay($row)
    {
        $this->actor->amGoingTo('公開／非公開を切り替える');
        $this->actor->click("//div[@id='grid_id']//table/tbody/tr[$row]/td[position()=last()-1]/a");
        $this->actor->wait(5);
        $this->actor->see('公開／非公開を切り替えてよろしいですか？', '//div[@class="bootbox-body"]');
        $this->actor->click('OK');
        $this->actor->wait(10);
    }
}
