<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/09/09
 * Time: 11:20
 */

namespace app\modules\manage\controllers\secure;


use app\models\manage\SearchkeyMaster;
use app\modules\manage\components\JobCsvLoader;
use app\modules\manage\controllers\CommonController;
use proseeds\helpers\ExportHelper;
use yii;
use yii\helpers\Url;
use app\models\FileUploader;
use yii\web\UploadedFile;
use app\common\csv\CsvDataProvider;
use app\common\csv\CsvWorker;
use app\models\manage\JobMasterSearch;
use app\common\AccessControl;
use app\models\manage\ClientCharge;

/**
 * Class JobCsvController
 * @package app\modules\manage\controllers
 */
class JobCsvController extends CommonController
{
    /** @var integer 求人最大 */
    const ERROR_LIMIT_COUNT = 100;

    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['owner_admin'],
                        'denyCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('jobCsvException');
                        },
                    ],
                ],
            ],
        ]);
    }
    
    /**
     * CSVアップロード画面
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
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
     * 検証画面
     * @param string|null $filename 検証するCSVファイル名
     * @return string|\yii\web\Response
     */
    public function actionVerify($filename = null)
    {
        if (is_null($filename) || !FileUploader::isTempFileExists($filename)) {
            Yii::$app->session->setFlash('csvError', Yii::t('app', '指定されたファイルは存在しません'));
            return $this->redirect(Url::to('index'));
        }

        return $this->render('verify', ['filename' => $filename]);
    }

    /**
     * 検証画面 (ワーカー)
     * @param string|null $filename 検証するCSVファイル名
     */
    public function actionVerifyWorker($filename = null)
    {
        $worker = new CsvWorker([
            'filename' => FileUploader::TEMP_PATH . $filename,
            'loaderClass' => JobCsvLoader::className(),
            'errorLimitCount' => self::ERROR_LIMIT_COUNT,
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
            'loaderClass' => JobCsvLoader::className(),
        ]);

        $listItems = [];
        foreach ((array)Yii::$app->functionItemSet->job->listAttributes as $attribute) {
            if ($attribute == 'disp_start_date' || $attribute == 'disp_end_date') {
                $listItems[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'value' => JobMasterSearch::getColumnName($attribute),
                    'format' => 'date',
                ];
            } else if ($attribute == 'corpLabel') {

            } else if ($attribute == 'client_master_id') {
                $listItems[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'value' => JobMasterSearch::getColumnName('clientNo'),
                ];
            }  else if ($attribute == 'client_charge_plan_id') {
                $listItems[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'value' => JobMasterSearch::getColumnName('clientChargePlanNo'),
                ];
            } else {
                $listItems[] = [
                    'type' => '',
                    'attribute' => $attribute,
                    'value' => JobMasterSearch::getColumnName($attribute),
                ];
            }
        }
        //状態
        $listItems[] = ['type' => '', 'attribute' => 'valid_chk', 'layout' => '{value}', 'format' => 'validChk', 'headerClass' => 'ss-column'];

        return $this->render('confirm', [
            'dataProvider' => $dataProvider,
            'filename' => $filename,
            'listItems' => $listItems,
        ]);
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
            'loaderClass' => JobCsvLoader::className(),
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
     * CSVテンプレートのダウンロード
     */
    public function actionCsvDownload()
    {
        $jobMasterSearch = new JobMasterSearch();
        ExportHelper::outputAsCSV($jobMasterSearch->search([]), 'csv_import_job.csv', $jobMasterSearch->csvAttributes());
        return;
    }

    /**
     * ファイルをアップロードする
     * @return array|bool
     */
    public function actionUpload()
    {
        if (Yii::$app->request->isPost) {
            $model = new FileUploader(['extensions' => ['csv']]);
            $model->file = UploadedFile::getInstanceByName('file');
            if ($model->validate()) {
                if ($fileName = $model->uploadToTemp()) {
                    return json_encode(['filename' => $fileName]);
                } else {
                    return json_encode(['error' => Yii::t('app', 'ファイルのアップロードに失敗しました。')]);
                }
            } else {
                return json_encode(['error' => $model->getErrors('file')]);
            }
        }

        return false;
    }

    /**
     * 検索キーコードのCSVダウンロード
     * @param int $id
     */
    public function actionSearchkeyCsvDownload($id)
    {
        /** @var SearchkeyMaster $model */
        $model = SearchkeyMaster::findOne(['id' => $id]);
        ExportHelper::outputAsCSV(
            $model->keyCsvSearch(),
            $model->csvFileName(),
            $model->searchkeyCsvAttributes()
        );
        return;
    }

    /**
     * 料金プランのCSVダウンロード
     */
    public function actionClientChargePlanCsvDownload()
    {
        /** @var ClientCharge $model */
        $model = new ClientCharge;
        ExportHelper::outputAsCSV(
            $model->keyCsvSearch(),
            $model->csvFileName(),
            $model->searchkeyCsvAttributes()
        );
        return;
    }
}
