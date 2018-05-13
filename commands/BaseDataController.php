<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/06/10
 * Time: 10:15
 */
namespace app\commands;

use proseeds\models\Tenant;
use yii\console\Controller;
use yii\console\Exception;

/**
 * Class DataController
 * @package app\commands
 */
abstract class BaseDataController extends Controller
{
    /** @var int メインテーブル名 */
    protected $tableName;

    /** @var int テナントid */
    protected $tenantId;

    /** @var string 現在データ生成中のテーブル名 */
    protected $currentTable;

    /** @var array 現在データ生成中のrowの主キーid（$currentId[テーブル名] = id） */
    protected $currentId;

    /** @var array relationするテーブルのidのキャッシュ */
    protected $ids;

    /** @var array 生成候補データのキャッシュ */
    protected $data;

    /** @var int 1回にinsertするレコード数 */
    protected $limit = 10000;

    /** @var bool 有効なレコードを生成するのか、無効なレコードを生成するのか */
    protected $valid = 1;

    /** @var array テーブル名をkeyにしてinsertするrowsを保持する */
    protected $rows;

    /**
     * tenant_id=1～$numOfTenantsに$numOfRecordsずつレコードを挿入する
     * メモリ節約のためtransactionとか全く効かせていないので注意してください
     * @param int $numOfRecords
     * @param int $numOfTenants
     * @param bool $confirm
     * @return int
     */
    public function actionIndex($numOfRecords, $numOfTenants, $confirm = true)
    {
        if ($confirm) {
            var_dump($confirm);
            if (!$this->confirm("\ntenant_id=1～{$numOfTenants}の{$this->tableName}に{$numOfRecords}ずつレコードを挿入します")) {
                return static::EXIT_CODE_NORMAL;
            }
        } else {
            echo "\ntenant_id=1～{$numOfTenants}の{$this->tableName}に{$numOfRecords}ずつレコードを挿入します\n";
        }


        // メモリと実行時間監視開始
        $baseMemoryUsage = memory_get_usage();
        $baseTime = microtime(true);

        for ($tenantId = 1; $tenantId <= $numOfTenants; $tenantId++) {
            // tenantにinsert
            $this->tenantInsert($numOfRecords, $tenantId);
            echo "tenant_id={$tenantId}へのinsert完了\n";
        }
        // 実行時間とメモリを出力
        $maxMemoryUsage = (memory_get_peak_usage() - $baseMemoryUsage) / (1024 * 1024);
        $processTime = microtime(true) - $baseTime;

        printf("Max Memory Usage : %.3f [MB]\n", $maxMemoryUsage);
        printf("Process Time : %.2f [s]\n", $processTime);
        return static::EXIT_CODE_NORMAL;
    }

    /**
     * tenantを
     * @param int $numOfRecords
     * @param int $tenantId
     * @return int
     */
    public function actionTenantInsert($numOfRecords, $tenantId)
    {
        if (!$this->confirm("\ntenant_id={$tenantId}に{$numOfRecords}件のレコードを挿入します")) {
            return static::EXIT_CODE_NORMAL;
        }

        // メモリと実行時間監視開始
        $baseMemoryUsage = memory_get_usage();
        $baseTime = microtime(true);

        // tenantにinsert
        $this->tenantInsert($numOfRecords, $tenantId);

        // 実行時間とメモリを出力
        $maxMemoryUsage = (memory_get_peak_usage() - $baseMemoryUsage) / (1024 * 1024);
        $processTime = microtime(true) - $baseTime;

        printf("Max Memory Usage : %.3f [MB]\n", $maxMemoryUsage);
        printf("Process Time : %.2f [s]\n", $processTime);
        return static::EXIT_CODE_NORMAL;
    }

    /**
     * insert処理
     * @param int $count
     * @return mixed
     */
    abstract protected function insert($count);

    /**
     * tenantにレコードをinsertする
     * @param int $numOfRecords
     * @param int $tenantId
     */
    protected function tenantInsert($numOfRecords, $tenantId)
    {
        // tenantIdをセット
        $this->setTenant($tenantId);

        if ($numOfRecords > $this->limit) {
            // limitを超えていたら分けてinsertする
            $num = $numOfRecords / $this->limit;
            for ($i = 1; $i <= $num; $i++) {
                // limitずつinsert
                $this->insert($this->limit);
            }
            // 余りをinsert
            if ($numOfRecords % $this->limit !== 0) {
                $this->insert($numOfRecords % $this->limit);
            }
        } else {
            // limitを超えていなければそのままinsert
            $this->insert($numOfRecords);
        }
    }

