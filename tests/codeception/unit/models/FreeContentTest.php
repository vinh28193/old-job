<?php
namespace tests\models;

use app\models\FreeContent;
use app\models\queries\FreeContentQuery;
use tests\codeception\unit\JmTestCase;

/**
 * Class FreeContentTest
 * @package tests\models\manage
 */
class FreeContentTest extends JmTestCase
{
    /**
     * rulesのtest
     */
    public function testRules()
    {
        static::getFixtureInstance('free_content')->initTable();
        static::getFixtureInstance('free_content_element')->initTable();
        $this->specify('必須チェック', function () {
            $model = new FreeContent;
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('url_directory'))->true();
        });

        $this->specify('boolチェック', function () {
            $model = new FreeContent;
            $model->valid_chk = 10;
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('stringチェック', function () {
            $model = new FreeContent;
            $model->title = 10;
            $model->keyword = [1, 2, 3];
            $model->description = new \stdClass();
            $model->url_directory = 10.52;
            $model->validate();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('keyword'))->true();
            verify($model->hasErrors('description'))->true();
            verify($model->hasErrors('url_directory'))->true();

            $model->title = str_repeat('a', 256);
            $model->keyword = str_repeat('b', 256);
            $model->description = str_repeat('c', 256);
            $model->url_directory = str_repeat('d', 31);
            $model->validate();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('keyword'))->true();
            verify($model->hasErrors('description'))->true();
            verify($model->hasErrors('url_directory'))->true();
        });

        $this->specify('uniqueチェック', function () {
            /** @var FreeContent $model */
            $model = FreeContent::find()->one();
            $model->validate();
            verify($model->hasErrors('url_directory'))->false();

            $model->setIsNewRecord(true);
            $model->validate();
            verify($model->hasErrors('url_directory'))->true();
        });

        $this->specify('url_directory形式チェック', function () {
            $model = new FreeContent;
            $model->url_directory = 'te/st';
            $model->validate();
            verify($model->hasErrors('url_directory'))->true();
        });

        $this->specify('正常値', function () {
            $model = new FreeContent;
            $model->valid_chk = 1;
            $model->title = str_repeat('a', 255);
            $model->keyword = str_repeat('b', 255);
            $model->description = str_repeat('c', 255);
            $model->url_directory = str_repeat('d', 30);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->false();
            verify($model->hasErrors('title'))->false();
            verify($model->hasErrors('keyword'))->false();
            verify($model->hasErrors('description'))->false();
            verify($model->hasErrors('url_directory'))->false();
        });
    }

    // attributeLabelsのtestは省略

    /**
     * findのtest
     */
    public function testFind()
    {
        verify(FreeContent::find())->isInstanceOf(FreeContentQuery::className());
    }

    // validArrayとgetElementsのtestは省略
}
