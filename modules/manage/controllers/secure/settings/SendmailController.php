<?php

namespace app\modules\manage\controllers\secure\settings;

use yii;
use app\models\manage\SendMailSet;
use app\models\manage\SendMailSetSearch;
use app\modules\manage\controllers\CommonController;
use app\common\AccessControl;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

/**
 * SendmailController implements the CRUD actions for SendMailSet model.
 */
class SendmailController extends CommonController
{
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
     * Lists all SendMailSet models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new SendMailSetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate()
    {
        $id = ArrayHelper::getValue($this->get, 'id');
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['list']);
        } else {
            //todo 例外処理のエラーメッセージなど決まれば修正する
            return $this->redirect(['list']);
        }
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPjaxModal($id)
    {
        $model = $this->findModel($id);

        return $this->renderAjax('_item-update', [
            'model' => $model,
        ]);
    }

    /**
     * モデル取得処理
     * @param integer $id
     * @return SendMailSet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SendMailSet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
