<?php
namespace app\modules\manage\components;

use app\models\ToolMaster;
use app\common\csv\CsvLoader;
use yii;
use app\common\csv\CsvWorker;

/**
 * TDK変更用CSVから行を受け取り、ToolMasterモデルを生成します。
 *
 * @uses CsvLoader
 */
class ToolMasterCsvLoader extends CsvLoader
{
    /**
     * CSVのフォーマットと、順列が等しいAttribute配列を返す
     *
     * @return string[]
     */
    public function getCsvAttributes()
    {
        return [
            'tool_no',
            'page_name',
            'title',
            'description',
            'keywords',
            'h1',
        ];
    }

    /**
     * @param string[] $line CSVの行
     * @param string $encodeFrom エンコード元の文字コード
     * @param string $encodeTo エンコード先の文字コード
     * @return \app\models\ToolMaster
     */
    public function getInstance($line, $encodeFrom = 'ASCII,JIS,UTF-8,EUC-JP,SJIS-win', $encodeTo = 'UTF-8')
    {
        /**
         * @var ToolMaster $model
         */
        if (count(self::getCsvAttributes()) != count($line)) {
            $model = new ToolMaster;
            $model->addError("formatError", Yii::t('app', 'CSVのフォーマットが正しくありません'));
            return $model;
        }

        $row = array_combine($this->getCsvAttributes(), $line);
        $model = ToolMaster::find()->select(['id', 'tool_no', 'tenant_id'])->andWhere(['tool_no' => $row['tool_no']])->one();
        if (empty($model)) {
            $model = new ToolMaster;
            $model->addError('formatError', Yii::t('app', '指定されたデータが存在しません'));
            return $model;
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
     */
    public function afterCsvLoad($worker = null)
    {
        // UserCsvRegister::resetLoadedLoginIds();
    }
}

