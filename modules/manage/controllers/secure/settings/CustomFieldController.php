<?php

namespace app\modules\manage\controllers\secure\settings;

use app\common\Helper\JmUtils;
use ReflectionClass;
use SplFileObject;
use yii;
use yii\base\Exception;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use proseeds\helpers\ExportHelper;
use app\common\csv\CsvDataProvider;
use app\common\csv\CsvWorker;
use app\common\AccessControl;
use app\models\FileUploader;
use app\modules\manage\controllers\CommonController;
use app\modules\manage\components\CustomFieldCsvLoader;
use app\models\manage\CustomField;
use app\models\manage\CustomFieldSearch;

/**
 * カスタムフィールドコントローラ
 *
 * Class CustomFieldController
 * @package app\modules\manage\controllers\secure\settings
 */
class CustomFieldController extends CommonController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'csv-upload' => ['post'],
                    'register' => ['post'],

                ],
            ],
            // 運営元権限のみが操作できるアクセス制限追加
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * リスト表示アクション
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new CustomFieldSearch();
        $dataProvider = $searchModel->search($this->get);
        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 削除アクション
     */
    public function actionDelete()
    {
        $searchModel = new CustomFieldSearch();
        $models = $searchModel->deleteSearch($this->post);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 画像レコード削除
            $deleteCount = CustomFieldSearch::deleteRecords($models);
            $this->session->setFlash('deleteCount', $deleteCount);
            $transaction->commit();
            CustomFieldSearch::deleteFiles($models);
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        // postからqueryパラメータ以外を除去してリダイレクト
        return $this->redirect(['list'] + $this->removeExtraParams($this->post));
    }

    /**
     * 新規登録アクション
     * @return Response
     */
    public function actionCreate()
    {
        $model = new CustomField();
        $model->load($this->post);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                throw new Exception;
            }
            $model->saveFiles();
            $this->session->setFlash('resultComment', Html::tag('p', Yii::t('app', '登録が完了しました。'), ['class' => 'alert alert-warning']));
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            //todo 例外処理のエラーメッセージなど決まれば修正する
            $this->session->setFlash('resultComment', Html::tag('p', Yii::t('app', '登録に失敗しました。'), ['class' => 'alert alert-warning']));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * 更新処理
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpdate()
    {
        $id = ArrayHelper::getValue($this->get, 'id');
        $model = $this->findModel($id);
        // todo 削除フラグもloadできるようにviewを修正
        $model->deleteFileFlg = $this->post['pushDeletePict'] ?? false;
        $model->load($this->post);


        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                throw new Exception;
            }


            if (UploadedFile::getInstance($model, 'pict')) {
                // ファイルがpostされていればファイルをアップロードする
                $model->saveFiles();
            } elseif ($model->deleteFileFlg) {
                // ファイルポストが無くても削除フラグが立っていたら旧画像を削除する
                $model->deleteOldFile();
            }

            $this->session->setFlash('resultComment', Html::tag('p', Yii::t('app', '更新が完了しました。'), ['class' => 'alert alert-warning']));
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            //todo 例外処理のエラーメッセージなど決まれば修正する
            $this->session->setFlash('resultComment', Html::tag('p', Yii::t('app', '更新に失敗しました。'), ['class' => 'alert alert-warning']));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * フォーム画面検証用のajaxバリデーションアクション
     * @param $id
     * @return array
     */
    public function actionAjaxValidation($id = null)
    {
        if ($id) {
            $model = CustomField::findOne($id);
        } else {
            $model = new CustomField();
        }
        $model->load($this->post);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPjaxModal($id = null)
    {
        $model = null;
        if ($id) {
            $model = $this->findModel($id);
        } else {
            $model = new CustomField();
        }

        return $this->renderAjax('_input-fields', [
            'model' => $model,
        ]);
    }

    /**
     * CSV初期画面
     */
    public function actionCsv()
    {
        return $this->render('csv');
    }

    /**
     * CSV入力方法画面
     * @return string
     */
    public function actionHelp()
    {
        $this->layout = 'popup';
        return $this->render('help');
    }

    /**
     * CSVのダウンロード
     */
    public function actionCsvDownload()
    {
        $searchModel = new CustomFieldSearch();
        $dataProvider = $searchModel->csvSearch($this->get);
        $fileName = 'CustomField_' . date('YmdHi') . '.csv';
        $columns = (new CustomFieldCsvLoader())->getCsvAttributes();
        ExportHelper::outputAsCSV($dataProvider, $fileName, $columns);
    }

    /**
     * CSVアップロード処理
     *
     * @return string
     */
    public function actionCsvUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $model = new FileUploader(['extensions' => ['csv']]);
            $model->file = UploadedFile::getInstanceByName('file');
            if ($model->validate()) {
                if ($fileName = $model->uploadToTemp()) {
                    return ['filename' => $fileName];
                } else {
                    return ['error' => Yii::t('app', 'ファイルのアップロードに失敗しました。')];
                }
            } else {
                return ['error' => $model->getErrors('file')];
            }
        }
        return false;
    }

    /**
     * 検証画面
     *
     * @param string|null $filename 検証するCSVファイル名
     * @return string|\yii\web\Response
     */
    public function actionVerify($filename = null)
    {
        if (is_null($filename) || !FileUploader::isTempFileExists($filename)) {
            Yii::$app->session->setFlash('csvError', Yii::t('app', '指定されたファイルは存在しません'));
            return $this->redirect(Url::to('csv'));
        }

        if ($this->hasDuplicateLines($filename)) {
            return $this->redirect(Url::to('csv'));
        }

        return $this->render('verify', ['filename' => $filename]);
    }

    /**
     * 確認画面
     * @param string|null $filename 確認画面に表示するCSVのファイル名
     * @return string|\yii\web\Response
     */
    public function actionConfirm($filename = null)
    {
        if (is_null($filename) || !FileUploader::isTempFileExists($filename)) {
            Yii::$app->session->setFlash('csvError', Yii::t('app', '指定されたファイルは存在しません'));
            return $this->redirect(Url::to('csv'));
        }

        $dataProvider = new CsvDataProvider([
            'filename' => FileUploader::TEMP_PATH . $filename,
            'loaderClass' => CustomFieldCsvLoader::className(),
        ]);

        return $this->render('confirm', ['dataProvider' => $dataProvider, 'filename' => $filename]);
    }

    /**
     * 完了画面
     * @param integer|null $count 登録数
     * @return string
     */
    public function actionComplete($count = null)
    {
        return $this->render('complete', ['count' => $count]);
    }

    /**
     * 検証画面 (ワーカー)
     * @param string|null $filename 検証するCSVファイル名
     */
    public function actionVerifyWorker($filename = null)
    {
        $worker = new CsvWorker([
            'filename' => FileUploader::TEMP_PATH . $filename,
            'loaderClass' => CustomFieldCsvLoader::className()
        ]);
        $worker->validate();
    }

    /**
     * 登録画面 (ワーカー)
     * @param string $filename 登録するに用いるCSVのファイル名
     * @throws \Exception
     */
    public function actionRegisterWorker($filename = null)
    {
        $worker = new CsvWorker([
            'filename' => FileUploader::TEMP_PATH . $filename,
            'loaderClass' => CustomFieldCsvLoader::className()
        ]);
        $worker->save();
    }

    /**
     * 登録画面
     * @return string|\yii\web\Response
     */
    public function actionRegister()
    {
        $filename = Yii::$app->request->post('filename');
        if (!Yii::$app->request->isPost || is_null($filename)) {
            return $this->redirect(Url::to('list'));
        }

        return $this->render('register', ['filename' => $filename]);
    }

    /**
     * モデル取得処理
     * @param integer $id
     * @return CustomField the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomField::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 重複行確認
     * urlの重複確認
     * @param string $filename
     * @return bool
     */
    private function hasDuplicateLines($filename)
    {
        $file = new SplFileObject(FileUploader::TEMP_PATH . $filename);
        $file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::READ_CSV);
        $tmp = [];
        $lineNoA = $lineNoB = 0;
        foreach ($file as $line) {
            if ($file->key() == 0) {
                continue;
            }

            $lineNoB++;

            if (empty($line) || is_null($line[0])) {
                continue;
            }

            // url
            $key = urlencode($line[2]?? '');
            if (array_key_exists($key, $tmp)) {
                $lineNoA = $tmp[$key];
                break;
            }
            $tmp[$key] = $lineNoB;
        }

        if ($lineNoA > 0) {
            Yii::$app->session->setFlash('csvError', Yii::t('app', '{lineNoA}行目と{lineNoB}行目のURLが重複しています。', compact('lineNoA', 'lineNoB')));
            return true;
        }
        return false;
    }
}
