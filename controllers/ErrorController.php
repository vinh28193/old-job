<?php

namespace app\controllers;

use app\common\controllers\CommonController;

class ErrorController extends CommonController
{
    // 404 errorの処理
    public function actionIndex()
    {
        return $this->render('/error/index', []);
    }
}