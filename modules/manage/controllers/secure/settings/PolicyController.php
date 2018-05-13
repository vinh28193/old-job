<?php

namespace app\modules\manage\controllers\secure\settings;

use app\common\AccessControl;
use Yii;
use app\models\manage\Policy;
use app\models\manage\PolicySearch;
use app\models\manage\ManageMenuMain;
use app\modules\manage\controllers\CommonController;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use app\models\manage\SiteHtml;

/**
 * PolicyController implements the CRUD actions for Policy model.
 */
class PolicyController extends CommonController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'update', 'preview', 'previewForm', 'complete', 'upload'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'update', 'preview', 'previewForm', 'complete', 'upload'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Lists all Policy models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new PolicySearch();
        $dataProvider = $searchModel->search($this->get);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
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
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function updateRegister($id)
    {
        $model = $this->findModel($id);
        $model->load($this->post);
        if ($model->validate() && $model->save()) {
            return $this->redirect(['complete']);
        }
        throw new NotFoundHttpException();
    }

    /**
     * Displays a single Policy model.
     * @param integer $id
     * @return mixed
     */
    public function actionPreview($id)
    {
        $model = $this->findModel($id);
        return $this->renderPreview($model);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionPreviewForm($id)
    {
        if ($this->post) {
            $model = $this->findModel($id);
            $post = [];
            $posted = $this->post['Policy'];
            $post['Policy'] = $posted;
            $model->load($post);
            return $this->renderPreview($model);
        }
        throw new Exception;
    }

    /**
     * todo app\controllers\PolicyControllerとまとめられるとこまとめる
     * @param $model Policy
     * @return string
     */
    private function renderPreview($model)
    {
        //　view側でヘッダーフッターのHtmlが必ず要求されるため、コントローラー側で追加する。
        $this->view->params['siteHtml'] = $this->findSiteHtml();
        if ($model->policy_no == Policy::ADMIN_POLICY_NO) {
            // 管理者規約のみviewが違う
            $this->layout = '@app/modules/manage/views/layouts/main';
            return $this->render('@app/views/policy/manage', [
                'model' => $model,
            ]);
        }
        $this->layout = '@app/views/layouts/main';
        return $this->render('@app/views/policy/index', [
            'model' => $model,
        ]);
    }

    /**
     * @return string
     */
    public function actionComplete()
    {
        return $this->render('complete');
    }

    /**
     * Finds the Policy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Policy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Policy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * SiteHtmlモデルの取得
     * @return SiteHtml モデル
     */
    private function findSiteHtml()
    {
        /** @var null|SiteHtml $model */
        $model = SiteHtml::find()->one();
        if (isset($model)) {
            return $model;
        } else {
            return new SiteHtml;
        }
    }
}
