<?php
namespace models\manage;

use app\models\manage\JobReviewStatus;
use app\modules\manage\models\Manager;
use Yii;
use proseeds\models\Tenant;
use tests\codeception\unit\JmTestCase;

/**
 * 審査ステータステスト
 */
class JobReviewStatusTest extends JmTestCase
{
    /** @var array $_baseStatuses 審査ステータス配列*/
    private $_baseStatuses = [
        JobReviewStatus::STEP_JOB_EDIT => '審査前（修正中）',
        JobReviewStatus::STEP_CORP_REVIEW_NG => '代理店審査NG',
        JobReviewStatus::STEP_OWNER_REVIEW_NG => '運営元審査NG',
        JobReviewStatus::STEP_CORP_REVIEW => '代理店審査中',
        JobReviewStatus::STEP_OWNER_REVIEW => '運営元審査中',
        JobReviewStatus::STEP_REVIEW_OK => '審査完了',
    ];

    /**
     * ラベル名テスト
     */
    public function testAttributeLabel()
    {
        verify(JobReviewStatus::attributeLabel())->equals('審査ステータス');
    }

    /**
     * プルダウン用データ取得テスト
     */
    public function testReviewStatuses()
    {
        $statuses = JobReviewStatus::reviewStatuses();
        verify($statuses)->equals($this->_baseStatuses);
        $statuses = JobReviewStatus::reviewStatuses(true);
        verify($statuses)->equals(['' => 'すべて'] + $this->_baseStatuses);
    }

    /**
     * 審査ステータス名を返すテスト
     */
    public function testGetName()
    {
        foreach ($this->_baseStatuses as $id => $name) {
            $model = new JobReviewStatus();
            $model->id = $id;
            verify($name)->equals($model->name);
        }
    }

    /**
     * 管理者権限別、代理店審査有無別に審査対象ステータス取得テスト
     */
    public function testReviewTargetStatusesByRole()
    {
        $this->specify('運営元管理者', function () {
            $reviewStatuses = JobReviewStatus::reviewTargetStatusesByRole(Manager::OWNER_ADMIN, true);
            verify($reviewStatuses)->equals([JobReviewStatus::STEP_OWNER_REVIEW]);

            $reviewStatuses = JobReviewStatus::reviewTargetStatusesByRole(Manager::OWNER_ADMIN, false);
            verify($reviewStatuses)->equals([JobReviewStatus::STEP_OWNER_REVIEW]);
        });
        $this->specify('代理店管理者', function () {
            $reviewStatuses = JobReviewStatus::reviewTargetStatusesByRole(Manager::CORP_ADMIN, true);
            verify($reviewStatuses)->equals([JobReviewStatus::STEP_CORP_REVIEW]);

            $reviewStatuses = JobReviewStatus::reviewTargetStatusesByRole(Manager::CORP_ADMIN, false);
            verify($reviewStatuses)->equals([]);
        });
        $this->specify('掲載企業管理者', function () {
            $reviewStatuses = JobReviewStatus::reviewTargetStatusesByRole(Manager::CLIENT_ADMIN, true);
            verify($reviewStatuses)->equals([]);

            $reviewStatuses = JobReviewStatus::reviewTargetStatusesByRole(Manager::CLIENT_ADMIN, false);
            verify($reviewStatuses)->equals([]);
        });
    }

    /**
     * 管理者別、審査OK/NG別、代理店審査有無別に審査後ステータス取得テスト
     */
    public function testReviewStatusesByRole()
    {
        // 審査機能をONにする
        Tenant::updateAll(['review_use' => 1]);

        $this->specify('運営元管理者', function () {
            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::OWNER_ADMIN, true, true);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_REVIEW_OK);

            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::OWNER_ADMIN, true, false);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_OWNER_REVIEW_NG);

            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::OWNER_ADMIN, false, true);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_REVIEW_OK);

            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::OWNER_ADMIN, false, false);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_OWNER_REVIEW_NG);
        });
        $this->specify('代理店管理者', function () {
            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::CORP_ADMIN, true, true);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_OWNER_REVIEW);

            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::CORP_ADMIN, true, false);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_CORP_REVIEW_NG);

            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::CORP_ADMIN, false, true);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_OWNER_REVIEW);

            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::CORP_ADMIN, false, false);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_CORP_REVIEW_NG);
        });
        $this->specify('掲載企業管理者', function () {
            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::CLIENT_ADMIN, true, true);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_CORP_REVIEW);

            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::CLIENT_ADMIN, true, false);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_CORP_REVIEW);

            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::CLIENT_ADMIN, false, true);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_OWNER_REVIEW);

            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::CLIENT_ADMIN, false, false);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_OWNER_REVIEW);
        });
        $this->specify('例外', function () {
            $reviewStatus = JobReviewStatus::reviewStatusByRole('test', true, true);
            verify($reviewStatus)->null();
        });
        $this->specify('審査機能OFF', function () {
            Yii::$app->tenant->tenant->review_use = 0;
            $reviewStatus = JobReviewStatus::reviewStatusByRole(Manager::CLIENT_ADMIN, true, true);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_REVIEW_OK);
        });
    }

    /**
     * 求人原稿登録時の審査ステータス取得テスト
     */
    public function testJobRegisterReviewStatus()
    {
        // 審査機能をONにする
        Yii::$app->tenant->tenant->review_use = 1;

        $this->specify('運営元管理者', function () {
            $reviewStatus = JobReviewStatus::jobRegisterReviewStatus(Manager::OWNER_ADMIN);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_REVIEW_OK);
        });

        $this->specify('代理店管理者', function () {
            $reviewStatus = JobReviewStatus::jobRegisterReviewStatus(Manager::CORP_ADMIN);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_JOB_EDIT);
        });

        $this->specify('掲載企業管理者', function () {
            $reviewStatus = JobReviewStatus::jobRegisterReviewStatus(Manager::CLIENT_ADMIN);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_JOB_EDIT);
        });

        $this->specify('例外', function () {
            $reviewStatus = JobReviewStatus::jobRegisterReviewStatus('test');
            verify($reviewStatus)->null();
        });
        $this->specify('審査機能OFF', function () {
            Yii::$app->tenant->tenant->review_use = 0;
            $reviewStatus = JobReviewStatus::jobRegisterReviewStatus(Manager::CLIENT_ADMIN);
            verify($reviewStatus)->equals(JobReviewStatus::STEP_REVIEW_OK);
        });
    }
}
