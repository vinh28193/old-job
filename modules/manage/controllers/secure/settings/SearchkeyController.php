<?php

namespace app\modules\manage\controllers\secure\settings;


use Yii;
use app\models\manage\SearchkeyMaster;
use app\models\manage\SearchkeyMasterSearch;
use app\modules\manage\controllers\CommonController;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use app\common\AccessControl;

/**
 * SearchkeyController implements the CRUD actions for SearchkeySetting model.
 */
class SearchkeyController extends CommonController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'update', 'pjax-modal'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'update', 'pjax-modal'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * 一覧画面アクション
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new SearchkeyMasterSearch();
        $dataProvider = $searchModel->search($this->get);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 更新アクション
     * @return \yii\web\Response
     */
    public function actionUpdate()
    {
        $model = $this->findModel($this->get['id'] ?? null);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // ↓ routing設定保存処理
            SearchkeyMaster::saveRouteSetting();
            $this->session->setFlash('updateComment', Html::tag('p', Yii::t('app', '更新が完了しました。'), ['class' => 'alert alert-warning']));
            return $this->redirect(['list']);
        } else {
            return $this->redirect(['list']);
        }
    }

    /**
     * 変更モーダルpjaxアクション
     * @param $id
     * @return string
     */
    public function actionPjaxModal($id)
    {
        $model = $this->findModel($id);

        return $this->renderAjax('_item-update', [
            'model' => $model,
        ]);
    }

    /**
     * idからモデルを取得する
     * @param integer $id
     * @return SearchkeyMaster
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = SearchkeyMaster::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
