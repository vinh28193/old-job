<?php

namespace app\modules\manage\controllers\secure;

use app\models\manage\JobPic;
use app\models\manage\ListDisp;
use app\models\manage\searchkey\JobDist;
use app\modules\manage\controllers\CommonController;
use app\models\manage\JobMaster;
use app\models\manage\JobMasterSearch;
use app\models\manage\ClientChargePlan;
use app\models\manage\searchkey\Station;
use app\models\manage\MainDisp;
use app\modules\manage\models\Manager;
use Yii;
use app\common\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;
use app\common\CorpClientPlanDepDropTrait;
use app\common\Helper\Html;
use proseeds\helpers\ExportHelper;
use proseeds\widgets\GridSubmitButton;
use proseeds\widgets\PopoverWidget;
use app\models\manage\JobReviewStatus;

/**
 * 求人原稿コントローラ
 *
 * @author Yukinori Nakamura
 */
class JobController extends CommonController
{
    /**
     * DepDrop用にAjaxアクション
     */
    use CorpClientPlanDepDropTrait;

    /** @var JobMaster */
    public $model;

    /**
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
                    'change-display' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'update', 'delete', 'create', 'copy'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'delete'],
                        'roles' => ['@'],
                        'denyCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('jobListException');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'copy', 'change-display'],
                        'roles' => ['corp_admin', 'client_admin'],
                        'matchCallback' => function ($rule, $action) use ($model) {
                            return Yii::$app->user->can('updateJob', ['jobMaster' => $model]);
                        },
                        'denyCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('jobListException');
                        },
                    ],
                    // 運営元時のsql発行回数の節約のため分けているが一緒にするか検討の余地あり
                    [
                        'allow' => true,
                        'actions' => ['update', 'copy'],
                        'roles' => ['owner_admin'],
                        'denyCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('jobListException');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('jobCreateException');
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
        } elseif ($action->id == 'copy') {
            $this->model = $this->findModel(ArrayHelper::getValue($this->get, 'id'));
        }
        return parent::beforeAction($action);
    }

    /**
     * 【画面】求人原稿情報一覧
     * @return string 描画結果
     */
    public function actionList()
    {
        //--------------------------------
        // 表示項目取得
        //--------------------------------
        $model = new JobMasterSearch();
        $dataProvider = $model->search($this->get);

        //--------------------------------
        // グリッド用表示アイテム整形
        //--------------------------------
        $listItems = [['type' => 'checkBox']];
        // todo 整理・リファクタ
        foreach ((array)Yii::$app->functionItemSet->job->listAttributes as $attribute) {
            if ($attribute == 'disp_start_date' || $attribute == 'disp_end_date') {
                $listItems[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'value' => JobMasterSearch::getColumnName($attribute),
                    'format' => 'date',
                ];
            } else {
                $listItems[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'value' => JobMasterSearch::getColumnName($attribute),
                ];
            }
        }

        // 審査機能がONのときに表示
        if (Yii::$app->tenant->tenant->review_use) {
            // 審査ステータス
            $listItems[] = [
                'type' => '',
                'attribute' => 'job_review_status_id',
                'layout' => '{value}',
                'format' => 'raw',
                'usePopover' => true,
                'value' => function ($model) {
                    return $this->reviewStatusDisplay($model);
                },
            ];
        }

        // 状態
        // 切替ボタンとしても機能させるためにこの実装にしている。
        $hint = Yii::$app->tenant->tenant->review_use ? Yii::t('app', '既に審査完了したものは「公開」「非公開」の変更は審査不要です。') : Yii::t('app', '求人原稿の公開／非公開の切り替えが出来ます。');
        $listItems[] = [
            'type' => '',
            'attribute' => 'valid_chk',
            'layout' => '{value}',
            'format' => 'raw',
            'headerClass' => 'ss-column',
            'encodeLabel' => false,
            'label' => $model->attributeLabels()['valid_chk'] . PopoverWidget::widget([
                'dataTitle' => null,
                'dataContent' => $hint,
                'dataHtml' => true,
                'dataContainer' => '#valid-check-hint',
                'id' => 'valid-check-hint',
                'eventPropagation' => false,
            ]),
            'usePopover' => false,
            'value' => function ($model) {
                $class = $model->valid_chk ? 'bg-blue' : 'bg-red';
                // 審査OK時しか公開/非公開を切り替えられないようにする
                if ($model->job_review_status_id == JobReviewStatus::STEP_REVIEW_OK) {
                    return GridSubmitButton::widget([
                        'method' => 'POST',
                        'text' => Yii::$app->formatter->asIsPublished($model->valid_chk),
                        'tag' => 'a',
                        'options' => ['class' => 'valid-check ' . $class],
                        'url' => Url::to(['change-display', 'id' => $model->id]),
                        'gridSelector' => '#grid_id',
                        'confirmMessage' => Yii::t('app', '公開／非公開を切り替えてよろしいですか？'),
                        'watch' => false,
                    ]);
                } else {
                    return '－';
                }
            },
        ];
        //操作ボタン
        $listItems[] = ['type' => 'operation', 'buttons' => '{update} {copy} {job-preview}'];

        return $this->render('list', [
            'jobMasterSearch' => $model,
            'dataProvider' => $dataProvider,
            'listItems' => $listItems,
        ]);
    }

