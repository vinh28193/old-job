<?php

namespace app\modules\manage\controllers\secure;

use app\models\MailSend;
use app\models\manage\ApplicationMaster;
use app\models\manage\ApplicationMasterSearch;
use app\modules\manage\controllers\CommonController;
use yii;
use yii\base\ErrorException;
use app\common\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use proseeds\helpers\ExportHelper;
use app\models\manage\ApplicationResponseLog;
use app\common\mail\MailSender;
use app\models\manage\SiteMaster;
use app\common\CorpClientPlanDepDropTrait;

/**
 * ApplicationController implements the CRUD actions for ApplicationMaster model.
 */
class ApplicationController extends CommonController
{
    /*
     * DepDrop用にAjaxアクション
     */
    use CorpClientPlanDepDropTrait;

    /** @var ApplicationMaster */
    public $model;

    /**
     * {@inheritDoc}
     * @see \app\modules\manage\controllers\CommonController::behaviors()
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
                'only' => ['list', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list'],
                        'roles' => ['@'],
                        'denyCallback' => function ($rule, $action) {
                            return Yii::$app->user->can('applicationListException');
                        },
                    ],
                    // 運営元時のsql発行回数の節約のため分けているが一緒にするか検討の余地あり
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['owner_admin'],
                        'denyCallback' => function ($rule, $action) {
                            return Yii::$app->user->can('applicationListException');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['corp_admin', 'client_admin'],
                        'matchCallback' => function ($rule, $action) use ($model) {
                            return Yii::$app->user->can('updateApplication', ['applicationMaster' => $model]);
                        },
                        'denyCallback' => function ($rule, $action) {
                            return Yii::$app->user->can('applicationListException');
                        },
                    ],
                ]
            ]
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
     * Finds the ApplicationMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ApplicationMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ApplicationMaster::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 一覧画面
     * @return mixed
     */
    public function actionList()
    {
        $applicationMasterSearch = new ApplicationMasterSearch();

        $dataProvider = $applicationMasterSearch->search($this->get);

        $items = [['type' => 'checkBox']];

        //仕事IDの列を決め打ちで表示
        $items[] = ['type' => 'default', 'attribute' => 'jobNo', 'value' => 'jobModel.job_no'];

        // リスト表示項目生成
        // todo listItemsを生成する処理は共通化したい
        foreach (Yii::$app->functionItemSet->application->listAttributes as $attribute) {
            if ($attribute == 'carrier_type') {
                $items[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'format' => 'carrierType',
                ];
            } elseif ($attribute == 'created_at') {
                $items[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'format' => 'dateTime',
                ];
            } elseif ($attribute == 'sex') {
                $items[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'format' => 'sex',
                ];
            } elseif ($attribute == 'birth_date') {
                $items[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'format' => 'date',
                ];
            } else {
                $items[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'value' => $this->getValueByAttribute($attribute),
                ];
            }
        };
        // 変更ボタン（削除機能はないので変更のみ）
        $items[] = ['type' => 'operation', 'buttons' => '{update}'];

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'applicationMasterSearch' => $applicationMasterSearch,
            'listItems' => $items,
        ]);
    }

    /**
     * attributeによるリレーションのカラムを返す
     * todo モデルに書くべきか？
     * @param string $attribute
     * @return string
     */
    protected function getValueByAttribute($attribute)
    {
        switch ($attribute) {
            case 'corpLabel':
                return 'jobModel.clientMaster.corpMaster.corp_name';
                break;
            case 'clientLabel':
                return 'jobModel.clientMaster.client_name';
                break;
            case 'application_status_id':
                return 'applicationStatus.application_status';
                break;
            case 'occupation_id':
                return 'occupation.occupation_name';
                break;
            case 'pref_id':
                return 'pref.pref_name';
            default:
                return $attribute;
        }
    }

    /**
     * @inheritdoc
     */
    public function update($id)
    {
        // function item set list取得
        $model = $this->findModel($id);
        $model->load($this->post);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return \Exception|string|ErrorException
     * @throws NotFoundHttpException
     */
    public function updateRegister($id)
    {
        $model = $this->findModel($id);

        $applicationResponseLog = new ApplicationResponseLog();

        //トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->load($this->post) || !$model->validate()) {
                throw new ErrorException();
            }
            $model->save();

            if (!$applicationResponseLog->load([
                    'application_id' => $model->id,
                    'application_status_id' => $model->application_status_id,
                ], '') || !$applicationResponseLog->validate()
            )
                throw new ErrorException();
            $applicationResponseLog->save();

            $transaction->commit();
        } catch (ErrorException $e) {
            $transaction->rollBack();
            return $e;   //　TODO:エラー処理は後ほど決める
        }

        return $this->redirect(Url::to(['update', 'id' => $id]));
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionMail($id)
    {
        $model = $this->findModel($id);
        $applicationResponseLog = new ApplicationResponseLog();

        $mailSender = new MailSender();
        $mailSend = $mailSender->mail([
            'mail_title' => ArrayHelper::getValue($this->post['MailSend'], 'mail_title'),
            'mail_body' => ArrayHelper::getValue($this->post['MailSend'], 'mail_body'),
            'mail_type_id' => MailSend::TYPE_INDIVIDUAL_APPLICATION,
        ])->users([
            ['pc_mail_address' => $model->mail_address]
        ])->fromName([
            'from_mail_address' => ArrayHelper::getValue($this->post['MailSend'], 'from_mail_address'),
            'from_mail_name' => ArrayHelper::getValue(SiteMaster::find()->one(), 'site_name'), // 送信者はサイト名になる
        ])->preparedInstantSend();

        $applicationResponseLog->load([
            'application_id' => $model->id,
            'mail_send_id' => $mailSend->id,
        ], '');
        $applicationResponseLog->validate();
        $applicationResponseLog->save();

        return $this->redirect(Url::to(['update', 'id' => $id]));
    }

    /**
     * 変更完了
     * @return string
     */
    public function actionComplete()
    {
        $isUpdate = isset($this->get["isUpdate"]) ? $isUpdate = $this->get["isUpdate"] : false;

        return $this->render("complete", [
            "isUpdate" => $isUpdate,
        ]);
    }

    /**
     * todo リファクタリング
     * CSVダウンロードアクション
     */
    public function actionCsvDownload()
    {
        $searchModel = new ApplicationMasterSearch();
        // 応募者一覧画面に表示される仕事IDはapplication_column_setされてないため直接マージしている
        $getApplicationColumnList = array_merge(['jobMaster.job_no'], ArrayHelper::getColumn(Yii::$app->functionItemSet->application->Items, 'column_name'));
        $csvRelationColumn = $this->parseColumnNameForCsv($getApplicationColumnList);

        $dataProvider = $searchModel->csvSearch($this->get);

        ExportHelper::outputAsCSV(
            $dataProvider,
            'ApplicationMasterList_' . date('YmdHi') . '.csv',
            $csvRelationColumn
        );
    }

    /**
     * todo リファクタリング
     * csvで出力できるように整形する
     * @param $columns array
     * @return array
     */
    public function parseColumnNameForCsv($columns)
    {
        $columnList = [];
        foreach ($columns as $item) {
            if ($item == 'carrier_type') {
                $columnList[] = $item . ':carrierType';
            } elseif ($item == 'created_at') {
                $columnList[] = $item . ':dateTime';
            } elseif ($item == 'birth_date') {
                $columnList[] = $item . ':date';
            } elseif ($item == 'sex') {
                $columnList[] = $item . ':sex';
            } else {
                $columnList[] = $this->getValueByAttribute($item);
            }
        }
        return $columnList;
    }

    /**
     * 削除アクション
     */
    public function actionDelete()
    {
        $searchModel = new ApplicationMasterSearch();
        // 削除するidを取得してそれを元に削除して削除件数をセット
        $deleteModels = $searchModel->deleteSearch($this->post);
        $deleteCount = $searchModel->backupAndDelete($deleteModels);
        $this->session->setFlash('deleteCount', $deleteCount);
        // postからqueryパラメータ以外を除去してリダイレクト
        $this->redirect(['list'] + $this->removeExtraParams($this->post));
    }
}
