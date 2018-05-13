<?php

namespace app\common\csv;


use app\common\ServerSentEventTrait;
use Yii;
use yii\base\Component;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * Class CsvWorker
 * @package app\common\csv
 * @property integer $sendId
 * CsvLoaderを使ってCSVファイルからモデルを生成し、検証、登録を行う
 */
class CsvWorker extends Component
{
    use ServerSentEventTrait;

    /** @var integer 最大 */
    const DEFAULT_ERROR_LIMIT_COUNT = 10;

    /**
     * @var string ファイル名
     */
    public $filename;

    /**
     * @var string 使用するCsvLoaderクラス
     */
    public $loaderClass;

    /**
     * @var \SplFileObject ファイルオブジェクト
     */
    private $file;

    /**
     * @var integer CSVの行数
     */
    private $totalCount;

    /**
     * @var string|callable 各モデルオブジェクトに対して適用する、save後処理のコーラブル
     * stringを指定すると各モデルのメソッドとなる
     * callableを指定すると第1引数が対象モデルとなる
     */
    public $afterSave;

    /**
     * @var string ファイル名
     */
    public $errorLimitCount = self::DEFAULT_ERROR_LIMIT_COUNT;

    /**
     * @var int ServerSentEventで使用するID
     */
    public $sendId;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->renderHeader();

