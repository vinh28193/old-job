<?php

namespace tests\codeception\_pages\manage\corp;

use tests\codeception\_pages\manage\BaseGridPage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class CorpSearchPage extends BaseGridPage
{
    public $route = 'manage/secure/corp/list';
}