<?php

namespace app\modules\manage\controllers\secure;

use yii;

/**
 *
 */
class OptionInquiryController extends OptionBaseController
{
    public function init()
    {
        $this->functionItemSetMenu = 'inquiry';
        return parent::init();
    }
}
