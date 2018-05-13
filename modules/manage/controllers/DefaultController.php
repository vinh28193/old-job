<?php

namespace app\modules\manage\controllers;

use app\modules\manage\models\ManageAuth;
use Yii;
use yii\filters\AccessControl;

class DefaultController extends CommonController
{
    // 未ログイン状態のlayoutが無いのでとりあえずデフォルト
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['login', 'logout'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['login', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * ログイン画面
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('secure/');
        }

        $message = '';
        $model = new ManageAuth();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('secure/');
        }else if(Yii::$app->request->post('doLogin') != null){
            $message = Yii::t('app', 'ログインできません。ログインID と パスワードを再度入力してください。');
        }

        return $this->render('login', [
            'model' => $model,
            'message'=>$message,
        ]);
    }

    /**
     * ログアウトアクション
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Pragma', 'no-cache');

        Yii::$app->user->logout();

        return $this->redirect('login');
    }

    /**
     * 権限拒否画面
     * @return string
     */
    public function actionPermissionDenied()
    {
        $this->layout = 'layout';
        return $this->render('permission-denied');
    }

    /**
     * 404エラー画面
     * @return string
     */
    public function actionError()
    {
        $this->layout = 'layout';
        return $this->render('404');
    }
}
