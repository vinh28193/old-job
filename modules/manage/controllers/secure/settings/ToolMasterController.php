<?php
namespace app\modules\manage\controllers\secure\settings;

use app\common\AccessControl;
use app\models\ToolMaster;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\manage\controllers\CommonController;
use app\models\FileUploader;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use app\modules\manage\components\ToolMasterCsvLoader;
use app\common\csv\CsvWorker;
use app\common\csv\CsvDataProvider;
use proseeds\helpers\ExportHelper;

/**
 * ToolMasterController implements the CRUD actions for ToolMaster model.
 */
class ToolMasterController extends CommonController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * CSV入力方法画面
     */
    public function actionIndex()
    {
        return $this->render('index', []);
    }

    /**
     * CSVアップロード処理
     *
     * @return string
     */
    public function actionCsvUpload()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

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
            return $this->redirect(Url::to('index'));
        }

        if ($this->hasDuplicateLines($filename)) {
            return $this->redirect(Url::to('index'));
        }

        return $this->render('verify', ['filename' => $filename]);
    }

    /**
     * 重複行確認
     *
     * noとページ名の重複確認
     * @param string $filename
     * @return bool
     */
    private function hasDuplicateLines($filename)
    {
        $file = new \SplFileObject(FileUploader::TEMP_PATH . $filename);
        $file->setFlags(\SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::READ_CSV);
        $tmp = [];
        $lineNoA = $lineNoB = 0;
        foreach ($file as $line) {
            if ($file->key() == 0) continue;
            if (empty($line) || is_null($line[0])) continue;

            $lineNoB++;
            //                  no    と ページ名
            $key = urlencode($line[0] . $line[1]);
            if (array_key_exists($key, $tmp)) {
                $lineNoA = array_search($key, array_keys($tmp)) + 1;
                break;
            }
            $tmp[$key] = true;
        }

        if ($lineNoA > 0) {
            Yii::$app->session->setFlash('csvError', Yii::t('app', '{lineNoA}行目と{lineNoB}行目が重複しています。', compact('lineNoA', 'lineNoB')));

            return true;
        }

        return false;
    }

    /**
     * 検証画面 (ワーカー)
     * @param string|null $filename 検証するCSVファイル名
     */
    public function actionVerifyWorker($filename = null)
    {
        $worker = new CsvWorker([
            'filename' => FileUploader::TEMP_PATH . $filename,
            'loaderClass' => ToolMasterCsvLoader::className()
        ]);
        $worker->validate();
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
            return $this->redirect(Url::to('index'));
        }

        $dataProvider = new CsvDataProvider([
            'filename' => FileUploader::TEMP_PATH . $filename,
            'loaderClass' => ToolMasterCsvLoader::className(),
        ]);

        return $this->render('confirm', ['dataProvider' => $dataProvider, 'filename' => $filename]);
    }

    /**
     * 登録画面
     * @return string|\yii\web\Response
     */
    public function actionRegister()
    {
        $filename = Yii::$app->request->post('filename');
        if (!Yii::$app->request->isPost || is_null($filename)) {
            return $this->redirect(Url::to(['index']));
        }

        return $this->render('register', ['filename' => $filename]);
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
            'loaderClass' => ToolMasterCsvLoader::className()
        ]);
        $worker->save();
    }

    /**
     * 完了画面
     * @param integer|null $count
     * @return string
     */
    public function actionComplete($count = null)
    {
        return $this->render('complete', ['count' => $count]);
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
        $dataProvider = new ActiveDataProvider(['query' => ToolMaster::find()]);
        ExportHelper::outputAsCSV($dataProvider, 'tdk.csv', (new ToolMasterCsvLoader())->getCsvAttributes());
    }
}
