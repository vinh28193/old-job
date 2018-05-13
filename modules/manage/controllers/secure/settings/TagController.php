<?php

namespace app\modules\manage\controllers\secure\settings;

use app\common\AccessControl;
use app\models\manage\SiteHtml;
use yii;
use app\modules\manage\controllers\CommonController;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\bootstrap\ActiveForm;

/**
 * TagController
 */
class TagController extends CommonController
{

    /**
     * {@inheritDoc}
     * @see \app\modules\manage\controllers\CommonController::behaviors()
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'update'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * 一覧表示アクション
     * @return mixed
     */
    public function actionList()
    {
        $model = $this->findModel();
        return $this->render('list', ['model' => $model]);
    }

    /**
     * 更新アクション
     * @return \yii\web\Response
     */
    public function actionUpdate()
    {
        $model = $this->findModel();

        if ($model->load($this->post) && $model->save()) {
            $this->session->setFlash('updateComment', Html::tag('p', Yii::t('app', '更新が完了しました。'), ['class' => 'alert alert-warning']));
            return $this->redirect(['list']);
        } else {
            $this->session->setFlash('updateComment', Html::tag('p', Yii::t('app', '更新に失敗しました。もう一度登録し直して頂くか、サポートセンターまでお問い合わせ下さい。'), ['class' => 'alert alert-danger']));
            return $this->redirect(['list']);
        }
    }

    /**
     * モデルの取得処理
     * @return SiteHtml the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel()
    {
        if (($model = SiteHtml::find()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * フォーム画面検証用のajaxバリデーションアクション
     * @param $id
     * @return array
     */
    public function actionAjaxValidation($id)
    {
        if ($id) {
            $model = SiteHtml::findOne($id);
        } else {
            $model = new SiteHtml();
        }
        $model->load($this->post);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
}
