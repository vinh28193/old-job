<?php

namespace app\modules\manage\controllers\secure;

use app\models\manage\AccessLog;
use app\models\manage\AccessLogSearch;
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
use app\common\AccessLogDropDownTrait;

/**
 * ページ別アクセス数確認機能コントローラ
 *
 * @author Yukinori Nakamura
 */
class AnalysisPageController extends CommonController
{
    /**
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
        $accessLogSearch = new AccessLogSearch();
        //検索後の一覧取得
        $dataProvider = $accessLogSearch->search($this->get);
        //複数選択用チェックボックス
        $listItems = [['type' => 'checkBox']];

        //アクセスページ
        $listItems[] = ['type' => '', 'attribute' => 'accessPageName'];

        //アクセス日（yyyy/mm/dd）
        $listItems[] = ['type' => '', 'attribute' => 'accessed_at', 'format' => 'dateTime'];

        //仕事ID
        $listItems[] = ['type' => '', 'attribute' => 'jobNo'];

        //アクセス機器
        $listItems[] = ['type' => '', 'attribute' => 'carrier_type', 'format' => 'carrierType'];

        //アクセスURL
        $listItems[] = ['type' => '', 'attribute' => 'access_url'];

        //ユーザーエージェント
        $listItems[] = ['type' => '', 'attribute' => 'access_user_agent'];

        //リファラー
        $listItems[] = ['type' => '', 'attribute' => 'access_referrer'];

        return $this->render('list', [
            'accessLogSearch' => $accessLogSearch,
            'dataProvider' => $dataProvider,
            'listItems' => $listItems,
        ]);
    }

    /**
     * CSVダウンロードアクション
     */
    public function actionCsvDownload()
    {
        $searchModel = new AccessLogSearch();
        $csvRelationColumn = ['accessPageName', 'accessed_at:dateTime', 'jobNo', 'carrier_type:carrierType', 'access_url', 'access_user_agent', 'access_referrer'];

        $dataProvider = $searchModel->csvSearch($this->get);
        ExportHelper::outputAsCSV(
            $dataProvider,
            'AccessLogPageList_' . date('YmdHi') . '.csv',
            $csvRelationColumn
        );
    }
}
