<?php

namespace app\modules\manage\controllers\secure;

use app\common\AccessControl;
use app\common\mail\MailSender;
use app\models\MailSend;
use app\models\manage\JobMaster;
use app\models\manage\JobReviewStatus;
use app\models\manage\SendMailSet;
use app\modules\manage\controllers\CommonController;
use app\modules\manage\models\JobReview;
use app\modules\manage\models\Manager;
use Yii;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * 原稿審査コントローラ
 * @property JobMaster $model
 */
class JobReviewController extends CommonController
{
    /** アクセス管理するアクション */
    const ACCSESS_CONTROL_ACTIONS = ['pjax-modal', 'review', 'request', 'request-complete'];
    const POST_ACTIONS = ['review', 'request-complete'];

    /** @var JobMaster */
    public $model;

    /**
     * ビヘイビア
     * @return array
     */
    public function behaviors()
    {
        $model = $this->model;
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'review' => ['post'],
                    'request-complete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => self::ACCSESS_CONTROL_ACTIONS,
                'rules' => [
                    // 審査依頼画面
                    [
                        'allow' => true,
                        'actions' => ['request', 'request-complete'],
                        'roles' => [Manager::CORP_ADMIN, Manager::CLIENT_ADMIN],
                        'matchCallback' => function ($rule, $action) use ($model) {
                            return Yii::$app->tenant->tenant->review_use && Yii::$app->user->can('updateJob', ['jobMaster' => $model]);
                        },
                    ],
                    // 審査画面
                    [
                        'allow' => true,
                        'actions' => ['pjax-modal', 'review'],
                        'roles' => [Manager::OWNER_ADMIN, Manager::CORP_ADMIN],
                        'matchCallback' => function ($rule, $action) use ($model) {
                            return Yii::$app->tenant->tenant->review_use && $model->isReview() && Yii::$app->user->can('updateJob', ['jobMaster' => $model]);
                        },
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (in_array($action->id, self::POST_ACTIONS)) {
            $this->model = $this->findModel(ArrayHelper::getValue(ArrayHelper::getValue($this->post, 'JobReview'), 'job_master_id'));
        } elseif (in_array($action->id, self::ACCSESS_CONTROL_ACTIONS)) {
            $this->model = $this->findModel(ArrayHelper::getValue($this->get, 'id'));
        }
        return parent::beforeAction($action);
    }

    /**
     * モデルの取得
     * @param integer $id 求人原稿ID
     * @return JobMaster モデル
     * @throws NotFoundHttpException モデルが見つからなかったとき
     */
    protected function findModel($id)
    {
        /** @var JobMaster $model */
        $model = JobMaster::find()
            ->where([JobMaster::tableName() . '.id' => $id])->one();
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 審査依頼／審査処理
     * @param string $scenario
     * @throws Exception|NotFoundHttpException
     */
    private function review($scenario = 'default')
    {
        // 審査機能がOFFの場合は404エラー
        if (!Yii::$app->tenant->tenant->review_use) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        // 審査モデル作成
        $jobReview = new JobReview();
        $jobReview->scenario = $scenario;
        if (!$jobReview->load(Yii::$app->request->post())) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        // 求人モデル取得
        $jobMaster = $this->model;

        // 審査後ステータスを設定
        $jobMaster->job_review_status_id = $jobReview->job_review_status_id;

        // 保存
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$jobReview->validate() || !$jobReview->save(false)) {
                throw new Exception('審査履歴 登録エラー');
            }
            if (!$jobMaster->validate() || !$jobMaster->save(false)) {
                throw new Exception('job_master 更新エラー');
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            //todo 例外処理のエラーメッセージなど決まれば修正する
            throw $e;
        }

        // 審査メール送信
        $mailTypeId = $jobReview->job_review_status_id === JobReviewStatus::STEP_REVIEW_OK ? MailSend::TYPE_JOB_REVIEW_COMPLETE : MailSend::TYPE_JOB_REVIEW;
        $mailSet = SendMailSet::findOne(['mail_type_id' => $mailTypeId]);
        $mailSet->model = $jobReview;

        $mailSender = new MailSender();
        $mailSender->sendAutoMail($mailSet);
    }


    /**
     * 完了画面
     * @return string
     */
    public function actionComplete(){
        $isRequest = $this->get['isRequest'] ?? false;
        return $this->render('complete', ['isRequest' => $isRequest]);
    }

    /**
     * 審査依頼画面
     *   $idはbeforeAction内でしか使用してないが、actionの呼び出し方を分かりやすくするために引数は残したままとしておく
     * @param integer $id
     * @return string 描画結果
     * @throws NotFoundHttpException
     */
    public function actionRequest($id)
    {
        $isUpdate = $this->get['isUpdate'] ?? false;

        // 以下の場合、404エラー
        //   審査機能がOFF
        //   運営元管理者
        //   必要情報がない(beforeAction内のfindModelメソッド)
        if (!Yii::$app->tenant->tenant->review_use ||
            Yii::$app->user->identity->isOwner()) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        // 審査モデル作成
        $jobReview = new JobReview();
        $jobReview->loadJob($this->model, true);

        return $this->render('request', [
            'jobReview' => $jobReview,
            'isUpdate' => $isUpdate,
        ]);
    }

    /**
     * 審査依頼処理
     * @return string 描画結果
     * @throws NotFoundHttpException
     */
    public function actionRequestComplete()
    {
        // 審査依頼
        $this->review();

        return $this->redirect(['complete', 'isRequest' => true]);
    }

    /**
     * 審査モーダル
     *   $idはbeforeAction内でしか使用してないが、actionの呼び出し方を分かりやすくするために引数は残したままとしておく
     * @param integer $id
     * @return string 描画結果
     * @throws NotFoundHttpException
     */
    public function actionPjaxModal($id)
    {
        // 以下の場合、404エラー
        //   審査機能がOFF
        //   必要情報がない(beforeAction内のfindModelメソッド)
        if (!Yii::$app->tenant->tenant->review_use) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        // 審査用モデル作成
        $jobReview = new JobReview();
        $jobReview->loadJob($this->model, false);

        return $this->renderAjax('form/_review-modal', [
            'jobReview' => $jobReview,
        ]);
    }

    /**
     * 審査アクション
     * @return string 描画結果
     * @throws NotFoundHttpException
     */
    public function actionReview()
    {
        // 審査
        $this->review(JobReview::SCENARIO_REVIEW);

        return $this->redirect(['complete', 'isRequest' => false]);
    }

    /**
     * 審査モーダル用Ajaxバリデーション
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAjaxValidation()
    {
        if (Yii::$app->request->isAjax && isset($this->post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new JobReview();
            $model->scenario = JobReview::SCENARIO_REVIEW;
            $model->load($this->post);

            return ActiveForm::validate($model);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
