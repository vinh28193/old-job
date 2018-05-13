<?php

namespace tests\codeception\_pages\manage;

/**
 * todo 各RegisterPageで共通化できそうなものを書いていく
 * Represents contact page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class BaseRegisterPage extends BaseGridPage
{
    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return parent::__get($name);
    }
}
