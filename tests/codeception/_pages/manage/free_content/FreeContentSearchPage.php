<?php

namespace tests\codeception\_pages\manage\free_content;

use tests\codeception\_pages\manage\BaseGridPage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class FreeContentSearchPage extends BaseGridPage
{
    /**
     * @var string
     */
    public $route = 'manage/secure/free-content/list';
}