    /**
     * 審査ステータスの表示
     * @param JobMaster $model
     * @return string 表示内容
     */
    private function reviewStatusDisplay($model)
    {
        // 審査対象の場合リンク化
        if ($model->isReview()) {
            return Html::a(
                $model->jobReviewStatus->name,
                Url::to(['/manage/secure/job-review/pjax-modal', 'id' => $model->id]),
                ['class' => 'pjaxModal']
            );
        } else {
            return Yii::t('app', $model->jobReviewStatus->name);
        }
    }

    /**
     * 【画面】求人原稿登録
     * @param null $id
     * @return string 描画結果
     * @throws NotFoundHttpException
     */
    public function create($id = null)
    {
        //モデル取得
        if (isset($id)) {
            $jobMaster = $this->model;
            $jobMaster->jobType;
            $jobMaster->jobDist;
            $jobMaster->jobWage;
            $jobMaster->jobStation;
            for ($i = 1; $i <= 20; $i++) {
                $jobMaster->{'jobSearchkeyItem' . $i};
            }
            $jobMaster->setAttribute('job_no', null);
            $jobMaster->setAttribute('id', null);
            $jobMaster->setAttribute('job_review_status_id', null);
            $jobMaster->sourceId = $id;
            $jobMaster->setIsNewRecord(true);
        } else {
            $jobMaster = new JobMaster();
        }

        return $this->render('create', [
            'model' => $jobMaster,
        ]);
    }

