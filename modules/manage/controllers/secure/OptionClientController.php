<?php

namespace app\modules\manage\controllers\secure;

use yii;

/**
 * 管理画面->項目管理ｰ>掲載企業項目設定->一覧画面　コントローラー
 */
class OptionClientController extends OptionBaseController
{
    public function init()
    {
        $this->functionItemSetMenu = 'client';
        return parent::init(); // TODO: Change the autogenerated stub
    }
}
