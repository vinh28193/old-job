<?php

namespace app\common\csv;

use app\common\csv\CsvLoader;
use yii\data\BaseDataProvider;
use yii\db\ActiveRecord;

/**
 * Class CsvDataProvider
 * @package app\common\csv
 * サーバ上のCSVファイルからデータを提供するDataProvider
 */
class CsvDataProvider extends BaseDataProvider
{
    /**
     * @var string CSVを読み込むクラス
     */
    public $loaderClass;

    /**
     * @var string 読み出す CSV ファイルの名前
     */
    public $filename;

    /**
     * @var string|callable キーカラムの名前またはそれを返すコーラブル
     */
    public $key;

    /**
     * @var \SplFileObject
     */
    protected $fileObject;

    /**
     * @var string[] attribute用配列
     */
    public $attributes;

    /**
     * @var string エンコード先の文字コード
     * 配列で出力する際のみ、使用されます
     */
    public $encodeTo = 'UTF-8';

    /**
     * @var string エンコード元の文字コード
     * 配列で出力する際のみ、使用されます
     */
    public $encodeFrom = 'SJIS-win';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // ファイルを開く
        $this->fileObject = new \SplFileObject(\Yii::getAlias($this->filename));
        $this->fileObject->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        $models = [];
        $pagination = $this->getPagination();

        if ($pagination === false) {
            // ページネーションが無い場合、全ての行を読む
            while (!$this->fileObject->eof()) {
                $line = $this->fileObject->fgetcsv();
                if (($data = $this->getRow($line)) === false) continue;
                $models[] = $data;
                $this->fileObject->next();
            }
        } else {
            // ページネーションがある場合、一つのページだけを読む
            $pagination->totalCount = $this->getTotalCount();
            $this->fileObject->seek($pagination->getOffset());
            $limit = $pagination->getLimit();

            for ($count = 0; $count < $limit;) {
                if ($this->fileObject->eof()) break;

                $line = $this->fileObject->fgetcsv();
                if (empty($line) || is_null($line[0])) continue;
                if (($model = $this->getRow($line)) === false) continue;
                $models[] = $model;
                $count++;
            }
        }
        return $models;
    }

    /**
     * CSVの行を受け取り、モデルもしくは配列として返す。
     * attributesが指定されている場合は、それらをkeyとして扱う
     * @return array|boolean|ActiveRecord 行の取得に失敗した場合はfalseを返す
     */
    protected function getRow($line)
    {
        if (isset($this->loaderClass)) {
            /** モデルとして読み込む */
            /**
             * @var CsvLoader $loader
             */
            $loader = \Yii::createObject(['class' => $this->loaderClass]);
            $model = $loader->getInstance($line);

            if ($model->hasErrors()) return false;
            return $model;
        } else {
            /** 配列として読み込む */
            $row = array_map(function ($val) {
                return mb_convert_encoding($val, $this->encodeTo, $this->encodeFrom);
            }, $line);

            if (isset($this->attributes)) {
                if (count($row) != count($this->attributes)) return false;
                return array_combine($this->attributes, $row);
            };

            return $row;
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareKeys($models)
    {
        if ($this->key !== null) {
            $keys = [];

            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }

            return $keys;
        } else {
            return array_keys($models);
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareTotalCount()
    {
        $count = 0;

        foreach ($this->fileObject as $line) {
            if ($this->fileObject->key() == 0) continue;
            if (empty($line) || is_null($line[0])) continue;
            $count++;
        }
        $this->fileObject->rewind();

        return $count;
    }
}
