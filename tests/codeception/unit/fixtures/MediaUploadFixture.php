<?php
namespace tests\codeception\unit\fixtures;

use tests\codeception\JmFixture;

class MediaUploadFixture extends JmFixture
{
    public $modelClass = 'app\models\manage\MediaUpload';
    public $depends = [];

}