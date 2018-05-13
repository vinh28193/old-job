<?php
namespace models\manage;

use app\models\bases\BaseMediaUpload;
use app\models\queries\MediaUploadQuery;
use tests\codeception\unit\JmTestCase;
use Yii;

/**
 * Class BaseMediaUploadTest
 * @package models\manage
 */
class BaseMediaUploadTest extends JmTestCase
{
    // tableNameとattributeLabelsは非常に単純なメソッドなので省略

    /**
     * findのテスト
     */
    public function testFind()
    {
        $q = BaseMediaUpload::find();
        verify($q)->isInstanceOf(MediaUploadQuery::className());
    }

    // getAdminMasterとgetClientMasterはrelationのため省略

    /**
     * orderTagsのテスト
     */
    public function testOrderTags()
    {
        $list = [
            'test' => 'test',
            '345' => '345',
            '(-A-)' => '(-A-)',
            'aaa' => 'aaa',
            '012' => '012',
        ];
        verify(static::method(new BaseMediaUpload(), 'orderTags', [$list]))->equals([
            '' => Yii::t('app', 'すべて'),
            0 => Yii::t('app', 'タグ無し'),
            '(-A-)' => '(-A-)',
            '012' => '012',
            '345' => '345',
            'aaa' => 'aaa',
            'test' => 'test',
        ]);
    }
}
