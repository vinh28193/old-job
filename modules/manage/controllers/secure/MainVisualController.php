<?php

namespace app\modules\manage\controllers\secure;

use app\common\AccessControl;
use app\models\manage\MainVisual;
use app\models\manage\searchkey\Area;
use app\modules\manage\controllers\CommonController;
use app\modules\manage\models\requests\MainVisualForm;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class MainVisualController
 *
 * @package app\modules\manage\controllers\secure
 */
class MainVisualController extends CommonController
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
                    'save' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['form'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['form'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * メインビジュアルフォーム
     * @param $areaId
     * @param null $id
     * @return string
     */
    public function actionForm($areaId = null, $id = null)
    {
        if (Yii::$app->request->isPost) {
            return $this->savePostDataForAreaId($areaId, $id);
        }
        return $this->showForm($areaId);
    }

    /**
     * メインビジュアルフォーム：表示
     * @param $areaId
     * @return string
     */
    protected function showForm($areaId)
    {
        return $this->render('form', [
            'forms' => $this->createAreaForms($areaId),
        ]);
    }

    /**
     * メインビジュアルフォーム：更新
     *
     * @param $areaId
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    protected function savePostDataForAreaId($areaId, $id)
    {
        $forms = $this->createAreaForms($areaId);
        // 見つからないときは404
        if (!array_key_exists($areaId, $forms)) {
            throw new NotFoundHttpException;
        }

        /** @var Area $areaOne */
        $areaOne = $forms[$areaId]->area;

        $model = new MainVisualForm(
            $id ? $this->findModel($id) : new MainVisual(),
            $areaOne,
            true
        );

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // 保存成功
            Yii::$app->session->setFlash('success', Yii::t('app', 'メインビジュアル情報を保存しました。'));
            return $this->redirect(['form', 'areaId' => $model->mainVisual->area_id]);
        }

        // 保存できなかった
        $forms[$areaId] = $model;
        return $this->render('form', [
            'forms' => $forms,
        ]);
    }

    /**
     * 表示用のMainVisualFormのリストを作って返す
     * @param null|Int $activeAreaId
     * @return MainVisualForm[]
     */
    protected function createAreaForms($activeAreaId = null)
    {
        /** @var Area[] $areas */
        $areas = Area::find()->where(['valid_chk' => MainVisualForm::STATUS_PUBLIC])
            ->orderBy(['sort' => SORT_ASC])->all();

        /** @var MainVisualForm[] $forms */
        $forms = [];
        // 全国設定
        $forms[Area::NATIONWIDE_ID] = new MainVisualForm($this->findModelNationwideArea(), null, !$activeAreaId);
        foreach ($areas as $i => $area) {
            $isActive = ($area->id == $activeAreaId);
            $forms[$area->id] = new MainVisualForm($this->fileModelByAreaId($area->id), $area, $isActive);
        }
        return $forms;
    }

    /**
     * @param $id
     * @return MainVisual|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = MainVisual::find()->andWhere(['id' => $id])->one();

        if ($model) {
            return $model;
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * メインビジュアルフォームを取得する
     *
     * @param int $areaId
     * @return MainVisual|array|null|\yii\db\ActiveRecord
     */
    protected function fileModelByAreaId(int $areaId)
    {
        if ($areaId === Area::NATIONWIDE_ID) {
            return $this->findModelNationwideArea();
        }

        $model = MainVisual::find()->where(['area_id' => $areaId])->one();

        if ($model) {
            return $model;
        } else {
            return new MainVisual();
        }
    }

    /**
     * 全国向けフォームを取得する
     *
     * @return MainVisual|array|null|\yii\db\ActiveRecord
     */
    protected function findModelNationwideArea()
    {
        $model = MainVisual::find()->where(['area_id' => null])->one();

        if ($model) {
            return $model;
        } else {
            return new MainVisual();
        }
    }
}
