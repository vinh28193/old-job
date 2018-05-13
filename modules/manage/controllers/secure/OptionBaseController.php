<?php

namespace app\modules\manage\controllers\secure;

use app\common\AccessControl;
use app\models\manage\BaseColumnSet;
use yii;
use app\modules\manage\controllers\CommonController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * 管理画面->項目管理ｰ>代理店項目設定->一覧画面　コントローラー
 */
class OptionBaseController extends CommonController
{
    /** _option-formで使うHtmlのID */
    const UPDATE_FORM_ID = 'updateForm';
    const SUBSET_UL_ID = 'subset_list';
    const OPTION_SEARCH_MODELS = [
        'job' => 'app\models\manage\JobColumnSetSearch',
        'corp' => 'app\models\manage\CorpColumnSetSearch',
        'admin' => 'app\models\manage\AdminColumnSetSearch',
        'application' => 'app\models\manage\ApplicationColumnSetSearch',
        'client' => 'app\models\manage\ClientColumnSetSearch',
//        'member' => 'app\models\manage\MemberColumnSetSearch',
        'inquiry' => 'app\models\manage\InquiryColumnSetSearch'
    ];

    /**
     * 呼び出すSearchModelのクラス名を保存
     * 項目管理で使用する各コントローラーで必ずinit処理を行うこと
     * @var string
     */
    public $functionItemSetMenu;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'update' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'update'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 一覧画面
     * @return mixed
     */
    public function actionList()
    {
        $menu = $this->functionItemSetMenu;
        $urls = explode('\\', Yii::$app->functionItemSet->$menu->columnSetModel);
        $modelName = end($urls);

        $nameArray = [
            'menu' => $menu,
            'columnSubsetModel' => str_replace ('Set', 'Subset', $modelName),
        ];

        $searchModel = Yii::createObject(self::OPTION_SEARCH_MODELS[$this->functionItemSetMenu], []);
        $dataProvider = $searchModel->search($this->get);


        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'nameArray' => $nameArray,
        ]);
    }

    /**
     * 更新画面
     * @return mixed
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws yii\db\Exception
     */
    public function actionUpdate()
    {
        $id = ArrayHelper::getValue($this->get, 'id');
        $model = $this->findModel($id);
        $model->load($this->post);
        $model->setScenarioByAttributes();

        $transaction = Yii::$app->db->beginTransaction();

        try {
            //項目管理の本体の内容（[function_item_set]）を保存。
            if (!$model->save()) {
                throw new Exception('エラー');
            }
            if(!$model->saveRelationModel($this->post)){
                throw new Exception('エラー');
            }
            $this->session->setFlash('updateComment', Html::tag('p', Yii::t('app', '更新が完了しました。'), ['class' => 'alert alert-warning']));
            $transaction->commit();
            return $this->redirect(Url::toRoute('list'));
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
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
        $model->setScenarioByAttributes();

        return $this->renderAjax('/secure/common/_option-form', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id int 主キーのid
     * @return BaseColumnSet
     * @throws NotFoundHttpException
     * @throws yii\base\InvalidConfigException
     */
    protected function findModel($id)
    {
        $menu = $this->functionItemSetMenu;
        /* @property \yii\db\ActiveRecord $model */
        $model = Yii::createObject(Yii::$app->functionItemSet->$menu->columnSetModel, []);
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
