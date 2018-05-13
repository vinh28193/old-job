<?php

namespace app\modules\manage\controllers\secure;

use app\modules\manage\controllers\CommonController;
use app\modules\manage\models\Manager;
use Exception;
use yii;
use app\common\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use app\models\manage\MediaUploadSearch;
use app\models\manage\MediaUpload;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * MediaUploadController implements the CRUD actions for MediaUploadMaster model.
 */
class MediaUploadController extends CommonController
{
    /**
     * {@inheritDoc}
     * @see \app\modules\manage\controllers\CommonController::behaviors()
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'conform' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'update', 'create', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'update', 'delete'],
                        'roles' => ['owner_admin', 'client_admin'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('mediaUpLoadListException');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['owner_admin', 'client_admin'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('mediaUpLoadCreateException');
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
        $mediaUploadSearch = new MediaUploadSearch();

        $dataProvider = $mediaUploadSearch->search($this->get);

        $role = ArrayHelper::getValue(
            ArrayHelper::getValue($this->get, $mediaUploadSearch->formName()),
            'role'
        );

        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;

        //掲載企業管理者が、運営元管理者の画像を変更削除できないようにするため
        $isOnlyView = $identity->role == Manager::CLIENT_ADMIN
            && $role == Manager::OWNER_ADMIN;

        if (!$isOnlyView) {
            $items = [['type' => 'checkBox']];
        }
        // GridHelperにイメージに関するものが見当たらなかったため、ここで処理する。
        // GridHelperが用意されたら修正する必要がある。
        $items[] = [
            'type' => '',
            'attribute' => 'imageFile',
            'sort' => false,
            'format' => 'html',
            'value' => function ($model) {
                /** @var MediaUpload $model */
                return Html::img($model->srcUrl(), ['height' => '50px']);
            },
            'usePopover' => false,
        ];
        $items[] = ['type' => '', 'attribute' => 'disp_file_name'];
        $items[] = ['type' => '', 'attribute' => 'tag'];
        $items[] = ['type' => '', 'attribute' => 'adminName', 'value' => 'adminMaster.fullName'];
        $items[] = ['type' => '', 'attribute' => 'clientName', 'value' => 'clientMaster.client_name'];
        $items[] = ['type' => '', 'attribute' => 'updated_at', 'format' => 'datetime'];
        $items[] = ['type' => '', 'attribute' => 'file_size', 'format' => 'shortSize'];
        // media用のメディアアップロード用のボタンを追加
        if (!$isOnlyView) {
            $items[] = ['type' => 'operation', 'buttons' => '{pjax-modal}'];
        }

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'mediaUploadSearch' => $mediaUploadSearch,
            'listItems' => $items,
            'isOnlyView' => $isOnlyView,
        ]);
    }

    /**
     * @inheritdocs
     */
    public function create()
    {
        $mediaUpload = new MediaUpload();

        return $this->render('create', [
            'mediaUpload' => $mediaUpload,
        ]);
    }

    /**
     * 画像アップロードに関するデータ登録
     * viewで使用しているWidgetの仕様に合わせるため、
     * createRegisterメソッドとしてではなく別アクションとして実装している
     * @inheritdoc
     */
    public function actionSave()
    {
        $model = new MediaUpload();
        $model->loadFileInfo();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save() || !$model->saveFiles()) {
                throw new Exception();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            // saveエラーの時はvalidationのエラーメッセージを、
            // それ以外は固定のエラーメッセージを表示している
            $output['error'] = implode('</li><li>', $model->errorMessages()) ?: Yii::t('app', '画像の登録に失敗しました');
            return json_encode($output);
        }

        $this->session->setFlash('upload_result', true);
        return json_encode([]);
    }

    /**
     * 画像登録メソッド
     * @param int $id
     * @return yii\web\Response
     * @throws Exception
     */
    public function updateRegister($id)
    {
        $model = $this->findModel($id);
        $model->load($this->post);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                throw new Exception();
            }
            $model->saveFiles();
            $this->session->setFlash('message', Yii::t('app', '画像情報の更新が完了しました'));
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $message = implode('<br>', $model->errorMessages());
            $this->session->setFlash('errorMessage', $message ?: Yii::t('app', '画像情報の更新に失敗しました'));
        }

        return $this->redirect(Url::toRoute([
            'list',
            'MediaUploadSearch' => ArrayHelper::getValue($this->get, 'queryParams.MediaUploadSearch'),
            'sort' => ArrayHelper::getValue($this->get, 'queryParams.sort'),
        ]));
    }

    /**
     * 削除アクション
     */
    public function actionDelete()
    {
        $searchModel = new MediaUploadSearch;
        $models = $searchModel->deleteSearch($this->post);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 画像レコード削除
            $deleteCount = MediaUploadSearch::deleteRecords($models);
            $this->session->setFlash('deleteCount', $deleteCount);
            $transaction->commit();
            MediaUploadSearch::deleteFiles($models);
        } catch (Exception $e) {
            $transaction->rollBack();
            if ($this->session->getFlash('deleteCount')) {
                $this->session->setFlash('errorMessage', Yii::t('app', '画像情報は削除されましたが、画像ファイルの削除に失敗しました'));
            } else {
                $this->session->setFlash('errorMessage', Yii::t('app', '画像情報の削除に失敗しました'));
            }
        }

        // postからqueryパラメータ以外を除去してリダイレクト
        return $this->redirect(['list'] + $this->removeExtraParams($this->post));
    }

    /**
     * 権限による制限も加味してidからモデルを取得する
     * @param integer $id
     * @return MediaUpload the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MediaUpload::find()->where(['id' => $id])->addAuthQuery()->one()) !== null) {
            /** @var $model MediaUpload */
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
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

        return $this->renderAjax('_update', [
            'model' => $model,
        ]);
    }
}
