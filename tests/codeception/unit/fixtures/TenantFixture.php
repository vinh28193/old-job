<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class TenantFixture extends JmFixture
{
    public $modelClass = 'proseeds\models\Tenant';
    public $depends = [];

}