<?php
namespace models\manage;

use app\models\UserSession;
use tests\codeception\unit\JmTestCase;

class UserSessionTest extends JmTestCase
{
    public function testAttributeLabels()
    {
        $sessions = new UserSession();
        verify($sessions->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
        $sessions = new UserSession();
        verify($sessions->rules())->notEmpty();
    }

}