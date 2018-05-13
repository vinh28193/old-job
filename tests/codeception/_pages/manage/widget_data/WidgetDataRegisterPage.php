<?php

namespace tests\codeception\_pages\manage\widget_data;

use tests\codeception\_pages\manage\BaseRegisterPage;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class WidgetDataRegisterPage extends BaseRegisterPage
{
    public $route = 'manage/secure/widget-data/create';

    /**
     * @param string $attribute
     * @param string|null $errorMessage
     */
    public function checkValidate($attribute, $errorMessage)
    {
        if ($errorMessage) {
            $this->actor->wait(2); //should wait 1 sec for javascript response
            $this->actor->see($errorMessage);
            $resultClass = 'has-error';
        } else {
            $resultClass = 'has-success';
        }
        $this->actor->wait(1);
        $this->actor->seeElement("td>.field-widgetdata-{$attribute}.{$resultClass}");
        $this->actor->seeElement("th>.field-widgetdata-{$attribute}.{$resultClass}");
    }

    /**
     * URLに入力して入力内容をキャッシュする
     * @param int $areaId
     * @param string $value
     */
    public function fillUrlsAndRemember($areaId, $value)
    {
        $this->actor->fillField("//input[@name='WidgetData[urls][{$areaId}]']", $value);
        $this->attributes['urls'][$areaId] = $value;
    }

    /**
     * @param int $areaId
     * @param string|null $errorMessage
     * @param boolean|null $headerResult
     */
    public function checkValidateForUrls($areaId, $errorMessage, $headerResult = null)
    {
        if ($errorMessage) {
            $resultClass = 'has-error';
            $this->actor->wait(1);
            $this->actor->see($errorMessage, "#widgetdata-urls-{$areaId} ~ div.urlError");
        } else {
            $resultClass = 'has-success';
        }

        if ($headerResult === null) {
            $resultHeaderClass = $resultClass;
        } else {
            $resultHeaderClass = $headerResult ? 'has-success' : 'has-error';
        }
        $this->actor->wait(1);
        $this->actor->seeElement(".field-widgetdata-urls-{$areaId}.{$resultClass}");
        $this->actor->seeElement(".field-widgetdata-urls.{$resultHeaderClass}");
    }

    /**
     * inputに入力して入力内容をキャッシュする
     * @param string $attribute
     * @param string $value
     */
    public function fillInputAndRemember($attribute, $value)
    {
        $this->actor->fillField("//input[@name='WidgetData[{$attribute}]']", $value);
        $this->attributes[$attribute] = $value;
    }

    /**
     * radioをチェックして入力内容をキャッシュする
     * @param string $attribute
     * @param string $value
     */
    public function fillRadioAndRemember($attribute, $value)
    {
        $this->actor->selectOption("//input[@name='WidgetData[{$attribute}]']", $value);
        $this->attributes[$attribute] = $value;
    }
}
