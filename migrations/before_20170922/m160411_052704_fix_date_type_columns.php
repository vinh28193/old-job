<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m160411_052704_fix_date_type_columns.php
 * UnixTimeStamp型への変更に合わせて各テーブルの掲載期間の型を変更
 * ソースかなり汚いけど多分動くので許してください
 */
class m160411_052704_fix_date_type_columns extends Migration
{
    const TABLES = [
        [
            'tableName' => 'client_charge',
            'disp_end_date' => [
                'newColumnName' => 'disp_end_date',
                'comment' => '掲載終了日',
            ],
        ],
        [
            'tableName' => 'job_master',
            'disp_start_date' => [
                'newColumnName' => 'disp_start_date',
                'comment' => '掲載開始日',
            ],
            'disp_end_date' => [
                'newColumnName' => 'disp_end_date',
                'comment' => '掲載終了日',
            ],
        ],
        [
            'tableName' => 'job_master_tmp',
            'primaryKey' => 'tmp_id',
            'disp_start_date' => [
                'newColumnName' => 'disp_start_date',
                'comment' => '掲載開始日',
            ],
            'disp_end_date' => [
                'newColumnName' => 'disp_end_date',
                'comment' => '掲載終了日',
            ],
        ],
        [
            'tableName' => 'job_master_update_tmp',
            'primaryKey' => 'tmp_id',
            'disp_start_date' => [
                'newColumnName' => 'disp_start_date',
                'comment' => '掲載開始日',
            ],
            'disp_end_date' => [
                'newColumnName' => 'disp_end_date',
                'comment' => '掲載終了日',
            ],
        ],
        [
            'tableName' => 'widget_data',
            'disp_start_date' => [
                'newColumnName' => 'disp_start_date',
                'comment' => '掲載開始日',
            ],
            'disp_end_date' => [
                'newColumnName' => 'disp_end_date',
                'comment' => '掲載終了日',
            ],
        ],
    ];

    public function safeUp()
    {
        foreach (self::TABLES as $table) {
            $tableName = \yii\helpers\ArrayHelper::remove($table, 'tableName');
            $primaryKey = \yii\helpers\ArrayHelper::remove($table, 'primaryKey', 'id');
            foreach ($table as $oldColumnName => $newColumn) {
                $this->toUnixTime($tableName,  $newColumn['newColumnName'], $newColumn['comment'], $primaryKey);
            }
        }
    }

    public function safeDown()
    {
        foreach (self::TABLES as $table) {
            $tableName = \yii\helpers\ArrayHelper::remove($table, 'tableName');
            $primaryKey = \yii\helpers\ArrayHelper::remove($table, 'primaryKey', 'id');
            foreach ($table as $oldColumnName => $newColumn) {
                $this->toDatetime($tableName, $oldColumnName,  $newColumn['comment'], $primaryKey);
            }
        }
    }

    private function toUnixTime($tableName, $newColumnName, $comment, $primaryKey)
    {
        $dateTimes = \yii\helpers\ArrayHelper::map((new Query)->select([$primaryKey, $newColumnName])->from($tableName)->all(), $primaryKey, $newColumnName);
        $this->alterColumn($tableName, $newColumnName, $this->string());
        $this->update($tableName, [$newColumnName => 0]);
        $this->alterColumn($tableName, $newColumnName, $newColumnName == 'disp_start_date' ? $this->integer(11)->notNull() : $this->integer(11) . ' COMMENT "' . $comment . '"');
        foreach ($dateTimes as $id => $dateTime) {
            $unixTime = strtotime($dateTime);
            if($unixTime < 0){
                $unixTime = 0;
            }
            $this->update($tableName, [$newColumnName => $unixTime], [$primaryKey => $id]);
        }
    }

    private function toDatetime($tableName, $oldColumnName, $comment, $primaryKey)
    {
        $dateTimes = \yii\helpers\ArrayHelper::map((new Query)->select([$primaryKey, $oldColumnName])->from($tableName)->all(), $primaryKey, $oldColumnName);
        $this->alterColumn($tableName, $oldColumnName, $this->string());
        $this->update($tableName, [$oldColumnName => '0000-00-00']);
        $this->alterColumn($tableName, $oldColumnName, $this->date() . ' COMMENT "' . $comment . '"');
        foreach ($dateTimes as $id => $dateTime) {
            $this->update($tableName, [$oldColumnName => date('Y-m-d', $dateTime)], ['id' => $id]);
        }
    }
}
