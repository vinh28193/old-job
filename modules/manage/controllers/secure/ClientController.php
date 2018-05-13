<?php

namespace app\modules\manage\controllers\secure;

use app\common\CorpClientPlanDepDropTrait;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\ClientMasterSearch;
use app\modules\manage\controllers\CommonController;
use app\models\manage\ClientCharge;
use Yii;
use app\common\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use proseeds\helpers\ExportHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * 掲載企業機能コントローラ
 *
 * @author Yukinori Nakamura
 */
class ClientController extends CommonController
{
    use CorpClientPlanDepDropTrait;
    /** @var ClientMaster */
    public $model;

    /**
     * ビヘイビア設定
     * @return array
     */
    public function behaviors()
    {
        $model = $this->model;
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
                'only' => ['list', 'update', 'delete', 'create'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'delete'],
                        'roles' => ['owner_admin', 'corp_admin'],
                        'denyCallback' => function ($rule, $action) {
                            return Yii::$app->user->can('clientListException');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['owner_admin'],
                        'denyCallback' => function ($rule, $action) {
                            return Yii::$app->user->can('clientListException');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['corp_admin'],
                        'matchCallback' => function ($rule, $action) use ($model) {
                            return Yii::$app->user->can('updateClient', ['clientMaster' => $model]);
                        },
                        'denyCallback' => function ($rule, $action) {
                            return Yii::$app->user->can('clientListException');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['owner_admin', 'corp_admin'],
                        'denyCallback' => function ($rule, $action) {
                            return Yii::$app->user->can('clientCreateException');
                        },
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id == 'update') {
            $this->model = $this->findModel(ArrayHelper::getValue($this->get, 'id'));
        }
        return parent::beforeAction($action);
    }

    /**
     * 【画面】掲載企業情報一覧
     * @return string 描画結果
     */
    public function actionList()
    {
        $clientMasterSearch = new ClientMasterSearch();
        $dataProvider = $clientMasterSearch->search($this->get);
        // grid表示アイテム生成
        $listItems = [['type' => 'checkBox']];
        foreach (Yii::$app->functionItemSet->client->listAttributes as $attr) {
            $listItems[] = [
                'type' => '',
                'attribute' => $attr,
                'value' => ClientMasterSearch::getColumnName($attr),
            ];
        }
        $listItems[] = ['type' => '', 'attribute' => 'valid_chk', 'layout' => '{value}', 'format' => 'validChk'];
        $listItems[] = ['type' => 'operation', 'buttons' => '{update}'];

        return $this->render('list', [
            'clientMasterSearch' => $clientMasterSearch,
            'dataProvider' => $dataProvider,
            'listItems' => $listItems,
        ]);
    }

    /**
     * 【画面】掲載企業登録
     * @return string 描画結果
     */
    public function create()
    {
        $clientMaster = new ClientMaster();
        return $this->render('create', [
            'model' => $clientMaster,
        ]);
    }

    /**
     * 【画面】掲載企業編集
     * @param int $id 掲載企業ID
     * @return string 描画結果
     */
    public function update($id)
    {
        $clientMaster = $this->model;
        return $this->render('update', [
            'model' => $clientMaster,
        ]);
    }

    /**
     * 【機能】掲載企業登録
     * @return string 描画結果
     * @throws Exception
     */
    public function createRegister()
    {
        //インスタンス生成
        $clientMaster = new ClientMaster();
        $clientMaster->load($this->post);
        //トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //掲載企業保存
            if (!$clientMaster->save()) {
                throw new Exception('エラー');
            }
            //申し込みプラン保存
            foreach ($this->post['ClientCharge'] as $clientChargePost) {
                //チェックの入っていない申し込みプランは処理しない。
                if (!$clientChargePost['client_charge_plan_id']) {
                    continue;
                }
                $clientCharge = new ClientCharge();
                $clientCharge->load($clientChargePost, '');
                $clientMaster->link('clientCharges', $clientCharge);
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $this->redirect(Url::toRoute('complete'));
    }

    /**
     * 【機能】掲載企業編集
     * @param int $id 掲載企業ID
     * @return string 描画結果
     * @throws Exception
     */
    public function updateRegister($id)
    {
        //インスタンス生成
        $clientMaster = $this->model;
        $clientMaster->load($this->post);
        //トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //掲載企業保存
            if (!$clientMaster->save()) {
                throw new Exception('エラー');
            }
            $clientMaster->unlinkAll('clientCharges', true);
            //申し込みプラン保存
            foreach ($this->post['ClientCharge'] as $clientChargePost) {
                //チェックの入っていない申し込みプランは処理しない。
                if (!$clientChargePost['client_charge_plan_id']) {
                    continue;
                }
                $clientCharge = new ClientCharge();
                $clientCharge->load($clientChargePost, '');
                $clientMaster->link('clientCharges', $clientCharge);
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $this->redirect(Url::toRoute('complete') . '?isUpdate=true');
    }

    /**
     * 【画面】登録完了
     * @return string 描画結果
     */
    public function actionComplete()
    {
        $isUpdate = isset($this->get["isUpdate"]) ? $this->get["isUpdate"] : false;
        return $this->render('complete', [
            'isUpdate' => $isUpdate,
        ]);
    }

    /**
     * モデルの取得
     * @param int $id ID
     * @return ClientMaster モデル
     * @throws NotFoundHttpException モデルが見つからなかったとき
     */
    protected function findModel($id)
    {
        $model = ClientMaster::find()->with('clientCharges')->where(['id' => $id])->one();
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * フォーム画面検証用のajaxバリデーションアクション
     * @param null $id
     * @return array
     */
    public function actionAjaxValidation($id = null)
    {
        if (Yii::$app->request->isAjax && isset($this->post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($id) {
                $clientMaster = ClientMaster::findOne($id);
            } else {
                $clientMaster = new ClientMaster();
            }
            $clientMaster->load($this->post);

            $judge = ArrayHelper::getColumn($this->post['ClientCharge'], 'client_charge_plan_id');
            if (!array_filter($judge)) {
                $clientMaster->clientChargePlan = false;
                foreach ($this->post['ClientCharge'] as $clientChargePlanId => $clientChargePost) {
                    $clientCharge = new ClientCharge();
                    $clientCharge->noSelect = 1;
                    $models[$clientChargePlanId] = $clientCharge;
                }
                return array_merge(ActiveForm::validateMultiple($models), ActiveForm::validate($clientMaster));
            }
            foreach ($this->post['ClientCharge'] as $clientChargePlanId => $clientChargePost) {
                //チェックの入っていない申し込みプランは処理しない。
                if (!$clientChargePost['client_charge_plan_id']) {
                    continue;
                }
                $clientCharge = new ClientCharge();
                $clientCharge->load($clientChargePost, '');
                $models[$clientChargePlanId] = $clientCharge;
            }
            return array_merge(ActiveForm::validateMultiple($models), ActiveForm::validate($clientMaster));
        }
        return false;
    }

    /**
     * csvで出力できるように整形する
     * @param $columns array
     * @return array
     */
    public function parseColumnNameForCsv($columns)
    {
        $columnList = [];
        foreach ($columns as $item) {
            $columnList[] = ClientMasterSearch::getColumnName($item);
        }
        return $columnList;
    }

    /**
     * CSVダウンロードアクション
     */
    public function actionCsvDownload()
    {
        $searchModel = new ClientMasterSearch();
        $getClientColumnList = ArrayHelper::getColumn(Yii::$app->functionItemSet->client->Items, 'column_name');
        $csvRelationColumn = $this->parseColumnNameForCsv($getClientColumnList);

        $dataProvider = $searchModel->csvSearch($this->get);

        ExportHelper::outputAsCSV(
            $dataProvider,
            'ClientMasterList_' . date('YmdHi') . '.csv',
            $csvRelationColumn + ['valid_chk:validChk']
        );
    }

    /**
     * 削除アクション
     */
    public function actionDelete()
    {
        $this->deleteByGridCheckBox(new ClientMasterSearch());
        // postからqueryパラメータ以外を除去してリダイレクト
        $this->redirect(['list'] + $this->removeExtraParams($this->post));
    }

    /**
     * 申し込みプランのCSVダウンロードアクション
     */
    public function actionPlanCsvDownload()
    {
        $searchModel = new ClientMasterSearch();

        $aaa = isset($this->get['ClientMasterSearch']) ? $this->get['ClientMasterSearch'] : [];

        $aaa += isset($this->get['gridData']) ? json_decode($this->get['gridData'], true) : [];
        unset($this->get['gridData']);

        $dataProvider = $searchModel->csvPlanSearch(['ClientMasterSearch' => $aaa]);

        ExportHelper::outputAsCSV(
            $dataProvider,
            'PlanList_' . date('YmdHi') . '.csv',
            [
                'client_no',
                'corpMaster.corp_name',
                'client_name',
                'clientCharge.clientChargePlan.client_charge_type',
                'clientCharge.clientChargePlan.plan_name',
                'clientCharge.disp_end_date',

            ]
        );
    }

    /**
     * client_charge_typeからプランのドロップダウン用json配列を取得するAjaxアクション
     * todo 他のとまとめて整理
     * @return array
     */
    public function actionPlanListSearch()
    {
        if (Yii::$app->request->isAjax && isset($this->post['depdrop_parents'])) {
            $planList = ClientChargePlan::getDropDownArray(
                false,
                null, // clientMasterIdでの検索はしない
                ArrayHelper::getValue($this->post, 'depdrop_parents.0') ?: null, // 値が無い場合はnullを入れて全件取得
                null // 有効なものも無効なものも取得
            );
            $out = [];
            foreach ($planList as $id => $name) {
                $out[] = ['id' => $id, 'name' => $name];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['output' => $out, 'selected' => ''];
        }
        return false;
    }
}
