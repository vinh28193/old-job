<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2018/01/04
 * Time: 15:09
 */
namespace test\commands\components;

use app\commands\components\Uploader;
use creocoder\flysystem\LocalFilesystem;
use Exception;
use tests\codeception\unit\JmTestCase;
use Yii;

/**
 * Class UploaderTest
 * @package app\commands\components
 */
class UploaderTest extends JmTestCase
{
    /**
     * initのtest
     */
    public function testInit()
    {
        $uploader = new Uploader();
        verify($uploader->tenant)->equals($this->tenant());
        verify($uploader->fileSystem)->equals(Yii::$app->publicFs);
        $tmpRoot = static::property($uploader, '_tmpRoot');
        verify($tmpRoot)->equals('/var/www/jm2/jobmaker/web/tmp');
        verify($uploader->localFileSystem)->isInstanceOf(LocalFilesystem::className());
        verify($uploader->localFileSystem->path)->equals($tmpRoot);
    }

    /**
     * uploadFileのtest
     * deleteStorageFileも同時にテスト
     */
    public function testUploadFile()
    {
        $uploader = $this->prepareUploader();
        $uploader->localFileSystem->put($uploader->localDir() . '/test', '');
        // testをアップロード
        $uploader->uploadFile('test');
        verify($uploader->hasStorage('test'))->true();
        // アップロードした.gitkeepを削除
        $uploader->deleteStorageFile('test', true);
        verify($uploader->hasStorage('test'))->false();
    }

    /**
     * storageDirのtest
     */
    public function testStorageDir()
    {
        $uploader = $this->prepareUploader();
        verify($uploader->storageDir())->equals($this->tenant()->tenantCode . '/' . 'sitemap');
    }

    /**
     * localDirのtest
     */
    public function testLocalDir()
    {
        $uploader = $this->prepareUploader();
        verify($uploader->localDir())->equals('sitemap');
    }

    /**
     * fullPathOfLocalDirのtest
     */
    public function testFullPathOfLocalDir()
    {
        $uploader = $this->prepareUploader();
        verify($uploader->fullPathOfLocalDir())->equals('/var/www/jm2/jobmaker/web/tmp/sitemap');
    }

    /**
     * deleteLocalFileのtest
     * hasLocalも同時にtest
     */
    public function testDeleteLocalFile()
    {
        $uploader = $this->prepareUploader();
        // testファイルを置くと存在すると判定
        $uploader->localFileSystem->put($uploader->localDir() . '/test', '');
        verify($uploader->hasLocal('test'))->true();
        // 削除すると存在しないと判定
        $uploader->deleteLocalFile('test');
        verify($uploader->hasLocal('.gitkeep'))->false();
    }

    /**
     * deleteStorageDirのtest
     */
    public function testDeleteStorageDir()
    {
        $uploader = new Uploader();
        $uploader->dirPath = 'sitemap/test';
        // testディレクトリ作成
        $uploader->fileSystem->createDir($uploader->storageDir() . '/sitemap/test');
        // 作成したディレクトリを削除
        $uploader->deleteStorageDir();
        verify($uploader->fileSystem->has($uploader->storageDir() . '/sitemap/test'))->false();
    }

    /**
     * createLocalDirのtest
     * deleteLocalDirも同時にtest
     */
    public function testCreateLocalDir()
    {
        $uploader = new Uploader();
        $uploader->dirPath = 'sitemap/test';
        // testディレクトリ作成
        $uploader->createLocalDir();
        verify($uploader->localFileSystem->has('sitemap/test'))->true();
        // 作成したディレクトリを削除
        $uploader->deleteLocalDir();
        verify($uploader->localFileSystem->has('sitemap/test'))->false();
    }

    /**
     * dirPathのsetterとgetterとsetterの例外のtest
     */
    public function testSetAndGetDirPath()
    {
        $uploader = new Uploader();
        // getterとsetterのテスト
        $uploader->dirPath = 'unit/test';
        verify($uploader->dirPath)->equals('unit/test');
        // setter例外テスト
        $this->expectException(Exception::class);
        $uploader->dirPath = 'aaa';
    }

    /**
     * dirPathのgetterの例外test
     */
    public function testGetDirPathException()
    {
        $uploader = new Uploader();
        $this->expectException(Exception::class);
        $uploader->dirPath;
    }

    /**
     * hasStorageのtest
     */
    public function testHasStorage()
    {
        $uploader = $this->prepareUploader();
        // testファイルを置くと存在すると判定
        $uploader->fileSystem->put($uploader->storageDir() . '/test', '');
        verify($uploader->hasStorage('test'))->true();
        // 削除すると存在しないと判定
        $uploader->deleteStorageFile('test');
        verify($uploader->hasStorage('.gitkeep'))->false();
    }

    /**
     * storageBaseNameListのtest
     */
    public function testStorageBaseNameList()
    {
        $uploader = $this->prepareUploader();
        $uploader->deleteStorageDir();
        // 直下に置く
        $uploader->fileSystem->put($uploader->storageDir() . '/file1', '');
        $uploader->fileSystem->put($uploader->storageDir() . '/file2', '');
        verify($uploader->storageBaseNameList())->equals(['file1', 'file2']);

        $uploader->fileSystem->put($uploader->storageDir() . '/dir/file3', '');
        $uploader->fileSystem->put($uploader->storageDir() . '/dir/file4', '');
        verify($uploader->storageBaseNameList('dir'))->equals(['file3', 'file4']);

        $uploader->deleteStorageDir();
    }

    /**
     * urlDirPathのtest
     */
    public function testUrlDirPath()
    {
        $uploader = $this->prepareUploader();
        verify($uploader->urlDirPath())->equals('/systemdata/sitemap');
    }

    /**
     * @return Uploader
     */
    protected function prepareUploader()
    {
        $uploader = new Uploader();
        $uploader->dirPath = 'sitemap';
        return $uploader;
    }
}
