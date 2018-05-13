<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2018/01/04
 * Time: 15:09
 */
namespace test\common\traits;

use app\commands\components\Uploader;
use app\common\traits\UploadTrait;
use app\models\manage\CustomField;
use app\models\manage\HeaderFooterSetting;
use app\models\manage\MediaUpload;
use app\models\manage\WidgetData;
use proseeds\base\Tenant;
use tests\codeception\unit\JmTestCase;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class UploadTraitTest
 * @package test\common\traits
 */
class UploadTraitTest extends JmTestCase
{
    /**
     * uploadInitのtest
     */
    public function testUploadInit()
    {
        /** @var Tenant $tenant */
        $tenant = Yii::$app->tenant;
        $model = new MediaUpload();
        $model->uploadInit('path', true);
        verify($model->isPublic)->true();
        verify($model->tempFilePath)->equals("{$tenant->tenantCode}/tmp/path");
        verify($model->filePath)->equals("{$tenant->tenantCode}/path");
        verify($model->addPath)->equals('path');
    }

    /**
     * saveFilesとdeleteFileのtest
     * deleteOldFileとafterFindも使われているので
     * このtestをもって担保する
     */
    public function testSaveAndDeleteFiles()
    {
        $model = new MediaUpload();
        $this->loadFilePost($model->formName(), 'imageFile');
        $this->verifySaveAndDeleteFiles($model, 'save_file_name', MediaUpload::DIR_PATH);

        $model = new WidgetData();
        $this->loadFilePost($model->formName(), 'pict');
        $this->verifySaveAndDeleteFiles($model, 'pict', WidgetData::DIR_PATH);

        $model = new CustomField();
        $this->loadFilePost($model->formName(), 'pict');
        $this->verifySaveAndDeleteFiles($model, 'pict', CustomField::DIR_PATH);

        $model = new HeaderFooterSetting();
        $this->loadFilePost($model->formName(), 'imageFile');
        $this->verifySaveAndDeleteFiles($model, 'logo_file_name', HeaderFooterSetting::DIR_PATH);

        // todo CustomField::isUsedPictが改善されたら削除
        static::getFixtureInstance(MediaUpload::tableName())->initTable();
        static::getFixtureInstance(WidgetData::tableName())->initTable();
        static::getFixtureInstance(CustomField::tableName())->initTable();
        static::getFixtureInstance(HeaderFooterSetting::tableName())->initTable();
    }

    /**
     * @param ActiveRecord $class
     * @param string $attribute 実ファイル名が格納されているattribute
     * @param string $path
     */
    protected function verifySaveAndDeleteFiles(ActiveRecord $class, string $attribute, string $path)
    {
        $uploader = new Uploader();
        $uploader->dirPath = $path;
        // 更新用に適当なインスタンスを取得
        /** @var ActiveRecord $model */
        $model = $class::find()->where([
            'and',
            ['not', [$attribute => '']],
            ['not', [$attribute => null]],
        ])->one();
        // 旧ファイル名を保持
        $oldFileName = $model->$attribute;
        $this->ensureFile($uploader, $model, $attribute);
        // ファイル名を更新してファイルをsave
        $model->$attribute = 'test.png';
        $model->save();// todo CustomField::isUsedPictが改善されたら削除
        /** @var UploadTrait $model */
        $model->saveFiles();
        // 新しいファイルが保存されていて、古いファイルは消えている
        verify($uploader->hasStorage('test.png'))->true();
        verify($uploader->hasStorage($oldFileName))->false();
        // ファイルを削除
        $model->deleteFile();
        // ファイルが消えている
        verify($uploader->hasStorage('test.png'))->false();
    }

    /**
     * レコードに対応するファイルが実在する状態を作るためにファイルを作ってアップロード
     * @param Uploader $uploader
     * @param ActiveRecord $model
     * @param string $attribute
     */
    public function ensureFile(Uploader $uploader, ActiveRecord $model, string $attribute)
    {
        $uploader->fileSystem->put($uploader->storageDir() . '/' . $model->$attribute, '');
    }

    /**
     * srcUrlのtest
     */
    public function testSrcUrl()
    {
        $model = new MediaUpload();
        $model->save_file_name = 'unit.test';
        verify($model->srcUrl())->equals('/systemdata/data/upload/unit.test?' . 'public=1');
    }

    // loadは非常に単純なメソッドなので省略


    /**
     * loadFileInfoのtest
     */
    public function testLoadFileInfo()
    {
        $model = new CustomField();
        $this->verifyLoadFileInfo($model, 'pict', 'pict');

        $model = new HeaderFooterSetting();
        $this->verifyLoadFileInfo($model, 'logo_file_name', 'imageFile');
    }

    /**
     * @param CustomField|HeaderFooterSetting $model
     * @param string $nameAttribute
     * @param string $fileAttribute
     */
    public function verifyLoadFileInfo($model, $nameAttribute, $fileAttribute)
    {
        $model->setOldAttribute($nameAttribute, 'old');
        // ファイルも削除フラグも無し
        $model->$nameAttribute = 'new';
        verify($model->loadFileInfo())->true();
        verify($model->$nameAttribute)->equals('old');
        // 削除フラグあり
        $model->$nameAttribute = 'new';
        $model->deleteFileFlg = true;
        verify($model->loadFileInfo())->true();
        verify($model->$nameAttribute)->equals('');
        // ファイルあり
        $model->$nameAttribute = 'new';
        $this->loadFilePost($model->formName(), $fileAttribute);
        verify($model->loadFileInfo())->true();
        verify($model->$nameAttribute)->contains((new \DateTime('NOW'))->format('Y-m-d'));
    }
}
