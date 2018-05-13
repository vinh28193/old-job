<?php

namespace app\modules\manage\controllers\secure;

use app\common\AccessControl;
use app\controllers\TopController;
use app\models\forms\JobSearchForm;
use app\models\manage\searchkey\Area;
use app\models\manage\SiteHtml;
use app\models\manage\Widget;
use app\models\manage\WidgetLayout;
use yii\web\NotFoundHttpException;
use app\modules\manage\controllers\CommonController;
use Yii;
use yii\bootstrap\Html;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * WidgetDataController implements the CRUD actions for WidgetData model.
 */
class WidgetController extends CommonController
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
                'only' => ['index', 'update', 'pjax'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'pjax'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        // widgetをlayout_noでグルーピング
        $arrayWidgets = ArrayHelper::index(
            Widget::find()->with('widgetLayout')->orderBy('sort')->all(),
            null,
            'widgetLayout.widget_layout_no'
        );
        return $this->render('index', [
            'arrayWidgets' => $arrayWidgets,
        ]);
    }

    /**
     * 更新（ajaxを使って非同期で更新する）
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['message' => ''];
        $id = $this->post['id'];
        $model = $this->findModel($id);
        $model->load($this->post);
        $model->setStyleSp();

        if ($model->validate() && $model->save()) {
            $out['message'] = Yii::t('app', '更新が完了しました。');
            $out['widgetName'] = Html::encode($model->widget_name);
        } else {
            $out['message'] = Yii::t('app', '更新に失敗しました。');
        }
        return $out;
    }

    /**
     * 変更モーダルpjaxアクション
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPjaxModal($id)
    {
        if (Yii::$app->request->isAjax) {
            $model = $this->findModel($id);
            return $this->renderAjax('_item-update', [
                'model' => $model,
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 並び順を更新する
     * @throws \Exception
     */
    public function actionSortUpdate()
    {
        /** @var Widget[] $widgets */
        /** @var WidgetLayout[] $widgetLayouts */
        $widgets = ArrayHelper::index(Widget::find()->all(), 'id');
        $widgetLayouts = ArrayHelper::index(WidgetLayout::find()->select(['id', 'widget_layout_no'])->all(), 'widget_layout_no');
        foreach ($this->post['widgetIds'] as $widgetLayoutNo => $str) {
            if ($str === '') {
                continue;
            }
            $widgetLayoutId = $widgetLayouts[$widgetLayoutNo]->id ?? null;
            $widgetIds = explode(',', $str);
            foreach ($widgetIds as $key => $id) {
                $sort = $key + 1;
                // layout_idとsortのどちらかが変更されていたらupdateする
                if ($widgets[$id]->widget_layout_id != $widgetLayoutId || $widgets[$id]->sort != $sort) {
                    $widgets[$id]->widget_layout_id = $widgetLayoutId;
                    $widgets[$id]->sort = $sort;
                    if (!$widgets[$id]->validate() || !$widgets[$id]->save()) {
                        throw new \Exception();
                    }
                }
            }
        }
        $this->session->setFlash('operationComment', Html::tag('p', Yii::t('app', 'ウィジェットの並び順の更新が完了しました。'), ['class' => 'alert alert-warning']));
        return $this->redirect('index');
    }

    /**
     * プレビュー画面を表示する
     * todo TOPプレビュー画面が複数あるので、それ用のtraitか何かを作ってもう少し共通化した方が良いかも
     * @param $isMobile
     * @return string
     */
    public function actionPreview($isMobile)
    {
        /** @var \app\components\Area $areaComp */
        $areaComp = Yii::$app->area;
        $area = $areaComp->firstArea;
        /** @var Widget[] $widgets */
        /** @var WidgetLayout[] $widgetLayouts */
        $widgets = ArrayHelper::index(
            Widget::find()
                ->innerJoinWithData($area->id)
                ->all(),
            'id'
        );
        $widgetLayouts = ArrayHelper::index(
            WidgetLayout::find()
                ->select(['id', 'widget_layout_no'])
                ->where(['area_flg' => WidgetLayout::AREA_COMMON])
                ->all(),
            'widget_layout_no'
        );

        // 表示用widgetLayout配列の生成
        foreach ($this->post['widgetIds'] as $widgetLayoutNo => $str) {
            if (!isset($widgetLayouts[$widgetLayoutNo])) {
                continue;
            }

            if ($str === '') {
                $widgetLayouts[$widgetLayoutNo]->populateRelation('widget', null);
                continue;
            }

            $widgetIds = explode(',', $str);
            $layoutWidgets = null;
            foreach ($widgetIds as $key => $id) {
                if (!isset($widgets[$id])) {
                    continue;
                }
                $widgets[$id]->sort = $key + 1;
                $layoutWidgets[] = $widgets[$id];
            }
            ArrayHelper::multisort($layoutWidgets, 'sort');
            $widgetLayouts[$widgetLayoutNo]->populateRelation('widget', $layoutWidgets);
        }

        // その他表示に必要な要素の設定
        $this->view->params['siteHtml'] = SiteHtml::find()->one();

        $jobSearchForm = new JobSearchForm();
        $jobSearchForm->initTopScenario($area);
        if (!$isMobile) {
            $allCount = $jobSearchForm->count();
            $this->layout = '@app/views/layouts/main';
        } else {
            $this->layout = '@app/views/layouts/sp/main';
            $allCount = null;
        }

        list($hotJob, $dataProvider) = TopController::hotJobResult($area->id);

        return $this->render('@app/views/top/index', [
            'searchForm' => $jobSearchForm,
            'widgetLayouts' => $widgetLayouts,
            'areaName' => $area->area_name,
            'areaId' => $area->id,
            'allCount' => $allCount,
            'isMobile' => $isMobile,
            'site' => Yii::$app->site,
            'hotJob'  => $hotJob,
            'dataProvider'  => $dataProvider,
        ]);
    }

    /**
     * idからモデルを取得する
     * @param integer $id
     * @return Widget
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Widget::findOne($id);
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
