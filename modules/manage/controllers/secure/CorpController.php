<?php

namespace app\modules\manage\controllers\secure;

use app\models\manage\CorpMaster;
use app\models\manage\CorpMasterSearch;
use app\modules\manage\controllers\CommonController;
use Yii;
use app\common\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use proseeds\helpers\ExportHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * CorpController implements the CRUD actions for CorpMaster model.
 */
class CorpController extends CommonController
{
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
                'only' => ['list', 'update', 'create', 'delete', 'detail'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'update', 'delete'],
                        'roles' => ['owner_admin'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('corpListException');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['owner_admin'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('corpCreateException');
                        },
                    ],
                ],
            ],
        ]);
    }

    /**
     * 一覧画面
     * @return mixed
     */
    public function actionList()
    {
        $corpMasterSearch = new CorpMasterSearch();

        $dataProvider = $corpMasterSearch->search($this->get);

        $listItems = [['type' => 'checkBox']];
        foreach (Yii::$app->functionItemSet->corp->listAttributes as $attribute) {
            $listItems[] = ['type' => '', 'attribute' => $attribute];
        };
        // 審査機能ONのときのみ
        if (Yii::$app->tenant->tenant->review_use) {
            $listItems[] = ['type' => '', 'attribute' => 'corp_review_flg', 'format' => 'onOff', 'layout' => '{value}'];
        }
        $listItems[] = ['type' => '', 'attribute' => 'valid_chk', 'format' => 'validChk', 'layout' => '{value}'];
        $listItems[] = ['type' => 'operation', 'buttons' => '{update}'];

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'corpMasterSearch' => $corpMasterSearch,
            'listItems' => $listItems,
        ]);
    }

    /**
     * @inheritdocs
     */
    public function create()
    {
        $model = new CorpMaster();
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function createRegister()
    {
        $model = new CorpMaster();

        $model->load($this->post);

        if ($model->validate()) {
            $model->save();
            $this->redirect(Url::toRoute('complete'));
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function update($id)
    {
        $model = $this->findModel($id);
        $model->load($this->post);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function updateRegister($id)
    {
        $model = $this->findModel($id);
        if ($model->load($this->post) && $model->validate()) {
            $model->save();
            $this->redirect(Url::toRoute('complete') . '?isUpdate=true');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionComplete()
    {
        $isUpdate = isset($this->get['isUpdate']) ? $isUpdate = $this->get['isUpdate'] : false;

        return $this->render('complete', [
            'isUpdate' => $isUpdate,
        ]);
    }

    /**
     * Finds the CorpMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CorpMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CorpMaster::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * CSVダウンロードアクション
     */
    public function actionCsvDownload()
    {
        $searchModel = new CorpMasterSearch();

        $getCorpColumnList = ArrayHelper::getColumn(Yii::$app->functionItemSet->corp->Items, 'column_name');

        $dataProvider = $searchModel->csvSearch($this->get);

        ExportHelper::outputAsCSV(
            $dataProvider,
            'CorpMasterList_' . date('YmdHi') . '.csv',
            $getCorpColumnList + ['valid_chk:validChk']
        );
    }

    /**
     * 削除アクション
     */
    public function actionDelete()
    {
        $this->deleteByGridCheckBox(new CorpMasterSearch());
        // postからqueryパラメータ以外を除去してリダイレクト
        $this->redirect(['list'] + $this->removeExtraParams($this->post));
    }

    /**
     * ユニーク処理用ajaxValidation action
     * @param null $id
     * @return array|bool
     * @throws NotFoundHttpException
     */
    public function actionAjaxValidation($id = null)
    {
        if (Yii::$app->request->isAjax && isset($this->post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($id) {
                $model = $this->findModel($id);
            } else {
                $model = new CorpMaster();
            }
            $model->load($this->post);
            return ActiveForm::validate($model);
        }
        return false;
    }
}
