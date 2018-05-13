<?php

namespace app\controllers;

use app\common\Keep;
use yii;
use app\common\controllers\CommonController;

/**
 * キープ求人コントローラ
 */
class KeepController extends CommonController
{

    /**
     * キープ一覧ページ
     * @return string
     */
    public function actionIndex()
    {
        $keepComp = new Keep();
        $dataProvider = $keepComp->getKeepJobs();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}