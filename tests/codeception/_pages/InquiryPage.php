<?php
namespace tests\codeception\_pages;

use yii\codeception\BasePage;
use yii\helpers\Inflector;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class InquiryPage extends BasePage
{
    public $route = 'inquiry/index';
}

