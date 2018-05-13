<?php
use app\common\csv\CsvLoader;
use app\models\ToolMaster;
use app\modules\manage\components\ToolMasterCsvLoader;
use Codeception\Specify;
use tests\codeception\fixtures\ToolMasterFixture;
use tests\codeception\unit\JmTestCase;

/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/03
 * Time: 18:34
 */
class ToolMasterCsvLoaderTest extends JmTestCase
{
    public function fixtures()
    {
        return [
            'tool_master' => ToolMasterFixture::className(),
        ];
    }

    public function testGetInstance()
    {
        $this->specify('csvのrowのcolumn数が違う場合', function () {
            $line=[
                '12',
                'test'
            ];
            $encodeFrom = 'ASCII,JIS,UTF-8,EUC-JP,SJIS-win';
            $encodeTo = 'UTF-8';
            $tool = new ToolMasterCsvLoader;
            verify($tool->getInstance($line, $encodeFrom, $encodeTo)->getErrors('formatError'))->equals(['CSVのフォーマットが正しくありません']);
        });
        // test case 2 : when empty($model) because $row['tool_no'] is invalid
        $this->specify('tool_noが不正な場合', function () {
            $line=[
                '99',
                'test',
                'test',
                'test',
                'test',
                'test',
            ];
            $encodeFrom = 'ASCII,JIS,UTF-8,EUC-JP,SJIS-win';
            $encodeTo = 'UTF-8';
            $tool = new ToolMasterCsvLoader;
            verify($tool->getInstance($line, $encodeFrom, $encodeTo)->getErrors('formatError'))->equals(['指定されたデータが存在しません']);
        });
        $this->specify('正常な場合', function () {
            $line=[
                '1',
                'test',
                'test',
                'test',
                'test',
                'test',
            ];
            $encodeFrom = 'ASCII,JIS,UTF-8,EUC-JP,SJIS-win';
            $encodeTo = 'UTF-8';
            $tool = new ToolMasterCsvLoader;
            verify($tool->getInstance($line, $encodeFrom, $encodeTo)->hasErrors())->equals(false);
        });
    }
}
