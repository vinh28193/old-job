<?php

namespace app\modules\manage\controllers\secure\settings;

use app\common\AccessControl;
use app\models\manage\ClientDisp;
use app\models\manage\DispType;
use app\models\manage\MainDisplay;
use Yii;
use yii\bootstrap\Html;
use app\models\manage\ListDisp;
use app\modules\manage\controllers\CommonController;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * DisplayController implements the CRUD actions for JobColumnSet model.
 */
class DisplayController extends CommonController
{
    const PJAX_ID = 'displayPajax';

    /**
     * ビヘイビア設定
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'list-pjax', 'update-list'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'list-pjax', 'update-list'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        /** @var DispType[] $dispTypes */
        $dispTypes = DispType::find()->where(['valid_chk' => DispType::VALID])->all();

        if (!$dispTypes) {
            throw new NotFoundHttpException();
        }

        if (Yii::$app->session->hasFlash('dispTypeId')) {
            $dispTypeId = Yii::$app->session->getFlash('dispTypeId');
            $bothListItems = ListDisp::bothItems($dispTypeId);
            $bothClientItems = ClientDisp::bothItems($dispTypeId);
            $mainDisplayModel = new MainDisplay(['dispTypeId' => $dispTypeId]);
        } else {
            $bothListItems = ListDisp::bothItems($dispTypes[0]->id);
            $bothClientItems = ClientDisp::bothItems($dispTypes[0]->id);
            $mainDisplayModel = new MainDisplay(['dispTypeId' => $dispTypes[0]->id]);
        }
        return $this->render('index', [
            'dispTypes' => $dispTypes,
            'bothListItems' => $bothListItems,
            'bothClientItems' => $bothClientItems,
            'mainDisplayModel' => $mainDisplayModel,
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPjaxForm()
    {
        if (Yii::$app->request->isAjax) {
            if (isset($this->post['dispTypeId'])) {
                $dispTypeId = $this->post['dispTypeId'];
            } else {
                throw new NotFoundHttpException();
            }

            $bothListItems = ListDisp::bothItems($dispTypeId);
            $bothClientItems = ClientDisp::bothItems($dispTypeId);
            $mainDisplayModel = new MainDisplay(['dispTypeId' => $dispTypeId]);

            return $this->renderAjax('pjax-form', [
                'dispTypeId' => $dispTypeId,
                'bothListItems' => $bothListItems,
                'bothClientItems' => $bothClientItems,
                'mainDisplayModel' => $mainDisplayModel,
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function actionUpdate()
    {
        $dispTypeId = $this->post['dispTypeId'];
        $dispType = DispType::findOne($dispTypeId);
        $this->session->setFlash('dispTypeId', $dispTypeId);
        $mainDisplayModel = new MainDisplay(['dispTypeId' => $dispTypeId]);
        //トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (isset($this->post['ListItems'])) {
                $this->updateSort(ListDisp::className(), $this->post['ListItems'], $dispTypeId);
            }

            if (isset($this->post['ClientItems'])) {
                $this->updateSort(ClientDisp::className(), $this->post['ClientItems'], $dispTypeId);
            }
            if (isset($this->post['MainDisplay'])) {
                $mainDisplayModel->save($this->post['MainDisplay']);
            }

            $this->session->setFlash('operationComment', Html::tag('p', Yii::t('app', $dispType->disp_type_name . 'の更新が完了しました。'), ['class' => 'alert alert-warning']));
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->session->setFlash('operationComment', Html::tag('p', Yii::t('app', '更新に失敗しました。'), ['class' => 'alert alert-danger']));
        }

        return $this->redirect('index');
    }

    /**
     * @param $modelClass string
     * @param $postData array
     * @param $dispTypeId integer
     * @throws Exception
     */
    private function updateSort($modelClass, $postData, $dispTypeId)
    {
        /** @var ActiveRecord $modelClass */
        $modelClass::deleteAll(['disp_type_id' => $dispTypeId]);
        if (!$postData) {
            return;
        }
        $columnNameList = explode(',', $postData);
        foreach ($columnNameList as $index => $columnName) {
            $model = Yii::createObject($modelClass);
            $model->tenant_id = Yii::$app->tenant->id;
            $model->column_name = $columnName;
            $sortNo = $index + 1;
            $model->sort_no = $sortNo;

            $model->disp_type_id = $dispTypeId;
            if (!$model->validate() || !$model->save()) {
                throw new Exception('エラー');
            }
        }
    }
}
