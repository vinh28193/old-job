<?php

namespace models\manage;

use app\models\ApplyAuth;
use tests\codeception\unit\JmTestCase;

/**
 * @group job_relations
 */
class ApplicationAuthTest extends JmTestCase
{
     /**
     * フィクスチャロード
     */
    public function setUp()
    {
        parent::setUp();
    }
    
    /**
     * ルールテスト
     */
    public function testRules()
    {
        $model = new ApplyAuth();
        verify(is_array($model->rules()))->true();
    }

    /**
     * ラベルテスト
     */
    public function testAttributeLabels()
    {
        $model = new ApplyAuth();
        verify(is_array($model->attributeLabels()))->true();
    }
    
    /**
     * フォーム名テスト
     */
    public function testFormName()
    {
        $model = new ApplyAuth();
        verify(is_string($model->formName()))->true();
    }
}
