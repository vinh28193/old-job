<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2015/10/31
 * Time: 17:32
 */

namespace app\modules\manage\controllers;

use app\models\manage\AccessCount;
use app\models\manage\ApplicationMaster;
use Yii;
use app\common\AccessControl;

class SecureController extends CommonController
{
    /**
     * topで使うAccessLogMonthlyのattribute
     */
    const YESTERDAY_COUNT_ITEMS = [
        'detail_count_pc',
        'detail_count_smart',
        'application_count_pc',
        'application_count_smart',
        'applicationCountTotal',
        'memberCountTotal',
        'detailCountTotal',
    ];

    public $layout = 'layout';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * top画面actionメソッド
     * @return string
     */
    public function actionIndex()
    {
        //GoogleAnalyticsのデータをaccess_logテーブルに登録する
        $googleAnalytics = Yii::$app->googleAnalytics;
        $googleAnalytics->saveGoogleAnalytics();

        // 各種インスタンスを用意
        $applicationMaster = new ApplicationMaster();
        $accessCount = new AccessCount();

        return $this->render('index', [
            'applicationMaster' => $applicationMaster,
            'accessCount' => $accessCount,
        ]);
    }
}