    /**
     * テナントをセットする
     * @param int $tenantId
     */
    protected function setTenant($tenantId)
    {
        $this->tenantId = $tenantId;
        $_SERVER['HTTP_HOST'] = Tenant::findOne($tenantId)->tenant_code;
        \Yii::$app->clear('tenant');
        \Yii::$app->setComponents([
            'tenant' => [
                'class' => 'proseeds\base\Tenant',
                'exclude' => [
                    'tables' => [
                        'tenant',
                        'user_session',
                        'manager_session',
                        'station',
                        'auth_assignment',
                        'auth_item',
                        'auth_item_child',
                        'auth_rule',
                        'complete_mail_domain',
                        'dist',
                        '{{%auth_assignment}}',
                    ],
                ],
            ],
        ]);
    }

    /** 
     * 開発用・開発終わったら消します 
     * @param $tableName 
     * @return mixed 
     */ 
    protected function makeRow($tableName) 
    { 
        $this->currentTable = $tableName; 
        $row = require(__DIR__ . "/../data/{$tableName}.php"); 
        $this->currentId[$tableName]++; 
        return $row; 
    } 

    /**
     * データ作成中テーブルのidを基に他テーブルのidを順番に入れていく
     * @param string $tableName
     * @return mixed
     * @throws Exception
     */
    protected function incId($tableName)
    {
        if (!count($this->ids[$tableName])) {
            throw new Exception("in tenant{$this->tenantId}, {$tableName} has no records.");
        }

        return $this->inc($this->ids[$tableName], $this->currentId[$this->currentTable]);
    }

    /**
     * 電話番号を返す
     * todo 何パターンか用意した方が良いか、いっそrandで作るか速度とも相談して検討
     * @return string
     */
    protected function telNo()
    {
        return '000-1111-2222';
    }

    /**
     * メールアドレスを返す
     * todo 何パターンか用意した方が良いか、いっそrandで作るか速度とも相談して検討
     * @return string
     */
    protected function mail()
    {
        return 'not-exists@pro-seeds.co.jp';
    }

    /**
     * URLを返す
     * todo 何パターンか用意した方が良いか、いっそrandで作るか速度とも相談して検討
     * @return string
     */
    protected function url()
    {
        return 'http://demo2.job-maker.jp/';
    }

    /**
     * タイムスタンプを返す
     * todo 仕様検討
     * @return int
     */
    protected function timeStamp()
    {
        return time();
    }

    /**
     * データ候補からランダムに一つを取得する（順番でもいいけど）
     * @param string $columnName
     * @param bool $addTenantSign
     * @return mixed
     */
    protected function data($columnName, $addTenantSign = true)
    {
        if (!$this->currentTable) {
            new Exception('please set $currentTable');
        }

        if (!isset($this->data[$this->currentTable][$columnName])) {
            $this->data[$this->currentTable][$columnName] = require(__DIR__ . "/../data/{$this->currentTable}/{$columnName}.php");
        }
        $array = $this->data[$this->currentTable][$columnName];

        if ($addTenantSign) {
            return $this->rand($array) . "tenant={$this->tenantId}";
        } else {
            return $this->rand($array);
        }
    }

    /**
     * 作成中データのidを取得する
     * @return mixed
     */
    protected function id()
    {
        return $this->currentId[$this->currentTable];
    }

    /**
     * 開始日と終了日を出力する
     * @return array array
     */
    protected function startAndEndDate()
    {
        if ($this->valid) {
            $startDate = rand(1450000000, time()); // 2015/12/13 18:46:40 ～ now
            $endDate = rand(1550000000, 1650000000); // 2019/02/13 04:33:20 ～ 2022/04/15 14:20:00
        } else {
            // 適当な日付な場合
            $startDate = rand(1450000000, 1550000000); // 2015/12/13 18:46:40 ～ 2019/02/13 04:33:20
            $endDate = rand($startDate, 1650000000); // start date ～ 2022/04/15 14:20:00
            // 有効期限外ばかりにする場合(未来)
//            $startDate = rand(1550000000, 1650000000); // 2015/12/13 18:46:40 ～ 2019/02/13 04:33:20
//            $endDate = rand($startDate, 1650000000); // start date ～ 2022/04/15 14:20:00
            // 有効期限外ばかりにする場合(過去)
//            $startDate = rand(1450000000, time()); // 2015/12/13 18:46:40 ～ 2019/02/13 04:33:20
//            $endDate = rand($startDate, time()); // start date ～ 2022/04/15 14:20:00
        }

        return ['start' => $startDate, 'end' => $endDate];
    }

    /**
     * 配列からランダムに値を取得する
     * @param array $array
     * @return mixed
     */
    protected function rand($array)
    {
        return $array[array_rand($array)];
    }

    /**
     * indexを元にarrayの要素を順番に取得する
     * @param array $array
     * @param int $index
     * @return mixed
     */
    protected function inc($array, $index)
    {
        $i = ($index - 1) % count($array);
        return current(array_slice($array, $i, 1, true));
    }
}
