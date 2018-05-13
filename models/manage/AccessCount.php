<?php

namespace app\models\manage;

use yii\base\Model;
use yii\helpers\Json;
use Yii;

/**
 * This is the model class for table "access_log".
 *
 * @property int $todayPcCount
 * @property int $todaySpCount
 * @property int $yesterdayPcCount
 * @property int $yesterdaySpCount
 * @property array $userAgentData
 */
class AccessCount extends Model
{
    /** 各getter用private property */
    private $_todayPcCount;
    private $_todaySpCount;
    private $_yesterdayPcCount;
    private $_yesterdaySpCount;
    private $_userAgentData;

    /**
     * まだ用意されていなければカウントを用意する
     * 一旦今日と昨日別々でカウントする方法に切り替え
     */
    private function prepareCount()
    {
        if ($this->_todayPcCount !== null) {
            return;
        }

        $this->_todayPcCount = 0;
        $this->_todaySpCount = 0;
        $this->_yesterdayPcCount = 0;
        $this->_yesterdaySpCount = 0;
        $logs = AccessLog::find()
            ->select(['carrier_type', AccessLog::tableName() . '.accessed_at'])
            ->where(['>=', AccessLog::tableName() . '.accessed_at', strtotime('yesterday')])
            ->onlyJobDetail()
            ->asArray()->all();

        // 4通りに場合分けしてカウント
        foreach ($logs as $log) {
            if ($log['carrier_type'] == AccessLog::PC_CARRIER) {
                if ($log['accessed_at'] >= strtotime('today')) {
                    $this->_todayPcCount++;
                } else {
                    $this->_yesterdayPcCount++;
                }
            } else {
                if ($log['accessed_at'] >= strtotime('today')) {
                    $this->_todaySpCount++;
                } else {
                    $this->_yesterdaySpCount++;
                }
            }
        }
    }

    /**
     * 今日の分のキャリア別カウントを準備する
     */
    private function prepareTodayCount()
    {
        if ($this->_todayPcCount !== null) {
            return;
        }
        // 初めてカウントする場合
        $this->_todayPcCount = 0;
        $this->_todaySpCount = 0;
        $todayCarrierArray = AccessLog::authFind()
            ->select('carrier_type')
            ->today()
            ->onlyJobDetail()
            ->column();
        foreach ($todayCarrierArray as $carrierType) {
            if ($carrierType == AccessLog::SMART_PHONE_CARRIER) {
                $this->_todaySpCount++;
            } else {
                $this->_todayPcCount++;
            }
        }
    }

    /**
     * 昨日の分のキャリア別カウントを準備する
     */
    private function prepareYesterdayCount()
    {
        if ($this->_yesterdayPcCount !== null) {
            return;
        }
        // 初めてカウントする場合
        $this->_yesterdayPcCount = 0;
        $this->_yesterdaySpCount = 0;
        $yesterdayCarrierArray = AccessLog::authFind()
            ->select('carrier_type')
            ->yesterday()
            ->onlyJobDetail()
            ->column();
        foreach ($yesterdayCarrierArray as $carrierType) {
            if ($carrierType == AccessLog::SMART_PHONE_CARRIER) {
                $this->_yesterdaySpCount++;
            } else {
                $this->_yesterdayPcCount++;
            }
        }
    }

    /**
     * 今日のPCでの求人情報閲覧数
     * @return null
     */
    public function getTodayPcCount()
    {
//        $this->prepareCount();
        $this->prepareTodayCount();
        return $this->_todayPcCount;
    }

    /**
     * 今日のスマホでの求人情報閲覧数
     * @return null
     */
    public function getTodaySpCount()
    {
//        $this->prepareCount();
        $this->prepareTodayCount();
        return $this->_todaySpCount;
    }

    /**
     * 昨日のPCでの求人情報閲覧数
     * @return mixed
     */
    public function getYesterdayPcCount()
    {
//        $this->prepareCount();
        $this->prepareYesterdayCount();
        return $this->_yesterdayPcCount;
    }

    /**
     * 今日のスマホでの求人情報閲覧数
     * @return mixed
     */
    public function getYesterdaySpCount()
    {
//        $this->prepareCount();
        $this->prepareYesterdayCount();
        return $this->_yesterdaySpCount;
    }


    /**
     * 今日のユーザーエージェントデータ
     * @return array
     */
    public function getUserAgentData()
    {
        // private propertyに値がセットされていない場合
        if ($this->_userAgentData === null) {
            $this->_userAgentData = AccessLog::authFind()
                ->select(['carrier_type','access_browser','pvCount' => 'count(*)' ])
                ->today()
                ->onlyJobDetail()
                ->groupBy(['carrier_type','access_browser'])
                ->orderBy(['pvCount' => SORT_DESC, 'carrier_type' => SORT_ASC])
                ->limit(10)
                ->asArray()
                ->all();
        }

        if (count($this->_userAgentData) > 0) {
            // カラム情報
            $chartData[] = [Yii::t('app', 'ブラウザ名'), Yii::t('app', 'PV数'), [ 'role' => 'style' ],[ 'role' => 'annotation' ]];
            // データ情報
            foreach ($this->_userAgentData as $userAgent) {
                $chartData[] = [
                    $userAgent['access_browser'],
                    (int)$userAgent['pvCount'],
                    $userAgent['carrier_type'] == 0 ? '#4169e1' : '3cb371',
                    $userAgent['pvCount'] . 'PV',
                ];
            }
            return Json::encode($chartData);
        } else {
            return null;
        }
    }
}
