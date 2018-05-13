<?php

namespace app\controllers;

use app\common\Keep;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * 求職者API用コントローラ
 */
class ApiController extends Controller
{
    /**
     * POST
     * @var array
     */
    public $post;

    /**
     * 初期化
     */
    public function init()
    {
        $this->post = Yii::$app->request->post();
        $this->enableCsrfValidation = false;
    }

    /**
     * @return array
     */
    public function actionDayList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!isset($this->post['depdrop_all_params']['apply-birthdateyear']{0}) || !isset($this->post['depdrop_all_params']['apply-birthdatemonth']{0})) {
            return ['output' => [], 'selected' => ''];
        }
        //指定した年月の初日を設定
        $date = $this->post['depdrop_all_params']['apply-birthdateyear'] . '-' . $this->post['depdrop_all_params']['apply-birthdatemonth'] . '-01';
        //末日を設定
        $endDate = date('t', strtotime($date));

        $dateList = [];
        for ($i = 1; $i <= $endDate; $i++) {
            $day = str_pad($i, 2, 0, STR_PAD_LEFT);
            $dateList[$i]['id'] = $day;
            $dateList[$i]['name'] = $day;
        }
        $selected = isset($this->post['depdrop_all_params']['apply-birthdateday']) ? $this->post['depdrop_all_params']['apply-birthdateday'] : null;
        if (is_null($selected) || $selected == Yii::t('app', '----')) {
            $selected = '';
        }

        return ['output' => $dateList, 'selected' => $selected];
    }

    /**
     * キープリストに求人IDを追加
     * @param string $jobNo 求人ID
     * @return array []
     */
    public function actionAddKeep($jobNo = null)
    {
        // JSON で返す
        Yii::$app->response->format = Response::FORMAT_JSON;

        $keepComp = new Keep();
        if (!$keepComp->addJobId($jobNo)) {
            $response = ['result' => false, 'msg' => $keepComp->errorMessage];
        } else {
            $response = ['result' => true, 'keepCount' => count($keepComp->keepJobNos)];
        }

        return $response;

    }

    /**
     * キープリストから求人IDを削除
     * @param string $jobNo 求人ID
     * @return array []
     */
    public function actionRemoveKeep($jobNo = null)
    {
        // JSON で返す
        Yii::$app->response->format = Response::FORMAT_JSON;

        $keepComp = new Keep();
        if (!$keepComp->removeKeepJobId($jobNo)) {
            $response = ['result' => false, 'msg' => $keepComp->errorMessage];
        } else {
            $response = ['result' => true, 'keepCount' => count($keepComp->keepJobNos)];
        }

        return $response;
    }
}
