<?php
namespace app\modules\manage\components;

use app\models\manage\CustomField;
use app\common\csv\CsvLoader;
use yii;
use app\common\csv\CsvWorker;
use yii\db\ActiveRecord;

/**
 * カスタムフィールド変更用CSVから行を受け取り、モデルを生成します。
 *
 * @uses CsvLoader
 */
class CustomFieldCsvLoader extends CsvLoader
{
    /**
     * CSVのフォーマットと、順列が等しいAttribute配列を返す
     *
     * @return string[]
     */
    public function getCsvAttributes()
    {
        return [
            'custom_no',
            'detail',
            'url',
            'pict',
            'valid_chk',
        ];
    }

    /**
     * @param string[] $line CSVの行
     * @param string $encodeFrom エンコード元の文字コード
     * @param string $encodeTo エンコード先の文字コード
     * @return \app\models\manage\CustomField
     */
    public function getInstance($line, $encodeFrom = 'ASCII,JIS,UTF-8,EUC-JP,SJIS-win', $encodeTo = 'UTF-8')
    {
        /**
         * @var CustomField $model
         */
        if (count(self::getCsvAttributes()) != count($line)) {
            $model = new CustomField();
            $model->addError('formatError', Yii::t('app', 'CSVのフォーマットが正しくありません'));
            return $model;
        }

        $row = array_combine($this->getCsvAttributes(), $line);
        $model = CustomField::find()->select(['id', 'custom_no'])
                                    ->andWhere(['tenant_id' => Yii::$app->tenant->id])
                                    ->andWhere(['custom_no' => $row['custom_no']])
                                    ->one();
        if (empty($model)) {
            $model = new CustomField();
        }

        $this->loadFromCsv($model, $line, $encodeFrom, $encodeTo);
        return $model;
    }

    /**
     * @param CsvWorker|null $worker
     */
    public function beforeCsvLoad($worker = null)
    {
        // UserCsvRegister::resetLoadedLoginIds();
    }

    /**
     * @param CsvWorker|null $worker
     * @return bool
     */
    public function afterCsvLoad($worker = null)
    {
        // UserCsvRegister::resetLoadedLoginIds();
    }

    /**
     * CSVの行をモデルにloadする
     * pictカラムを削除上書き可能にするためのオーバーライド
     * @param CustomField $model load先のモデル
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
            $model->addError('formatError', Yii::t('app', 'CSVのフォーマットが正しくありません'));
            return $model;
        }

        $row = array_combine($this->getCsvAttributes(), $line);
        $model->scenario = CustomField::SCENARIO_CSV;
        if ($model->load($row, '') === false) {
            return false;
        }
        return $model;
    }
}
