<?php

namespace tests\codeception\fixtures;

use tests\codeception\JmFixture;

class InquiryColumnSubsetFixture extends JmFixture
{
    const RECORDS_PER_TENANT = 20;
    public $modelClass = 'app\models\manage\InquiryColumnSubset';
    public $depends = [];
}
