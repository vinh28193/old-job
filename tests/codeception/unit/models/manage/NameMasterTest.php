<?php

namespace models\manage;

use app\models\manage\NameMaster;
use tests\codeception\unit\fixtures\NameMasterFixture;
use tests\codeception\unit\JmTestCase;

/**
 * @group job_relations
 */
class NameMasterTest extends JmTestCase
{
    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new NameMaster();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $model = new NameMaster();
        verify(is_array($model->rules()))->true();
    }

    /**
     * 変更後名の取得テスト
     */
    public function testGetChangeName()
    {
        verify(NameMaster::getChangeName('応募'))->equals('応募');
    }
}
