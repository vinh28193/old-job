<?php

namespace tests\codeception\_pages\manage\client;

use tests\codeception\_pages\manage\BaseGridPage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ClientSearchPage extends BaseGridPage
{
    public $route = 'manage/secure/client/list';
}