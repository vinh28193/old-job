<?php

namespace app\common\csv;


use proseeds\helpers\StringUtil;
use yii\base\Component;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class CsvLoader
 * @package app\common\csv
 * CSVから行を受け取り、モデルを生成する
 */
abstract class CsvLoader extends Component
{
    /**
     * @var ActiveRecord 生成するモデルのクラス
     */
    public $modelClass;

    /**
     * CSVのフォーマットと、順列が等しいAttribute配列を返す
     * @return string[]
     */
    abstract public function getCsvAttributes();

    /**
     * CSV読み込み開始前処理（バリデーション時
     * @param CsvWorker|null $worker
     */
    public function beforeCsvLoad($worker = null)
    {
    }
    
    /**
     * CSV読み込み終了後処理（バリデーション時）
     * @param CsvWorker|null $worker
     * @return bool
     */
    public function afterCsvLoad($worker = null)
    {
        return true;
    }

    /**
     * CSV読み込み開始前処理（セーブ時）
     * @param CsvWorker|null $worker
     */
    public function beforeCsvSave($worker = null)
    {
    }

    /**
     * CSV読み込み終了後処理（セーブ時）
     * @param CsvWorker|null $worker
     */
    public function afterCsvSave($worker = null)
    {
    }

    /**
     * @param string[] $line CSVの行
     * @param string $encodeFrom エンコード元の文字コード
     * @param string $encodeTo エンコード先の文字コード
     * @return ActiveRecord モデルのインスタンス
     */
    public function getInstance($line, $encodeFrom = 'ASCII,JIS,UTF-8,EUC-JP,SJIS-win', $encodeTo = 'UTF-8')
    {
        /**
         * @var ActiveRecord $model
         */
        $model = Yii::createObject(['class' => $this->modelClass]);
        return $this->loadFromCsv($model, $line, $encodeTo, $encodeFrom);
    }

    /**
     * CSVの行をモデルにloadする
     * @param ActiveRecord $model load先のモデル
     * @param string[] $line CSVの行
     * @param string $encodeFrom エンコード元の文字コード
     * @param string $encodeTo エンコード先の文字コード
     * @return ActiveRecord|boolean $model モデルのインスタンス
     */
    protected function loadFromCsv($model, $line, $encodeFrom = 'ASCII,JIS,UTF-8,EUC-JP,SJIS-win', $encodeTo = 'UTF-8')
    {
        // 文字エンコードを変更
        if (isset($encodeTo)) {
            foreach ((array)$line as $key => $val) {
                $line[$key] = mb_convert_encoding($val, $encodeTo, $encodeFrom);
            }
        }

        if (count($this->getCsvAttributes()) != count($line)) {
            $model->addError("formatError", Yii::t('app', 'CSVのフォーマットが正しくありません'));
            return $model;
        }

        $row = static::convertRow(array_combine($this->getCsvAttributes(), $line));
        if ($model->load($row, '') === false) return false;

        return $model;
    }

    /**
     * 行のセルを前処理に掛ける
     * @param $row
     * @return \string[]
     */
    public static function convertRow($row)
    {
        $row = self::unsetEmptyCell($row);
        return self::parseDeleteCell($row);
    }

    /**
     * 中身が"[del]"のセルを空に変換する
     * @val string[] $row csv行の配列
     * @return string[] 変換後の配列
     */
    protected static function parseDeleteCell($row)
    {
        return array_map(function ($val) {
            if (strtolower($val) == "[del]") return "";
            return $val;
        }, $row);
    }

    /**
     * 空白セルを配列から取り除く
     * @param string[] $row csv行の配列
     * @return string[] 空白セルを取り除いた配列
     */
    protected static function unsetEmptyCell($row)
    {
        $ret = [];
        foreach ((array)$row as $key => $val) {
            if ($val !== "") $ret[$key] = $val;
        }

        return $ret;
    }
}
