<?php

namespace tests\codeception\_pages\manage\widget;

use app\models\manage\Widget;
use tests\codeception\_pages\manage\BaseRegisterPage;
use yii\helpers\BaseInflector;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class WidgetPage extends BaseRegisterPage
{
    public $route = 'manage/secure/widget/index';

    /**
     * inputに入力して入力内容をキャッシュする
     * @param $attribute
     * @param $value
     */
    public function fillInputAndRemember($attribute, $value)
    {
        $this->actor->fillField("//input[@name='Widget[{$attribute}]']", $value);
        $this->attributes[$attribute] = $value;
    }

    /**
     * radioをチェックして入力内容をキャッシュする
     * @param $attribute
     * @param $value
     */
    public function fillRadioAndRemember($attribute, $value)
    {
        $this->actor->selectOption("//input[@name='Widget[{$attribute}]']", $value);
        $this->attributes[$attribute] = $value;
    }

    /**
     * @param $value
     */
    public function selectDropdownAndRemember($attribute, $value)
    {
        $this->actor->selectOption("//select[@name='Widget[{$attribute}]']", $value);
        $this->attributes[$attribute] = $value;
    }

    /**
     * @param string $attribute
     * @param string|null $errorMessage
     */
    public function checkValidate($attribute, $errorMessage = null)
    {
        if ($errorMessage) {
            $this->actor->wait(2); //should wait 1 sec for javascript response
            $this->actor->see($errorMessage);
            $resultClass = 'has-error';
        } else {
            $resultClass = 'has-success';
        }
        $this->actor->wait(1);
        $this->actor->seeElement("td>.field-widget-{$attribute}.{$resultClass}");
        $this->actor->seeElement("th>.field-widget-{$attribute}.{$resultClass}");
    }

    /**
     * クリアキャッシュ
     */
    public function clearCache()
    {
        foreach ($this->attributes as $data) {
            unset($data);
        }
    }

    /**
     * @param Widget $form
     * @param int $layout
     * @param int $position
     */
    public function moveWidget($form, $layout, $position = null)
    {
        if ($layout == null) {
            $target = '//ul[@id=\'layout-sortable\']';
        } else {
            $target = "//ul[@id=\"layout{$layout}-sortable\"]";
        }
        if ($position != null) {
            $target .= "/li[{$position}]";
        }
        $this->actor->dragAndDrop("//*[@id='item-{$form->id}']", $target);
    }

    /**
     * @param Widget $form
     * @param int $layout
     * @param int $position
     */
    public function seeWidget($form, $layout, $position = null)
    {
        if ($position) {
            $this->actor->seeElement("//ul[@id=\"layout{$layout}-sortable\"]/li[{$position}]/div/div/p[@id=\"item-{$form->id}\"]");
        } else {
            $this->actor->seeElement("#layout{$layout}-sortable #item-{$form->id}");
        }
    }

    /**
     * @param string $inputType
     * @param string $attribute
     */
    public function checkOptionSelected($inputType, $attribute)
    {
        $method = 'get' . BaseInflector::camelize($attribute) . 'Labels';
        $this->actor->seeOptionIsSelected("//{$inputType}[@name='Widget[{$attribute}]']", Widget::$method()[$this->attributes[$attribute]]);
    }

    /**
     * @param Widget $widget
     */
    public function clickSettingWidget($widget)
    {
        $this->actor->click("//li[@data-key='{$widget->id}']/div/div/div/a");
    }
}