        if (isset($this->filename)) {
            $this->file = $this->getFileObject();
            if (isset($this->file)) $this->totalCount = $this->getTotalCount();
        }
    }

    /**
     * 読み込んだ内容から、モデルを生成し検証する
     */
    public function validate()
    {
        if (is_null($this->file)) {
            $this->sendLoadingError(0, "loadingError");
            return false;
        }

        if ($this->totalCount == 0) {
            $this->sendEmptyError(0, "emptyError");
            return false;
        }

        ini_set('max_execution_time', 0);

        /** YiiのLoggerを無効化 */
        Yii::$app->log->targets = [];

        $this->sendId = 0;
        $errorCount = 0;

        ini_set('max_execution_time', 0);

        /** YiiのLoggerを無効化 */
        Yii::$app->log->targets = [];

        /** @var CsvLoader $loader */
        $loader = Yii::createObject(['class' => $this->loaderClass]);

        try {
            $loader->beforeCsvLoad($this);
            foreach ($this->file as $line) {
                if ($this->file->key() == 0) continue;
                if (empty($line) || is_null($line[0])) continue;

                $this->sendMessage($this->sendId++, json_encode([
                    'totalCount' => $this->totalCount,
                    'progress' => $this->sendId,
                ]));

                $model = $loader->getInstance($line);
                if ($model->hasErrors() || !$model->validate()) {
                    $errors = self::getErrorMessages($model, $this->file->key(), $loader);
                    $errorCount += count($errors);
                    $this->sendError($this->sendId, json_encode(['error' => $errors]));

                    if ($errorCount >= $this->errorLimitCount) {
                        $this->sendTooManyError($this->sendId, "tooManyError");
                        break;
                    }
                }
                $errors = null;
                $model = null;
                $line = null;
            }

            if ($errorCount > 0 || $loader->afterCsvLoad($this)) {
                $this->deleteCsvFile();
            }

            $this->sendComplete($this->sendId, "complete");
        } catch (Exception $e) {
            $this->sendExceptionError(0, Yii::t("app", "例外が発生しました"));
            throw $e;
        }

        return true;
    }

    /**
     * 読み込んだCSVの内容から、モデルを生成し保存する
     * @throws \Exception
     */
    public function save()
    {
        if (is_null($this->file)) {
            $this->sendLoadingError(0, "loadingError");
            return false;
        }

        if ($this->totalCount == 0) {
            $this->sendEmptyError(0, "emptyError");
            return false;
        }

        ini_set('max_execution_time', 0);

        /** YiiのLoggerを無効化 */
        Yii::$app->log->targets = [];

        Yii::$app->db->transaction(function () {
            $this->sendId = 0;
            /** @var CsvLoader $loader*/
            $loader = Yii::createObject(['class' => $this->loaderClass]);

            $loader->beforeCsvSave($this);
            foreach ($this->file as $line) {
                if ($this->file->key() == 0) continue;
                if (empty($line) || is_null($line[0])) continue;

                $this->sendMessage($this->sendId++, json_encode([
                    'totalCount' => $this->totalCount,
                    'progress' => $this->sendId,
                ]));

                $model = $loader->getInstance($line);
                if (!$model->hasErrors() && $model->validate()) {
                    $model->save(false);
                } else {
                    $errors = self::getErrorMessages($model, $this->file->key(), $loader);
                    $this->sendError($this->sendId, json_encode(['error' => $errors]));

                    $this->deleteCsvFile();

                    throw new ErrorException(Yii::t("app", "CSVによる登録に失敗しました。"));
                }
            }
            $loader->afterCsvSave($this);

            // 必要に応じ、各モデルで保存後処理を行う
            if ($this->afterSave) {
                $this->sendId = 0;
                foreach ($this->file as $line) {
                    if ($this->file->key() == 0) continue;
                    if (empty($line) || is_null($line[0])) continue;

                    $this->sendMessage($this->sendId++, json_encode([
                        'totalCount' => $this->totalCount,
                        'progress' => $this->sendId,
                        'message' => Yii::t('app', '登録後処理を行っています...'),
                    ]));

                    $model = $loader->getInstance($line);
                    if (is_string($this->afterSave)) {
                        $ret = call_user_func([$model, $this->afterSave], $model, $this);
                    } else {
                        $ret = call_user_func_array($this->afterSave, [$model, $this]);
                    }
                    if (!$ret) {
                        $errors = self::getErrorMessages($model, $this->file->key(), $loader);
                        $this->sendError($this->sendId, json_encode(['error' => $errors]));
                        throw new Exception("CSV登録後処理でエラーが発生しました");
                    }
                }
            }

            $this->deleteCsvFile();
            $this->sendComplete($this->sendId, "complete");
        });

        return true;
    }

    /**
     * 読み込んだCSVファイルを削除
     */
    public function deleteCsvFile()
    {
        if (isset($this->file)) {
            $filePath = $this->file->getPathName();
            $this->file = null;
            unlink($filePath);
        }
    }

    /**
     * ファイル名からSplFileObjectを取得
     * @return \SplFileObject
     */
    protected function getFileObject()
    {
        try {
            $data = file_get_contents(Yii::getAlias($this->filename));
            $data = mb_convert_encoding($data, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-win');
            file_put_contents(Yii::getAlias($this->filename), $data);
            $file = new \SplFileObject(Yii::getAlias($this->filename));
            $file->setFlags(\SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::READ_CSV);
        } catch (\RuntimeException $e) {
            return null;
        }

        return $file;
    }

    /**
     * CSVファイルの行数を取得
     * @return int CSVの行数
     */
    protected function getTotalCount()
    {
        $totalCount = 0;
        foreach ($this->file as $line) {
            if ($this->file->key() == 0) continue;
            if (empty($line) || is_null($line[0])) continue;
            $totalCount++;
        }
        $this->file->rewind();

        return $totalCount;
    }

    /**
     * @param ActiveRecord $model エラーを取得するモデル
     * @param integer $lineNumber エラーが発生したCSVの行
     * @param CsvLoader $loader CsvLoader
     * @return string[] エラーメッセージ
     */
    protected static function getErrorMessages($model, $lineNumber, $loader)
    {
        $errors = [];
        foreach ($model->getErrors() as $attribute => $attributeErrors) {
            $attributeRow = array_flip($loader->getCsvAttributes());
            if (array_key_exists($attribute, $attributeRow)) {
                $columnNumber = $attributeRow[$attribute] + 1;
            }

            foreach ($attributeErrors as $error) {
                if (isset($columnNumber)) {
                    $errors[] = Yii::t("app", "{lineNumber}行目 ({columnNumber}列目) : {error}", ["lineNumber" => $lineNumber,
                        "error" => $error, "columnNumber" => $columnNumber]);
                } else {
                    $errors[] = Yii::t("app", "{lineNumber}行目 : {error}", ["lineNumber" => $lineNumber, "error" => $error]);
                }
            }
        }

        return $errors;
    }
}
