<?php

namespace app\controllers;

use app\common\controllers\CommonController;
use Yii;
use yii\web\NotFoundHttpException;
use app\models\MailSend;
use app\models\manage\Policy;
use app\models\manage\SendMailSet;
use app\models\manage\InquiryMaster;
use app\common\mail\MailSender;

/**
 * Class InquiryController
 * @package app\controllers
 * @property $_POST $post
 */
class InquiryController extends CommonController
{

    /**
     * indexアクション
     * @return mixed
     */
    public function actionIndex()
    {
        $inquiry = new InquiryMaster;
        if ($this->post){
            $inquiry->load($this->post);
        }
        $policy = Policy::find()->where(['policy_no' => 5])->one();
        return $this->render('index', [
            'inquiry' => $inquiry,
            'policy' => $policy
        ]);
    }

    /**
     * confirmアクション
     * @return mixed
     */
    public function actionConfirm()
    {
        $inquiry = new InquiryMaster;
        $inquiry->load($this->post);
        return $this->render('confirm', [
            'inquiry' => $inquiry
        ]);
    }

    /**
     * registerアクション
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionRegister()
    {
        $inquiry = new InquiryMaster;
        if (!$inquiry->load($this->post)) {
            throw new NotFoundHttpException;
        }
        $mailSet = SendMailSet::findOne([
            'mail_type_id' => MailSend::TYPE_INQUILY_NOTIFICATION,
        ]);
        $mailSet->model = $inquiry;
        $mailSet->additionalText = $inquiry->additionalText;
        $mailSender = new MailSender();
        $mailSender->sendAutoMail($mailSet);
        Yii::$app->session->setFlash('inquiry', true);
        return $this->redirect('complete');
    }

    /**
     * completeアクション
     * @return mixed
     */
    public function actionComplete()
    {
        if (!Yii::$app->session->getFlash('inquiry')) {
            $this->redirect('/');
        }
        return $this->render('complete');
    }
}