<?php

namespace app\controllers;

use app\common\mail\MailSender;
use app\common\controllers\CommonController;
use app\models\manage\Policy;
use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\validators\EmailValidator;
use yii\web\NotFoundHttpException;
use yii\db\Exception;
use app\models\JobMasterDisp;
use app\models\Apply;
use app\models\manage\SendMailSet;

/**
 * 応募機能コントローラ
 * @author Yukinori Nakamura <y_nakamura@id-frontier.jp>
 */
class ApplyController extends CommonController
{

    /**
     * 応募情報入力画面
     * @param int $job_no 仕事ID
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex($job_no = null)
    {
        $jobMaster = $this->findModel($job_no);
        //ID指定なし、または見つからないときは404
        if (is_null($job_no) || is_null($jobMaster)) {
            throw new NotFoundHttpException;
        }

        $apply = new Apply();
        $apply->setScenario(Apply::SCENARIO_USER_REGISTER);
        $apply->load($this->post);

        $policy = Policy::find()->where(['policy_no' => 1])->one();

        return $this->render('index', [
            'jobMaster' => $jobMaster,
            'apply' => $apply,
            'policy' => $policy,
        ]);
    }

    /**
     * 応募情報確認画面
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionConfirm()
    {
        $job_no = ArrayHelper::getValue($this->post, 'job_no');

        $jobMaster = $this->findModel($job_no);
        //ID指定なし、または見つからないときは404
        if (is_null($job_no) || is_null($jobMaster)) {
            throw new NotFoundHttpException;
        }

        $apply = new Apply();
        $apply->setScenario(Apply::SCENARIO_USER_REGISTER);
        //ロードかバリデーション検証に失敗したら404
        if (!$apply->load($this->post) || !$apply->validate()) {
            throw new NotFoundHttpException;
        }

        return $this->render('confirm', [
            'jobMaster' => $jobMaster,
            'apply' => $apply,
        ]);
    }

    /**
     * 応募完了画面
     * @param int $job_no 仕事No
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionComplete($job_no = null)
    {
        if (!Yii::$app->session->getFlash('applicationNo')) {
            $this->redirect('/');
        }
        //AccessLogのGoogleAnalytics用のパラメータを挿入
        $this->isAnalytics = true;
        $this->analyticsParam = Yii::$app->session->getFlash('jobMasterId') . ',' . Yii::$app->session->getFlash('applicationId');

        return $this->render('complete', compact('job_no'));
    }

    /**
     * 応募登録処理
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionRegister()
    {
        $job_no = ArrayHelper::getValue($this->post, 'job_no');

        $apply = new Apply();
        //job_no指定なし、またはロード失敗時は404
        if (is_null($job_no) || !$apply->load($this->post)) {
            throw new NotFoundHttpException;
        }

        //==============================
        // 登録処理
        //==============================
        //応募機器セット
        $apply->setCarrierTypeByUserAgent(Yii::$app->request->headers->get('User-Agent'));
        //応募機器がnull または、保存実行失敗時は404
        if (is_null($apply->carrier_type) || !$apply->save()) {
            throw new NotFoundHttpException(Yii::t('app', '応募登録エラー'));
        }

        //==============================
        // メール送信処理
        //==============================
        // 応募完了時は応募者に確認メールを、求人原稿(job_master.application_mail)と運営元(site_master.application_mail)宛に通知メールを送る
        $mailSets = ArrayHelper::index(SendMailSet::find()->where(['mail_type' => SendMailSet::MAIL_TYPE_APPLY_MAIL])->all(), 'mail_to');
        $mailSender = new MailSender();
        foreach ($mailSets as $mailSet) {
            $mailSet->model = $apply;
            $mailSender->sendAutoMail($mailSet);
        }

        Yii::$app->session->setFlash('applicationNo', $apply->application_no);
        Yii::$app->session->setFlash('applicationId', $apply->id);
        Yii::$app->session->setFlash('jobMasterId', $apply->job_master_id);

        $this->redirect(Url::toRoute(['apply/complete', 'job_no' => $job_no]));
    }

    /**
     * JobMasterDispモデルの取得
     * @param int $job_no 仕事No
     * @return JobMasterDisp
     * @throws NotFoundHttpException
     */
    protected function findModel($job_no)
    {
        /** @var JobMasterDisp $model */
        $model = JobMasterDisp::find()->where(['job_no' => $job_no])->active()->one();
        $validator = new EmailValidator();
        if (isset($model) && $validator->validate($model->application_mail)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
