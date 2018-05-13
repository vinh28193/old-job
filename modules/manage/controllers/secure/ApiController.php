<?php

namespace app\modules\manage\controllers\secure;

use yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\manage\AccessLog;
use app\models\manage\AccessLogSearch;
use app\modules\manage\models\Manager;
use app\modules\manage\controllers\CommonController;

/**
 * API用コントローラ
 */
class ApiController extends CommonController
{
    /**
     * select2アクセスURL ajax
     * @param string $q 検索文字列
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAccessUrlListSearch($q = null)
    {
         Yii::$app->response->format = Response::FORMAT_JSON;

         $data = AccessLog::authFind()->select('access_url')
                                   ->distinct(true)
                                   ->andFilterWhere(['like', 'access_url', addcslashes($q, '_%')])
                                   ->all();
         $data = ArrayHelper::getColumn($data, function ($v) {
             return ['id' => $v->access_url, 'text' => $v->access_url];
         });
         $out['results'] = $data;
         return $out;
        
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * autocomplete アクセスユーザーエージェント ajax
     * @param string $q 検索文字列
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAccessUserAgentListSearch($q = null)
    {
         Yii::$app->response->format = Response::FORMAT_JSON;

         $data = AccessLogSearch::getAutoCompleteList('access_user_agent', $q);

         $out['results'] = $data;
         return $out;
        
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * autocomplete アクセスリファラー ajax
     * @param string $q 検索文字列
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAccessReferrerListSearch($q = null)
    {
         Yii::$app->response->format = Response::FORMAT_JSON;

         $data = AccessLogSearch::getAutoCompleteList('access_referrer', $q);

         $out['results'] = $data;
         return $out;
        
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
