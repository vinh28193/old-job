<?php
/**
 * Created by PhpStorm.
 * User: KNakamoto
 * Date: 2016/02/18
 * Time: 9:31
 */

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * POSTされたファイルをアップロードするModel
 * 設定されたパスに基づいて、ファイルをアップロードする
 * @package app\models
 */
class FileUploader extends Model
{
    const DEST_PATH = '../web/data/csv/';   //LW3から変更
    const TEMP_PATH = '../web/data/csv/tmp/';   //LW3から変更

    public $file;
    public $extensions = [];
    public $uploadPath = self::DEST_PATH;
    public $uploadTempPath = self::TEMP_PATH;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                'file'
                ,'file'
                ,'extensions' => $this->extensions
                ,'skipOnEmpty' => false
                ,'checkExtensionByMimeType' => false
            ],
        ];
    }

    /**
     * 送信されたファイルをアップロード
     * @return bool|string
     * アップロード成功時: 保存したファイル名
     * アップロード失敗時: false
     */
    public function upload()
    {
        return self::uploadFile($this->file, $this->uploadPath);
    }

    /**
     * 送信されたファイルを一時フォルダにアップロード
     * @return bool|string
     * アップロード成功時: 保存したファイル名
     * アップロード失敗時: false
     */
    public function uploadToTemp()
    {
        return self::uploadFile($this->file, $this->uploadTempPath);
    }

    /**
     * 指定されたパスにファイルをアップロードする
     * @param UploadedFile|boolean $file
     * @param string $destPath アップロード先フォルダパス
     * @return bool|string アップロードしたファイルの名前
     */
    public static function uploadFile($file, $destPath = self::DEST_PATH)
    {
        if (!$file) return false;
        if (!file_exists($destPath)) {
            throw new \InvalidArgumentException;
        }

        $datetime = new \DateTime('NOW');
        $fileName = $datetime->format("Y-m-d") . '_' . md5(uniqid()) . '.' . $file->extension;
        if (!$file->saveAs($destPath . $fileName)) {
            return false;
        }

        return $fileName;
    }

    /**
     * UploadedFileインスタンスを取得する
     * @param string $name アップロードに使ったinputのname
     * @param Model $model モデルが指定されている場合は、モデルの属性からfileを取得する
     * @return bool|UploadedFile
     */
    public static function getInstance($name, $model = null)
    {
        if (isset($model)) {
            if (!($model->canGetProperty($name))) {
                throw new \InvalidArgumentException;
            }

            if (!$file = UploadedFile::getInstance($model, $name)) {
                return false;
            }
        } else {
            if (!$file = UploadedFile::getInstanceByName($name)) {
                return false;
            }
        }

        return $file;
    }

    /**
     * ファイルをtempフォルダへアップロードする
     * @param \yii\base\Model $model fileインスタンスを保存しているModel
     * @param string $attributeName fileのインスタンスを保存するプロパティの名前
     * @param string $tempFilePath tempフォルダのパス
     * @return bool|string アップロードしたファイルの名前
     */
    public static function uploadFileToTemp($model, $attributeName, $tempFilePath = self::TEMP_PATH)
    {
        return self::uploadFile(self::getInstance($attributeName, $model), $tempFilePath);
    }

    /**
     * ファイルを$this->filePathで指定されたパスへアップロードする
     * @param \yii\base\Model $model fileインスタンスを保存しているModel
     * @param string $attributeName fileのインスタンスを保存するプロパティの名前
     * @param string $destFilePath uploadフォルダのパス
     * @return bool|string アップロードしたファイルの名前
     */
    public static function uploadFileToDest($model, $attributeName, $destFilePath = self::DEST_PATH)
    {
        return self::uploadFile(self::getInstance($attributeName, $model), $destFilePath);
    }

    /**
     * ファイルを移動させる
     * @param string $fileName 移動させるファイル
     * @param string $sourcePath 移動元のパス
     * @param string $destPath 移動先のパス
     * @return string パスを含んだ移動後のファイル名
     */
    public static function moveFile($fileName, $sourcePath = self::TEMP_PATH, $destPath = self::DEST_PATH)
    {
        if (is_string($fileName) && strlen($fileName) > 0 && file_exists($sourcePath . $fileName)) {
            rename($sourcePath . $fileName, $destPath . $fileName);
            return $destPath . $fileName;
        } else {
            return "";
        }
    }

    /**
     * 添付フォルダに$fileNameが存在するかを確認する
     * @param $fileName
     * @param string $sourcePath
     * @return bool
     */
    public static function isTempFileExists($fileName, $sourcePath = self::TEMP_PATH)
    {
        return self::isFileExists($fileName, $sourcePath);
    }

    /**
     * @param $fileName
     * @param string $sourcePath
     * @return bool
     */
    public static function isFileExists($fileName, $sourcePath = self::DEST_PATH)
    {
        return file_exists($sourcePath . $fileName);
    }
}
