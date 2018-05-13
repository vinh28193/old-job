<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

use yii\test\ActiveFixture;

class TenantFixture extends JmFixture
{
    public $modelClass = 'proseeds\models\Tenant';
    public $depends = [];

}