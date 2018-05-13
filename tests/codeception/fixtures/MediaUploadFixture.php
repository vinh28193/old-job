<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

/**
 * Class MediaUploadFixture
 * @package tests\codeception\fixtures
 */
class MediaUploadFixture extends JmFixture
{
    /** @var string */
    public $modelClass = 'app\models\manage\MediaUpload';
    /** @var array */
    public $depends = [];
}