    /**
     * 【画面】求人原稿編集
     * @param int $id 求人原稿ID
     * @return string 描画結果
     */
    public function update($id)
    {
        $jobMaster = $this->model;

        return $this->render('update', [
            'model' => $jobMaster,
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionRequirements()
    {
        if (Yii::$app->request->isAjax) {
            $jobMaster = $this->post['id'] ? $this->findModel($this->post['id']) : new JobMaster();
            $plan = ClientChargePlan::find()->select('disp_type_id')->where(['id' => ArrayHelper::getValue($this->post, 'clientChargePlanId')])->one();
            /** @var Manager $identity */
            $identity = Yii::$app->user->identity;
            if ($identity->job_input_type) {
                $viewName = '_classic-requirements';
            } else {
                $viewName = '_requirements';
            }

            return $this->renderAjax('form/_table-form', [
                'id' => $this->post['id'],
                'viewName' => $viewName,
                'pjaxId' => 'requirementsContent',
                'model' => $jobMaster,
                'dispTypeId' => $plan ? $plan->disp_type_id : null,
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 【機能】求人原稿登録
     */
    public function createRegister()
    {
        //インスタンス生成
        $jobMaster = new JobMaster();
        // ロード
        if (!$jobMaster->load($this->post)) {
            throw new Exception('ロード失敗');
        }

        $jobMaster->setScenario($jobMaster->getTypeScenario());
        // 保存
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$jobMaster->validate() || !$jobMaster->save(false)) {
                throw new Exception('エラー');
            }
            $jobMaster->saveRelationalModels($this->post);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        // リダイレクト先を決めるときに必要なためセット
        $this->model = $jobMaster;

        return $this->redirect($this->getRedirectUrl(false));
    }

    /**
     * 【機能】求人原稿編集
     * @param int $id
     * @return string|Response
     * @throws Exception
     */
    public function updateRegister($id)
    {
        //求人原稿取得
        $jobMaster = $this->model;

        // ロード
        if (!$jobMaster->load($this->post)) {
            throw new Exception('ロード失敗');
        }

        $jobMaster->setScenario($jobMaster->getTypeScenario());
        // 保存
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$jobMaster->validate() || !$jobMaster->save(false)) {
                throw new Exception('エラー');
            }
            $jobMaster->unlinkAll('jobDist', true);
            $jobMaster->unlinkAll('jobStation', true);
            $jobMaster->unlinkAll('jobWage', true);
            $jobMaster->unlinkAll('jobType', true);
            $jobMaster->unlinkAll('jobPref', true);
            for ($i = 1; $i <= 20; $i++) {
                $jobMaster->unlinkAll('jobSearchkeyItem' . $i, true);
            }
            $jobMaster->saveRelationalModels($this->post);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect($this->getRedirectUrl(true));
    }


    /**
     * 登録・編集時のリダイレクト先を取得
     * @param boolean $isUpdate
     * @return string
     */
    private function getRedirectUrl($isUpdate)
    {
        // 以下の場合は保存のみ
        //    審査機能OFF
        //    運営元管理者
        //    一時保存ボタン押下
        if (!Yii::$app->tenant->tenant->review_use || Yii::$app->user->identity->isOwner() || $this->post['submitType'] == 'saveOnly') {
            // 保存のみの場合
            $url = Url::toRoute(['complete', 'isUpdate' => $isUpdate]);
        } else {
            // 登録or更新+審査依頼の場合
            $url = Url::toRoute(['/manage/secure/job-review/request', 'id' => $this->model->id, 'isUpdate' => $isUpdate]);
        }
        return $url;
    }

    /**
     * 【画面】登録完了
     * @param boolean $isUpdate
     * @return string 描画結果
     */
    public function actionComplete($isUpdate)
    {
        return $this->render('complete', [
            'isUpdate' => $isUpdate,
        ]);
    }

    /**
     * 【画面】登録・編集のインラインフォーム
     * @return string 描画結果
     */
    public function actionPreview()
    {
        if (Yii::$app->request->isAjax && isset($this->post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $this->post;
        }
        //ヘッダーなどの共通部を除く。
        $this->layout = false;

        $listDisps = null;
        $mainDisps = null;

        if (isset($this->get['dispTypeId'])) {
            //募集項目用データの取得
            $listDisps = ListDisp::items($this->get['dispTypeId']);
            //メイン表示項目データの取得
            $mainDisps = MainDisp::items($this->get['dispTypeId']);
        }
        return $this->render('form/preview/preview', [
            'model' => ArrayHelper::getValue($this->get, 'id') ? JobMaster::findOne($this->get['id']) : new JobMaster(),
            'mainDisps' => $mainDisps,
            'listDisps' => $listDisps,
        ]);
    }

    /**
     * 画像選択・アップロードのモーダルコンテンツ
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPic()
    {
        if (Yii::$app->request->isAjax && isset($this->post['clientMasterId'])) {
            $jobPic = new JobPic(['client_master_id' => $this->post['clientMasterId']]);
            return $this->render('form/_pic-content', [
                'jobPic' => $jobPic,
            ]);
        }
        throw new NotFoundHttpException();
    }

    /**
     * 掲載企業に紐付いた画像のアップロード
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionUploadPic()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $jobPic = new JobPic();
            $jobPic->load($this->post);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // ファイルをアップロードしてDBのレコードを追加
                if (!$jobPic->save() || !$jobPic->saveFiles()) {
                    throw new \Exception();
                }
                $this->session->setFlash('message', Yii::t('app', '画像のアップロードが完了しました'));
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                $message = implode('<br>', $jobPic->errorMessages());
                $this->session->setFlash('errorMessage', $message ?: Yii::t('app', '画像情報の更新に失敗しました'));
            }

            $jobPic = new JobPic(['client_master_id' => $jobPic->client_master_id]);
            return $this->render('form/_pic-content', ['jobPic' => $jobPic]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 掲載企業に紐付いた画像の削除
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDeletePic()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $id = ArrayHelper::getValue($this->post, 'id');
            /** @var JobPic $jobPic */
            $jobPic = JobPic::find()->where([JobPic::tableName() . '.id' => $id])->addAuthQuery()->one();

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 画像レコード削除
                if (!$jobPic->delete()) {
                    throw new \Exception();
                }
                $this->session->setFlash('message', Yii::t('app', '画像の削除が完了しました'));
                $transaction->commit();
                // コミットしてから画像実体削除
                $jobPic->deleteFile();
            } catch (Exception $e) {
                $transaction->rollBack();
                $this->session->setFlash('errorMessage', Yii::t('app', '画像の削除に失敗しました'));
            }

            $jobPic = new JobPic(['client_master_id' => ArrayHelper::getValue($this->post, 'client_master_id')]);
            return $this->render('form/_pic-content', ['jobPic' => $jobPic]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * client_idから掲載プランのドロップダウン用json配列を取得するAjaxアクション
     * todo 他のと似ている部分を共通化もしくは統合
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionPlanList()
    {
        if (Yii::$app->request->isAjax && isset($this->post['depdrop_parents'])) {
            $planList = ClientChargePlan::getDropDownArray(
                false,
                ArrayHelper::getValue($this->post, 'depdrop_parents.0') ?: false // 値が無い場合はfalseを入れて何も取得させない
            ); // 有効なものみ取得
            $out = [];
            foreach ($planList as $id => $name) {
                $out[] = ['id' => $id, 'name' => $name];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['output' => $out, 'selected' => ''];
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * モデルの取得
     * @param int $id 求人原稿ID
     * @return JobMaster モデル
     * @throws NotFoundHttpException モデルが見つからなかったとき
     */
    protected function findModel($id)
    {
        /** @var JobMaster $model */
        $model = JobMaster::find()
            ->with([
                'clientMaster',
                'jobType',
                'jobDist',
                'jobStation.station',
            ])
            ->where([JobMaster::tableName() . '.id' => $id])->one();
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * CSVダウンロードアクション
     */
    public function actionCsvDownload()
    {
        $searchModel = new JobMasterSearch();
        $dataProvider = $searchModel->csvSearch($this->get);
        ExportHelper::outputAsCSV(
            $dataProvider,
            'JobMasterList_' . date('YmdHi') . '.csv',
            $searchModel->csvAttributes(),
            JobMasterSearch::STRESS_MODE_PAGE_SIZE    //ExportHelper::outputAsCSVでの処理件数を引き上げる
        );
    }

    /**
     * JobMaster AjaxValidation
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAjaxValidation($id)
    {
        if (Yii::$app->request->isAjax && isset($this->post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($id) {
                $model = JobMaster::find()->select('client_charge_plan_id')->where(['id' => $id])->one();
            } else {
                $model = new JobMaster();
            }

            $model->setScenario(JobMaster::SCENARIO_AJAX_VALIDATION);
            $model->load($this->post);

            $jobDist = new JobDist();
            $jobDist->load($this->post);

            return array_merge(ActiveForm::validate($model), ActiveForm::validate($jobDist));
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 【画面】求人原稿コピー
     * @param int $id 仕事ID
     * @return string 描画結果
     */
    public function actionCopy($id)
    {
        // アクセスルートによってタイトルなど制御しているため変更している。
        Yii::$app->requestedRoute = '/manage/secure/job/create';
        return $this->create($id);
    }

    /**
     * 削除アクション
     */
    public function actionDelete()
    {
        $searchModel = new JobMasterSearch();
        // 削除するidを取得してそれを元に削除して削除件数をセット
        $deleteModels = $searchModel->deleteSearch($this->post);
        $deleteCount = $searchModel->backupAndDelete($deleteModels);
        $this->session->setFlash('deleteCount', $deleteCount);
        // postからqueryパラメータ以外を除去してリダイレクト
        $this->redirect(['list'] + $this->removeExtraParams($this->post));
    }

    /**
     * 有効/無効 切替アクション
     */
    public function actionChangeDisplay($id = null)
    {
        $model = JobMaster::findOne($id);
        if (isset($model)) {
            $model->valid_chk = $model->valid_chk === JobMaster::FLAG_VALID ? JobMaster::FLAG_INVALID : JobMaster::FLAG_VALID;
            $model->save(false);
            return $this->redirect(['list'] + $this->removeExtraParams($this->post));
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * select2駅用ajax
     * @param string $q 検索文字列
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAjaxStation($q = null)
    {
        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];
            if ($q !== null) {
                $data = Station::find()->where(['like', 'station_name', $q])->all();
                $data = ArrayHelper::getColumn($data, function ($v) {
                    return ['id' => $v->station_no, 'text' => $v->station_name];
                });
                $out['results'] = array_values(array_unique($data, SORT_REGULAR));
            }
            return $out;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 募集要項入力方式を切り替えて元画面にリダイレクトする
     * @param $selected
     * @return Response
     */
    public function actionChangeMode($selected)
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        $identity->job_input_type = $selected;
        $identity->save(false);
        return $this->redirect(Yii::$app->request->referrer);
    }
}
