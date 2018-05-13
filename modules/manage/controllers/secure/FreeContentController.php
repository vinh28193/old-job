<?php

namespace app\modules\manage\controllers\secure;

use app\common\AccessControl;
use app\models\manage\SiteHtml;
use app\modules\manage\controllers\CommonController;
use app\models\FreeContent;
use app\modules\manage\models\forms\FreeContentElementForm;
use app\modules\manage\models\forms\FreeContentForm;
use app\modules\manage\models\search\FreeContentSearch;
use Exception;
use kartik\widgets\ActiveForm;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * FreeContentController implements the CRUD actions for FreeContent model.
 */
class FreeContentController extends CommonController
{
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
                'only' => ['list', 'update', 'create', 'delete', 'preview', 'copy', ''],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'update', 'create', 'delete', 'preview', 'copy'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * 一覧ページ
     * @return string
     */
    public function actionList()
    {
        $model = new FreeContentSearch();
        $dataProvider = $model->search($this->get);

        return $this->render('list', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'listItems' => [
                ['type' => 'checkBox'],
                ['type' => '', 'attribute' => 'title'],
                ['type' => '', 'attribute' => 'keyword'],
                ['type' => '', 'attribute' => 'description'],
                ['type' => '', 'attribute' => 'url', 'format' => 'newWindowUrl', 'usePopover' => false],
                ['type' => '', 'attribute' => 'valid_chk', 'format' => 'isPublished'],
                ['type' => 'operation', 'buttons' => '{update} {copy} {preview}'],
            ],
        ]);
    }

    /**
     * 新規作成ページ
     * @return string
     */
    protected function create()
    {
        $model = new FreeContentForm();
        return $this->render('create', ['model' => $model]);
    }

    /**
     * 更新ページ
     * @param int $id
     * @return string
     */
    protected function update($id)
    {
        $model = $this->findModel($id);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 作成処理
     * @return Response
     * @throws Exception
     */
    protected function createRegister()
    {
        // エレメントの登録が無いと例外
        if (empty($this->post['FreeContentElementForm'])) {
            throw new Exception();
        }
        // model準備
        $model = new FreeContentForm();
        $model->load($this->post);
        // elementモデル群準備
        $elements = FreeContentElementForm::loadMultipleAndIndex($this->post['FreeContentElementForm']);
        if (!$elements) {
            throw new Exception();
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save(true) || !FreeContentElementForm::saveMultiple($elements, $model)) {
                throw new Exception();
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $this->redirect(['complete', 'isUpdate' => false]);
    }

    /**
     * 更新処理
     * @param int $id
     * @return Response
     * @throws Exception
     */
    protected function updateRegister($id)
    {
        // エレメントの登録が無いと例外
        if (empty($this->post['FreeContentElementForm'])) {
            throw new Exception();
        }

        // model準備
        $model = $this->findModel($id);
        $model->load($this->post);

        // elementモデル群準備
        $oldElements = $model->elements;
        $newElements = FreeContentElementForm::loadMultipleAndIndex($this->post['FreeContentElementForm']);

        if (!$newElements) {
            throw new Exception();
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // レコード更新及び画像のアップロード
            if (!$model->save() || !FreeContentElementForm::saveMultiple($newElements, $model)) {
                throw new Exception();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        FreeContentElementForm::deleteUnusedFiles($oldElements);

        return $this->redirect(['complete', 'isUpdate' => true]);
    }

    /**
     * コピー画面アクション
     * @param $id
     * @return string
     */
    public function actionCopy($id)
    {
        if (isset($this->post['complete'])) {
            return $this->createRegister();
        }

        return $this->copy($id);
    }

    /**
     * コピー画面
     * @param $id
     * @return string
     */
    protected function copy($id)
    {
        $model = $this->findModel($id);
        // 新規モデル化する前にリレーションを呼び出しておく
        $model->elements;
        // findしてきたモデルを新規モデル化
        $model->setIsNewRecord(true);
        $model->id = null;
        // todo タイトルエラーを防ぐためにjob側に倣ったが、そもそもメニューレコードを追加するべきかもしれない
        Yii::$app->requestedRoute = 'manage/secure/free-content/create';
        return $this->render('create', ['model' => $model]);
    }

    /**
     * 完了画面
     * @param $isUpdate
     * @return string
     */
    public function actionComplete($isUpdate)
    {
        return $this->render('complete', ['isUpdate' => $isUpdate]);
    }

    /**
     * 削除する
     * @return Response
     */
    public function actionDelete()
    {
        $this->deleteByGridCheckBox(new FreeContentSearch());
        // postからqueryパラメータ以外を除去してリダイレクト
        return $this->redirect(['list'] + $this->removeExtraParams($this->post));
    }

    /**
     * formからのプレビュー
     * @param string $mode
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionFormPreview($mode)
    {
        if (empty($this->post['FreeContentElementForm'])) {
            throw new NotFoundHttpException();
        }

        if ($mode == 'PC') {
            $this->layout = '@app/views/layouts/main';
        } elseif ($mode == 'Mobile') {
            $this->layout = '@app/views/layouts/sp/main';
        } else {
            throw new NotFoundHttpException();
        }

        $this->view->params['siteHtml'] = $this->findSiteHtml();

        // model準備
        $model = new FreeContentForm();

        $model->load($this->post);

        // elementモデル群準備
        $elements = FreeContentElementForm::loadMultipleAndIndex($this->post['FreeContentElementForm']);

        $model->populateRelation('elements', $elements);

        return $this->render('@app/views/contents/index', ['model' => $model]);
    }

    /**
     * list画面からのプレビュー
     * @param $id
     * @return string
     */
    public function actionPreview($id)
    {
        $this->layout = '@app/views/layouts/main';
        $this->view->params['siteHtml'] = $this->findSiteHtml();
        $model = $this->findModel($id);
        return $this->render('@app/views/contents/index', ['model' => $model]);
    }

    /**
     * FreeContentのAjaxValidation
     * @param null $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAjaxValidation($id = null)
    {
        if (Yii::$app->request->isAjax && isset($this->post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $id ? FreeContentForm::findOne([$id]) : new FreeContentForm();
            $model->load($this->post);

            return ActiveForm::validate($model);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * モデルを取得する
     * @param integer $id
     * @return FreeContent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = FreeContentForm::findOne(['id' => $id]);
        if ($model) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * todo preview全般で必要になるので共通化する
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

    /**
     * 関連レコードも含めて削除する
     * todo 影響範囲が広いので今回はやらないが、基底に適用する
     * @param FreeContentSearch $searchModel
     * @return array
     */
    protected function deleteByGridCheckBox($searchModel)
    {
        // 削除するidを取得してそれを元に削除して削除件数をセット
        $deleteIds = $searchModel->deleteSearch($this->post);
        $deleteCount = $searchModel->deleteAllData($deleteIds);
        $this->session->setFlash('deleteCount', $deleteCount);
    }
}
