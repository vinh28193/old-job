<?php

namespace app\modules\manage\controllers\secure;

use app\models\manage\AccessLog;
use app\models\manage\AccessLogDailySearch;
use app\modules\manage\controllers\CommonController;
use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\common\AccessControl;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use proseeds\helpers\ExportHelper;
use app\common\CorpClientPlanDepDropTrait;
use app\models\forms\JobSearchForm;

/**
 * 日別アクセス数集計機能コントローラ
 *
 * @author Yukinori Nakamura
 */
class AnalysisDailyController extends CommonController
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
                    'confirm' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list'],
                        'roles' => ['owner_admin', 'corp_admin', 'client_admin'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->can('analysisDailyListException');
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

        $page = ArrayHelper::getValue($this->get, 'page');
        $sort = ArrayHelper::getValue($this->get, 'sort');
        
        //GoogleAnalyticsのデータをaccess_logテーブルに登録する
        if (!isset($page) && !isset($sort)) {
            $googleAnalytics = Yii::$app->googleAnalytics;
            $googleAnalytics->saveGoogleAnalytics();
        }

        //管理者の検索クラス
        $accessLogDailySearch = new AccessLogDailySearch();

        //検索後の一覧取得
        $dataProvider = $accessLogDailySearch->search($this->get);
        //複数選択用チェックボックス
        $listItems = [['type' => 'checkBox']];

        //アクセス日（yyyy/mm/dd）
        $listItems[] = ['type' => '', 'attribute' => 'accessDate', 'sort' => 'true', 'format' => 'date' ];

        // 運営元管理者の場合
        $identity = Yii::$app->user->identity;
        if ($identity->myRole == Manager::OWNER_ADMIN) {
            //全国TOP_PC
            $listItems[] = ['type' => '', 'attribute' => 'zenkokuPc'];

            //全国TOP_スマートフォン
            $listItems[] = ['type' => '', 'attribute' => 'zenkokuSp'];

            $jobSearchForm = new JobSearchForm();
            // ワンエリア表示の場合は、「エリアTOP」を表示しない。
            if (count($jobSearchForm->areas) != 1) {
                //エリアTOP_PC
                $listItems[] = ['type' => '', 'attribute' => 'areaPc'];

                //エリアTOP_スマートフォン
                $listItems[] = ['type' => '', 'attribute' => 'areaSp'];
            }
        }

        //求人詳細_PC
        $listItems[] = ['type' => '', 'attribute' => 'jobPc', 'layout'];

        //求人詳細_スマートフォン
        $listItems[] = ['type' => '', 'attribute' => 'jobSp', 'layout'];

        //応募完了_PC
        $listItems[] = ['type' => '', 'attribute' => 'applicationPc'];

        //応募完了_スマートフォン
        $listItems[] = ['type' => '', 'attribute' => 'applicationSp'];

        return $this->render('list', [
            'accessLogDailySearch' => $accessLogDailySearch,
            'dataProvider' => $dataProvider,
            'listItems' => $listItems,
        ]);
    }

    /**
     * CSVダウンロードアクション
     */
    public function actionCsvDownload()
    {
        $searchModel = new AccessLogDailySearch();
        $jobSearchForm = new JobSearchForm();
        $identity = Yii::$app->user->identity;
        if ($identity->myRole == Manager::OWNER_ADMIN) {
            // ワンエリア表示の場合は、「エリアTOP」を表示しない。
            if (count($jobSearchForm->areas) == 1) {
                $csvRelationColumn = ['accessDate', 'prefName', 'corpName', 'jobNo', 'zenkokuPc', 'zenkokuSp', 'jobPc', 'jobSp', 'applicationPc', 'applicationSp'];
            } else {
                $csvRelationColumn = ['accessDate', 'prefName', 'corpName', 'jobNo', 'zenkokuPc', 'zenkokuSp', 'areaPc', 'areaSp', 'jobPc', 'jobSp', 'applicationPc', 'applicationSp'];
            }
        } else {
            $csvRelationColumn = ['accessDate', 'prefName', 'corpName', 'jobNo', 'jobPc', 'jobSp', 'applicationPc', 'applicationSp'];
        }

        $dataProvider = $searchModel->csvSearch($this->get);
        ExportHelper::outputAsCSV(
            $dataProvider,
            'AccessLogDailyList_' . date('YmdHi') . '.csv',
            $csvRelationColumn
        );
    }
}
