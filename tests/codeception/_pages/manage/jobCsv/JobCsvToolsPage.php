<?php

namespace tests\codeception\_pages\manage\jobCsv;

use tests\codeception\_pages\manage\BaseGridPage;
/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class JobCsvToolsPage extends BaseGridPage
{
    public $route = 'manage/secure/job-csv/index';

    /**
     * 元の原稿CSV一括登録のwindowを開く処理（window.nameが取得できないため下記処理にしている）
     */
    public function openDefaultWindow()
    {
        $this->actor->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) {
            $handles = $webDriver->getWindowHandles();
            $firstWindow = reset($handles); // 原稿CSV一括登録のwindow
            $webDriver->switchTo()->window($firstWindow);
        });
    }
}
