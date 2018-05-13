<?php

namespace app\modules\manage;

use Yii;

class Module extends \yii\base\Module
{
    use ReconfigureTrait;

    public $controllerNamespace = 'app\modules\manage\controllers';

    public function init()
    {
        parent::init();

        $this->reconfigure(require __DIR__ . '/config/manage.php');
    }
}
