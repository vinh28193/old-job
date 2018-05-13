<?php
namespace app\commands\components;

use app\common\Helper\JmUtils;
use creocoder\flysystem\Filesystem;
use creocoder\flysystem\LocalFilesystem;
use proseeds\base\Tenant;
use Yii;
use yii\base\Component;
use yii\console\Exception;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/12/27
 * Time: 17:27
 *
 * @property string $dirPath
 */
class Uploader extends Component
{
    /** @var Tenant $tenant */
    public $tenant;
    /** @var Filesystem $fileSystem */
    public $fileSystem;
    /** @var LocalFilesystem $localFileSystem */
    public $localFileSystem;
    /**@var string $_dirPath */
    private $_dirPath;
    /**@var string $_tmpRoot ローカルでファイルが置かれる場所 */
    private $_tmpRoot;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->tenant = Yii::$app->tenant;
        $this->fileSystem = Yii::$app->publicFs;
        $this->_tmpRoot = Yii::$app->basePath . '/web/tmp';
        $this->localFileSystem = Yii::createObject(['class' => LocalFilesystem::className(), 'path' => $this->_tmpRoot]);
    }

    /**
     * localからstorageにアップロードする
     * @param $fileName
     * @throws Exception
     */
    public function uploadFile(string $fileName)
    {
        $stream = $this->localFileSystem->readStream($this->localDir() . '/' . $fileName);
        if (!$this->fileSystem->writeStream($this->storageDir() . '/' . $fileName, $stream)) {
            throw new Exception("{$fileName} was not uploaded in " . $this->tenant->tenantCode);
        }
    }

    /**
     * @return string
     */
    public function storageDir(): string
    {
        return $this->tenant->tenantCode . '/' . $this->dirPath;
    }

    /**
     * @return string
     */
    public function localDir(): string
    {
        return $this->dirPath;
    }

    /**
     * ローカルのフルパスを返す
     * @return string
     */
    public function fullPathOfLocalDir(): string
    {
        return $this->_tmpRoot . '/' . $this->dirPath;
    }

    /**
     * オブジェクトストレージのファイルを削除する
     * 2018/01現在テストでしか使っていない（実装側でも使うようになったらこのコメント削除してください）
     * @param $fileName
     * @param bool $must 削除できなかった際例外にするかどうか
     * @throws Exception
     */
    public function deleteStorageFile(string $fileName, bool $must = true)
    {
        if (!$this->fileSystem->delete($this->storageDir() . '/' . $fileName) && $must) {
            throw new Exception("{$fileName} file on storage could not be deleted in " . $this->tenant->tenantCode);
        }
    }

    /**
     * ローカルのファイルを削除する
     * 2018/01現在テストでしか使っていない（実装側でも使うようになったらこのコメント削除してください）
     * @param $fileName
     * @param bool $must 削除できなかった際例外にするかどうか
     * @throws Exception
     */
    public function deleteLocalFile(string $fileName, bool $must = true)
    {
        if (!$this->localFileSystem->delete($this->localDir() . '/' . $fileName) && $must) {
            throw new Exception("{$fileName} file on local could not be deleted in " . $this->tenant->tenantCode);
        }
    }

    /**
     * オブジェクトストレージのディレクトリを削除する
     * @param bool $must 削除できなかった際例外にするかどうか
     * @throws Exception
     */
    public function deleteStorageDir(bool $must = true)
    {
        $dirName = $this->storageDir();
        if (!$this->fileSystem->deleteDir($this->storageDir()) && $must) {
            throw new Exception("{$dirName} directory on storage could not be deleted in " . $this->tenant->tenantCode);
        }
    }

    /**
     * ローカルのディレクトリを削除する
     * @param bool $must 削除できなかった際例外にするかどうか
     * @throws Exception
     */
    public function deleteLocalDir(bool $must = true)
    {
        $dirName = $this->localDir();
        if (!$this->localFileSystem->deleteDir($dirName) && $must) {
            throw new Exception("{$dirName} directory on local could not be deleted in " . $this->tenant->tenantCode);
        }
    }

    /**
     * ローカルのディレクトリを作成する
     */
    public function createLocalDir()
    {
        $this->localFileSystem->createDir($this->localDir());
    }

    /**
     * dirPathのsetter
     * 一度入れたら上書き禁止
     * @param string $v
     * @throws Exception
     */
    public function setDirPath(string $v)
    {
        if ($this->_dirPath !== null) {
            throw new Exception('property _direPath is already exists');
        }
        $this->_dirPath = $v;
    }

    /**
     * dirPathのgetter
     * @return string
     * @throws Exception
     */
    public function getDirPath():string
    {
        if ($this->_dirPath === null) {
            throw new Exception('property _direPath should be not null');
        }
        return $this->_dirPath;
    }

    /**
     * オブジェクトストレージのファイル及びディレクトリの存在チェック
     * 2018/01現在テストでしか使っていない（実装側でも使うようになったらこのコメント削除してください）
     * @param string $name
     * @return bool
     */
    public function hasStorage(string $name):bool
    {
        return $this->fileSystem->has($this->storageDir() . '/' . $name);
    }

    /**
     * ローカルのファイル及びディレクトリの存在チェック
     * 2018/01現在テストでしか使っていない（実装側でも使うようになったらこのコメント削除してください）
     * @param string $name
     * @return bool
     */
    public function hasLocal(string $name):bool
    {
        return $this->localFileSystem->has($this->localDir() . '/' . $name);
    }

    /**
     * ストレージ内のファイル名のlistを作る
     * ディレクトリ名も降ってくるので注意
     * @param $path
     * @return array
     */
    public function storageBaseNameList($path = '')
    {
        $listContents = $this->fileSystem->listContents($this->storageDir() . '/' . $path);
        return ArrayHelper::getColumn($listContents, 'basename');
    }

    /**
     * @return string
     */
    public function urlDirPath():string
    {
        return JmUtils::fileUrl($this->dirPath);
    }
}
