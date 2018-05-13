<?php

namespace app\controllers;

use app\common\controllers\CommonController;
use app\models\FreeContent;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class ContentsController
 * @package app\controllers
 */
class ContentsController extends CommonController
{
    /**
     * @param $urlDirectory
     * @return string
     */
    public function actionIndex($urlDirectory)
    {
        $model = $this->findModel($urlDirectory);
        return $this->render('index', ['model' => $model]);
    }

    /**
     * @param $urlDirectory
     * @return FreeContent
     * @throws NotFoundHttpException
     */
    protected function findModel($urlDirectory)
    {
        /** @var FreeContent $model */
        if (($model = FreeContent::findOne(['url_directory' => $urlDirectory, 'valid_chk' => FreeContent::VALID])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
