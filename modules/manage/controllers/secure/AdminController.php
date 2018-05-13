<?php

namespace app\modules\manage\controllers\secure;

use app\models\manage\AdminMaster;
use app\models\manage\AdminMasterSearch;
use app\modules\manage\controllers\CommonController;
use app\models\manage\SendMailSet;
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use app\models\manage\SiteMaster;
use yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\db\Exception;
use app\common\AccessControl;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use proseeds\helpers\ExportHelper;
use app\common\constants\MailConst;
use app\models\MailSend;
use app\common\mail\MailSender;
use app\common\CorpClientPlanDepDropTrait;

/**
 * 管理者機能コントローラ
 *
 * @author Yukinori Nakamura
 */
class AdminController extends CommonController
{
    /*
     * DepDrop用にAjaxアクション
     */
    use CorpClientPlanDepDropTrait;
    
    /**
     * ビヘイビア設定
     * @return array
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
                'only' => ['list', 'update', 'create', 'delete', 'profile'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'update', 'delete'],
                        'roles' => ['owner_admin'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('adminListException');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['owner_admin'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('adminCreateException');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['profile'],
                        'roles' => ['owner_admin', 'corp_admin', 'client_admin'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('adminProfileException');
                        },
                    ],
                ],
            ],
        ]);
    }

    /**
     * 【画面】管理者情報一覧
     * @return string 描画結果
     */
    public function actionList()
    {
        //管理者の検索クラス
        $adminMasterSearch = new AdminMasterSearch();
        //検索後の一覧取得
        $dataProvider = $adminMasterSearch->search($this->get);
        //複数選択用チェックボックス
        $listItems = [['type' => 'checkBox']];
        //メインの表示データ（functionItemSetテーブルに基づく）
        foreach ((array)\Yii::$app->functionItemSet->admin->listAttributes as $attr) {
            $listItems[] = [
                'type' => '',
                'attribute' => $attr,
                'value' => AdminMasterSearch::getColumnName($attr),
            ];
        }
        //状態
        $listItems[] = ['type' => '', 'attribute' => 'valid_chk', 'layout' => '{value}', 'format' => 'validChk'];
        //操作ボタン
        $listItems[] = ['type' => 'operation', 'buttons' => '{update}'];

        return $this->render('list', [
            'adminMasterSearch' => $adminMasterSearch,
            'dataProvider' => $dataProvider,
            'listItems' => $listItems,
        ]);
    }

    /**
     * 【画面】管理者登録
     * @return string 描画結果
     */
    public function create()
    {
        $adminMaster = new AdminMaster();
        return $this->render('create', [
            'model' => $adminMaster,
        ]);
    }

    /**
     * 【画面】管理者編集
     * @param int $id 管理者ID
     * @return string 描画結果
     */
    public function update($id)
    {
        $adminMaster = $this->findModel($id);
        return $this->render('update', [
            'model' => $adminMaster,
        ]);
    }

    /**
     * 【画面】マイプロフィール編集
     * @return string 描画結果
     */
    public function actionProfile()
    {
        if (isset($this->post['complete'])) {
            return $this->profileRegister();
        }
        //モデル取得
        $adminMaster = $this->findModel(Yii::$app->user->id);
        $adminMaster->load($this->post);
        return $this->render('profile', [
            'model' => $adminMaster,
        ]);
    }

    /**
     * 【機能】管理者登録
     * @return string 描画結果
     */
    public function createRegister()
    {
        //インスタンス生成
        $adminMaster = new AdminMaster();
        $adminMaster->load($this->post);
        //トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //管理者保存
            if (!$adminMaster->save()) {
                throw new Exception('エラー');
            }

            //除外する管理権限保存
            $adminMaster->saveAuthExceptions($this->post);

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return $this->redirect(Url::toRoute('create'));
        }

        //パスワードのメール送信機能
        if (!empty($adminMaster->sendPass)) {
            $mailSet = SendMailSet::findOne(['mail_type_id' => MailSend::TYPE_ADMN_CREATE]);
            $mailSet->model = $adminMaster;

            $mailSender = new MailSender();
            $mailSender->sendAutoMail($mailSet);
        }

