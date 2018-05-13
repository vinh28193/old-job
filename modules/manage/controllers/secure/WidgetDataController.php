<?php

namespace app\modules\manage\controllers\secure;

use app\common\AccessControl;
use Exception;
use proseeds\widgets\TableForm;
use Yii;
use yii\web\Response;
use app\models\manage\WidgetData;
use app\models\manage\WidgetDataSearch;
use app\modules\manage\controllers\CommonController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WidgetDataController implements the CRUD actions for WidgetData model.
 */
class WidgetDataController extends CommonController
{
    const PJAX_ID = 'inputFields';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'update', 'create', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'update', 'create', 'delete'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Lists all WidgetData models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new WidgetDataSearch();
        $dataProvider = $searchModel->search($this->get);
        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 削除アクション
     */
    public function actionDelete()
    {
        $searchModel = new WidgetDataSearch();
        $models = $searchModel->deleteSearch($this->post);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 画像レコード削除
            $deleteCount = WidgetDataSearch::deleteRecords($models);
            $this->session->setFlash('deleteCount', $deleteCount);
            $transaction->commit();
            WidgetDataSearch::deleteFiles($models);
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        // postからqueryパラメータ以外を除去してリダイレクト
        return $this->redirect(['list'] + $this->removeExtraParams($this->post));
    }

    /**
     * @return string
     */
    public function create()
    {
        $model = new WidgetData();
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 書き込みシナリオ
     * @return string
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function createRegister()
    {
        $model = new WidgetData();
        $model->load($this->post);
        $model->scenario = WidgetData::SCENARIO_REGISTER;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save() || !$model->saveRelations()) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            // fileのpostが存在し、fileが必要なwidgetであればfileをupload
            if ($model->fileInstance()) {
                $model->saveFiles();
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $this->render('complete');
    }

    /**
     * アップデートフォームシナリオ
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function update($id)
    {
        $model = $this->findModel($id);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 書き込みシナリオ
     * @param int $id
     * @return string
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function updateRegister($id)
    {
        $model = $this->findModel($id);
        $model->load($this->post);
        $model->scenario = WidgetData::SCENARIO_REGISTER;
        if (!$model->validate()) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save(false) || !$model->updateRelations()) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            if ($model->fileInstance()) {
                // fileのpostが存在し、fileが必要なwidgetであればfileをupload
                $model->saveFiles();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $this->render('complete');
    }

    /**
     * 日付のみajaxValidate
     * @param null
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAjaxValidation($id = null)
    {
        if (Yii::$app->request->isAjax && isset($this->post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($id) {
                $model = $this->findModel($id);
            } else {
                $model = new WidgetData();
            }
            $model->load($this->post);
            return TableForm::validate($model);
        }
        throw new NotFoundHttpException();
    }

    /**
     * pjaxで呼び出されるfieldのview
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInputFields()
    {
        if (Yii::$app->request->isAjax) {
            $model = new WidgetData();
            $model->widget_id = $this->post['widgetId'];
            return $this->renderAjax('_input-fields', [
                'model' => $model,
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return string
     */
    public function actionComplete()
    {
        return $this->render('complete');
    }

    /**
     * Finds the WidgetData model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WidgetData the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WidgetData::find()->with('widgetDataArea')->where(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
