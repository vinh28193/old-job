<?php

namespace app\modules\manage\controllers\secure;

use app\common\Helper\JmUtils;
use app\modules\manage\controllers\CommonController;
use yii;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Pref;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\NotFoundHttpException;

/**
 * AreaController implements the CRUD actions for AreaCd model.
 */
class AreaController extends CommonController
{
    /**
     * @return string
     */
    public function actionList()
    {
        $areas = Area::find()->with('pref')->orderBy('sort')->all();
        return $this->render('list', [
            'areas' => $areas,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        throw new NotFoundHttpException();
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function actionUpdate()
    {
        $id = ArrayHelper::getValue($this->get, 'id');
        $model = Area::findOne(['id' => $id]);
        if($model === null){
            throw new NotFoundHttpException();
        }
        $model->load($this->post);
        if (!$model->validate() || !$model->save()) {
            throw new \Exception();
        };

        $this->session->setFlash('operationComment', Html::tag('p', Yii::t('app', 'エリア情報の更新が完了しました。'), ['class' => 'alert alert-warning']));

        return $this->redirect('list');
    }

    /**
     * @param $areaId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPjaxModal($areaId)
    {
        if (Yii::$app->request->isAjax) {
            $model = Area::findOne(['id' => $areaId]);
            return $this->renderAjax('/secure/common/_searchkey-form', [
                'model' => $model,
                'isNew' => false,
                'flg' => 'first',
                'attribute' => ['page' => 'area'],
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAjaxValidation($id)
    {
        if (Yii::$app->request->isAjax) {
            if ($id) {
                $model = Area::findOne($id);
            } else {
                $model = new Area();
            }
            $model->load($this->post);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 並び順を更新する
     * @return Response
     * @throws \Exception
     */
    public function actionSortUpdate()
    {
        /** @var Pref[] $prefs */
        $prefs = ArrayHelper::index(Pref::find()->all(), 'id');
        foreach ($this->post['prefIds'] as $areaId => $str) {
            if ($str === '') {
                continue;
            }
            $prefIds = explode(',', $str);
            foreach ($prefIds as $key => $prefId) {
                $sort = $key + 1;
                if ($prefs[$prefId]->area_id != $areaId || $prefs[$prefId]->sort != $sort) {
                    $prefs[$prefId]->area_id = $areaId ?: null;
                    $prefs[$prefId]->sort = $sort;
                    if (!$prefs[$prefId]->validate() || !$prefs[$prefId]->save()) {
                        throw new \Exception();
                    }
                }
            }
        }

        /** @var Area[] $areas */
        $areas = ArrayHelper::index(Area::find()->all(), 'id');
        $areaIds = [];
        foreach (explode(',', $this->post['Areas']) as $v) {
            $id = str_replace('area', '', $v, $count);
            if ($count !== 0) {
                $areaIds[] = $id;
            }
        }
        foreach ($areaIds as $key => $areaId) {
            $sort = $key + 1;
            if ($areas[$areaId]->sort != $sort) {
                $areas[$areaId]->sort = $sort;
                if (!$areas[$areaId]->validate() || !$areas[$areaId]->save()) {
                    throw new \Exception();
                }
            }
        }

        $this->session->setFlash('operationComment', Html::tag('p', Yii::t('app', 'エリアの並び順、都道府県の割当と並び順の更新が完了しました。'), ['class' => 'alert alert-warning']));

        return $this->redirect('list');
    }
}