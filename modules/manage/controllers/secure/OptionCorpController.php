<?php

namespace app\modules\manage\controllers\secure;

use yii;

/**
 * 管理画面->項目管理ｰ>代理店項目設定->一覧画面　コントローラー
 */
class OptionCorpController extends OptionBaseController
{
    public function init()
    {
        $this->functionItemSetMenu =  'corp';    // TODO:サービスロケータを経由するか検討する
        return parent::init(); // TODO: Change the autogenerated stub
    }
}