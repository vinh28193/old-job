<?php

namespace tests\codeception\_pages\manage\application;

use tests\codeception\_pages\manage\BaseGridPage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ApplicationSearchPage extends BaseGridPage
{
    public $route = 'manage/secure/application/list';
}
?>