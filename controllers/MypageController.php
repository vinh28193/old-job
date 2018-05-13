<?php

namespace app\controllers;

use app\common\controllers\CommonController;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\models\JobMasterDisp;
use app\models\Apply;
use app\models\ApplyAuth;

/**
 * マイページコントローラ
 * @author Yukinori Nakamura <y_nakamura@id-frontier.jp>
 */
class MypageController extends CommonController
{

    /**
     * ビヘイビア設定
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * indexアクション
     * @return mixed
     */
    public function actionIndex()
    {
        
    }

    /**
     * 応募詳細画面
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEntryHistory()
    {
        $act = ArrayHelper::getValue($this->post, 'act');
        
        $applicationAuth = new ApplyAuth();
        if (is_null($act)) {
            //認証画面処理
            return $this->render('entry-auth', [
                        'model' => $applicationAuth,
            ]);
        }

        //応募内容取得
        $apply = Apply::findOne([
                    'application_no' => $this->post['ApplyAuth']['applicationId'],
                    'name_sei' => $this->post['ApplyAuth']['nameSei'],
                    'name_mei' => $this->post['ApplyAuth']['nameMei'],
                    'mail_address' => $this->post['ApplyAuth']['mailAddress']
        ]);

        //認証判定
        if (is_null($apply)) {
            $errorMessage = Yii::t('app', '認証に失敗しました。');
            $applicationAuth->load($this->post);
            return $this->render('entry-auth', [
                        'model' => $applicationAuth,
                        'errorMessage' => $errorMessage,
            ]);
        }

        //--------------------
        //応募詳細画面
        //--------------------
        // todo 何故relationを使わずにこんな回りくどいことを？
        $apply->setScenario(Apply::SCENARIO_USER_REGISTER);
        //モデルにデータをセット
        $apply->load($this->post);

        $jobMaster = JobMasterDisp::findOne([$apply->job_master_id]);
        //ID指定なし、または見つからないときは404
        if (is_null($apply->job_master_id) || is_null($jobMaster)) {
            throw new NotFoundHttpException;
        }

        return $this->render('entry-history', [
                    'jobMaster' => $jobMaster,
                    'apply' => $apply,
        ]);
    }

}
