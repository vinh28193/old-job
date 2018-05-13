<?php

namespace models\manage;

use app\models\manage\SocialButton;
use tests\codeception\fixtures\SocialButtonFixture;
use tests\codeception\unit\JmTestCase;

class SocialButtonTest extends JmTestCase
{
    /**
     * ルールテスト
     */
    public function testRules()
    {
        $model = new SocialButton();
        verify($model->rules())->notEmpty();
    }

    /**
     * ラベルテスト
     */
    public function testAttributeLabels()
    {
        $model = new SocialButton();
        verify($model->attributeLabels())->notEmpty();
    }
}