        return $this->redirect(Url::toRoute('complete'));
    }

    /**
     * 【機能】管理者編集
     * @return string 描画結果
     */
    public function updateRegister($id)
    {
        //インスタンス生成
        $adminMaster = $this->findModel($id);
        $adminMaster->load($this->post);
        //トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //管理者保存
            if (!$adminMaster->save()) {
                throw new Exception('エラー');
            }
            //除外する管理者権限保存
            $adminMaster->saveAuthExceptions($this->post);

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return $this->redirect(Url::toRoute('update?id=' . $id));
        }

        return $this->redirect(Url::toRoute('complete') . '?isUpdate=true');
    }

    /**
     * 【機能】マイプロフィール編集
     * @return string 描画結果
     */
    public function profileRegister()
    {
        //インスタンス生成
        $adminMaster = $this->findModel(Yii::$app->user->id);
        $adminMaster->load($this->post);
        //トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //管理者保存
            if (!$adminMaster->save()) {
                throw new Exception('エラー');
            }
            //除外する管理者権限保存
            $adminMaster->saveAuthExceptions($this->post);

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return $this->redirect(Url::toRoute('profile'));
        }

        return $this->redirect(Url::toRoute('complete') . '?isUpdate=true&isProfile=true');
    }

    /**
     * 【画面】登録完了
     * @return string 描画結果
     */
    public function actionComplete()
    {
        $isUpdate = isset($this->get["isUpdate"]) ? $this->get["isUpdate"] : false;
        $label = isset($this->get["isProfile"]) ? 'マイプロフィール' : '管理者情報';

        return $this->render('complete', [
            'isUpdate' => $isUpdate,
            'label' => $label,
        ]);
    }

    /**
     * モデルの取得
     * @param int $id ID
     * @return AdminMaster モデル
     * @throws NotFoundHttpException モデルが見つからなかったとき
     */
    protected function findModel($id)
    {
        $model = AdminMaster::find()->with('corpMaster', 'clientMaster')->where(['id' => $id])->one();
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * フォーム画面検証用のajaxバリデーションアクション
     * @param $id
     * @return array
     */
    public function actionAjaxValidation($id)
    {
        if ($id) {
            $model = AdminMaster::findOne($id);
        } else {
            $model = new AdminMaster();
        }
        $model->load($this->post);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }

    /**
     * roleに対応する除外する管理者権限メニュのリスト取得ajaxアクション
     * @return array|bool
     */
    public function actionAjaxAuthMenu()
    {
        if (!isset($this->post['role'])) {
            return false;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        //Todo valid_chk値定数化
        $manageMenuMain = ManageMenuMain::find()->andWhere(['valid_chk' => 1]);

        //管理者種別ごとに取得する条件を設定
        switch ($this->post['role']) {
            case Manager::CORP_ADMIN:
                $manageMenuMain->andFilterWhere(['or', ['permitted_role' => Manager::CORP_ADMIN], ['permitted_role' => Manager::CLIENT_ADMIN]]);
                break;
            case Manager::CLIENT_ADMIN:
                $manageMenuMain->andFilterWhere(['permitted_role' => Manager::CLIENT_ADMIN]);
                break;
        }
        return ArrayHelper::map($manageMenuMain->all(), 'exception', 'title');
    }

    /**
     * csvで出力できるように整形する
     * @param $columns
     * @return array
     */
    public function parseColumnNameForCsv($columns)
    {
        $columnList = [];
        foreach ((array)$columns as $item) {
            $columnList[] = AdminMasterSearch::getColumnName($item);
        }
        return $columnList;
    }

    /**
     * CSVダウンロードアクション
     */
    public function actionCsvDownload()
    {
        $searchModel = new AdminMasterSearch();
        $getAdminColumnList = ArrayHelper::getColumn(Yii::$app->functionItemSet->admin->Items, 'column_name');
        foreach ($getAdminColumnList as $key => $value) {
            if ($value == 'exceptions') {
                unset($getAdminColumnList[$key]);
            }
        }

        $csvRelationColumn = $this->parseColumnNameForCsv($getAdminColumnList);

        $dataProvider = $searchModel->csvSearch($this->get);
        ExportHelper::outputAsCSV(
            $dataProvider,
            'AdminMasterList_' . date('YmdHi') . '.csv',
            $csvRelationColumn + ['valid_chk:validChk']
        );
    }

    /**
     * 削除アクション
     */
    public function actionDelete()
    {
        $this->deleteByGridCheckBox(new AdminMasterSearch());
        // postからqueryパラメータ以外を除去してリダイレクト
        $this->redirect(['list'] + $this->removeExtraParams($this->post));
    }
}
