<?php

namespace tests\codeception\_pages\manage;

use yii\codeception\BasePage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ManageLoginPage extends BasePage
{
    public $route = '/manage/login';

    private $menus = [
        'owner' => [
            1,
            2,
            3,
            4,
            8,
            9,
            6,
            7,
            10,
            5,
            11,
            12,
        ],
        'agency' => [
            1,
            2,
            8,
            9,
            6,
            10,
        ],
        'client' => [
            1,
            2,
            8,
            9,
            6,
            10,
        ],
    ];

    /**
     * @param $loginId
     * @param $password
     */
    public function login($loginId, $password)
    {
        $this->actor->fillField('//*[@id="loginid"]', $loginId);
        $this->actor->fillField('//*[@id="password"]', $password);
        $this->actor->click('login-button');
    }

    /**
     * ログアウトリンククリック
     */
    public function logout()
    {
        $this->actor->click('//*[@id="bs-example-navbar-collapse-1"]/ul/li[2]/a');
        if (method_exists($this->actor, 'wait')) {
            $this->actor->wait(2);
        }
        $this->actor->click('//*[@id="bs-example-navbar-collapse-1"]/ul/li[2]/ul/li/a[@href="/manage/logout"]');
        if (method_exists($this->actor, 'wait')) {
            $this->actor->wait(2);
        }
    }

    /**
     * ホーム画面上でのログアウトクリック
     *
     * @return void
     * @access public
     */
    public function logoutOnHome()
    {
        $this->logout();
    }

    /**
     * @return mixed
     */
    public function getOwnerMenus()
    {
        return $this->menus['owner'];
    }

    /**
     * @return mixed
     */
    public function getAgencyMenus()
    {
        return $this->menus['agency'];
    }

    /**
     * @return mixed
     */
    public function getClientMenus()
    {
        return $this->menus['client'];
    }

    /**
     * 新規タブに移動
     */
    public function switchNewTab()
    {
        $this->actor->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) {
            $handles = $webDriver->getWindowHandles();
            reset($handles);
            $nextWindow = next($handles);
            $webDriver->switchTo()->window($nextWindow);
        });
    }

    /**
     * 元のタブに移動
     */
    public function switchOriginTab()
    {
        $this->actor->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) {
            $handles = $webDriver->getWindowHandles();
            $resetWindow = reset($handles);
            $webDriver->switchTo()->window($resetWindow);
        });
    }
}
