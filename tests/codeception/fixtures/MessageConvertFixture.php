<?php
namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

/**
 * Class MessageConvertFixture
 * @package tests\codeception\fixtures
 */
class MessageConvertFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 1;
    /**
     * @var string
     */
    public $modelClass = 'app\models\manage\MessageConvert';
    /**
     * @var array
     */
    public $depends = ['tests\codeception\fixtures\TenantFixture'];
}