<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2018/01/04
 * Time: 15:09
 */
namespace test\common\traits;

use app\commands\components\Uploader;
use app\common\traits\FileDeleteTrait;
use app\common\traits\UploadTrait;
use app\models\manage\CustomField;
use app\models\manage\CustomFieldSearch;
use app\models\manage\MediaUpload;
use app\models\manage\MediaUploadSearch;
use app\models\manage\WidgetData;
use app\models\manage\WidgetDataSearch;
use tests\codeception\unit\JmTestCase;
use yii\db\ActiveRecord;

/**
 * Class FileDeleteTraitTest
 * @package test\common\traits
 */
class FileDeleteTraitTest extends JmTestCase
{
    /**
     * deleteRecordsとdeleteFilesをまとめてtest
     */
    public function testDeleteMethods()
    {
        $this->verifyDeleteMethods(new MediaUpload(), new MediaUploadSearch(), 'save_file_name', MediaUpload::DIR_PATH);
        $this->verifyDeleteMethods(new WidgetData(), new WidgetDataSearch(), 'pict', WidgetData::DIR_PATH);
        $this->verifyDeleteMethods(new CustomField(), new CustomFieldSearch(), 'pict', CustomField::DIR_PATH);

        // todo CustomField::isUsedPictが改善されたら削除
        static::getFixtureInstance(MediaUpload::tableName())->initTable();
        static::getFixtureInstance(WidgetData::tableName())->initTable();
        static::getFixtureInstance(CustomField::tableName())->initTable();
    }

    /**
     * 削除を検証する
     * @param ActiveRecord $class
     * @param ActiveRecord $searchClass
     * @param string $attribute
     * @param string $path
     */
    protected function verifyDeleteMethods(ActiveRecord $class, ActiveRecord $searchClass, string $attribute, string $path)
    {
        $uploader = new Uploader();
        $uploader->dirPath = $path;
        /** @var UploadTrait[] $models */
        $models = $class::find()->where([
            'and',
            ['not', [$attribute => '']],
            ['not', [$attribute => null]],
        ])->limit(5)->all();

        // テストデータに対応するファイルを作ってアップロード
        foreach ($models as $model) {
            $uploader->fileSystem->put($uploader->storageDir() . '/' . $model->$attribute, '');
            verify($uploader->hasStorage($model->$attribute))->true();
        }

        // レコード削除 todo CustomField::isUsedPictが改善されたら分ける
        /** @var FileDeleteTrait $searchClass */
        /** @var ActiveRecord[] $models */
        $searchClass::deleteRecords($models);
        // レコードが消えているか検証
        foreach ($models as $model) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->tester->cantSeeInDatabase($class::tableName(), ['id' => $model->id]);
        }

        // ファイル削除
        /** @var UploadTrait[] $models */
        $searchClass::deleteFiles($models);
        // ファイルが消えているか検証
        foreach ($models as $model) {
            verify($uploader->hasStorage($model->$attribute))->false();
        }
    }
}
