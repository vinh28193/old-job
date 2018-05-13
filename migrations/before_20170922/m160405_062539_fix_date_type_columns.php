<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m160405_062539_fix_date_type_columns
 * UnixTimeStamp型への変更に合わせて各テーブルの作成・更新日時の型を変更
 * ソースかなり汚いけど多分動くので許してください
 */
class m160405_062539_fix_date_type_columns extends Migration
{
    const TABLES = [
        [
            'tableName' => 'access_log',
            'access_date' => [
                'newColumnName' => 'created_at',
                'comment' => 'アクセス日',
            ],
        ],
        // 論理時間なため保留
//        [
//            'tableName' => 'access_log_monthly',
//            'access_date' => [
//                'newColumnName' => 'access_date',
//                'comment' => 'アクセス日',
//            ],
//        ],
        [
            'tableName' => 'admin_master',
            'regist_datetime' => [
                'newColumnName' => 'created_at',
                'comment' => '登録日時',
            ],
        ],
        [
            'tableName' => 'application_master',
            // 論理時間なため保留
//            'birth_date' => [
//                'newColumnName' => 'birth_date',
//                'comment' => '誕生日',
//            ],
            'regist_datetime' => [
                'newColumnName' => 'created_at',
                'comment' => '応募日時',
            ],
        ],
        // 仕様未定のため保留
//        [
//            'tableName' => 'client_charge',
//            'disp_end_date' => [
//                'newColumnName' => '',
//                'comment' => '掲載終了日',
//            ],
//        ],
        [
            'tableName' => 'client_master',
            'regist_date' => [
                'newColumnName' => 'created_at',
                'comment' => '登録日',
            ],
        ],
        [
            'tableName' => 'client_master_tmp',
            'primaryKey' => 'tmp_id',
            'regist_datetime' => [
                'newColumnName' => 'created_at',
                'comment' => '登録日',
            ],
        ],
        [
            'tableName' => 'client_scout_favorite_member',
            'primaryKey' => 'member_id',
            'regist_datetime' => [
                'newColumnName' => 'created_at',
                'comment' => 'コメント未定',
            ],
        ],
        [
            'tableName' => 'client_scout_template',
            'primaryKey' => 'client_scout_template_id',
            'update_date' => [
                'newColumnName' => 'updated_at',
                'comment' => 'コメント未定',
            ],
        ],
        [
            'tableName' => 'corp_master',
            'regist_datetime' => [
                'newColumnName' => 'created_at',
                'comment' => '登録日時',
            ],
        ],
        [
            'tableName' => 'job_application_count',
            'primaryKey' => 'job_id',
            'update_date' => [
                'newColumnName' => 'updated_at',
                'comment' => '',
            ],
        ],
        [
            'tableName' => 'job_master',
            // 仕様未定のため保留
//            'disp_start_date' => [
//                'newColumnName' => '',
//                'comment' => '掲載開始日',
//            ],
//            'disp_end_date' => [
//                'newColumnName' => '',
//                'comment' => '掲載終了日',
//            ],
            'regist_datetime' => [
                'newColumnName' => 'created_at',
                'comment' => '登録日時',
            ],
            'update_time' => [
                'newColumnName' => 'updated_at',
                'comment' => '更新日時',
            ],
        ],
        [
            'tableName' => 'job_master_tmp',
            'primaryKey' => 'tmp_id',
            // 仕様未定のため保留
//            'disp_start_date' => [
//                'newColumnName' => '',
//                'comment' => '掲載開始日',
//            ],
//            'disp_end_date' => [
//                'newColumnName' => '',
//                'comment' => '掲載終了日',
//            ],
            'regist_date' => [
                'newColumnName' => 'created_at',
                'comment' => '登録日時',
            ],
            'update_time' => [
                'newColumnName' => 'updated_at',
                'comment' => '更新日時',
            ],
        ],
        [
            'tableName' => 'job_master_update_tmp',
            'primaryKey' => 'tmp_id',
            // 仕様未定のため保留
//            'disp_start_date' => [
//                'newColumnName' => '',
//                'comment' => '掲載開始日',
//            ],
//            'disp_end_date' => [
//                'newColumnName' => '',
//                'comment' => '掲載終了日',
//            ],
            'regist_date' => [
                'newColumnName' => 'created_at',
                'comment' => '登録日時',
            ],
            'update_time' => [
                'newColumnName' => 'updated_at',
                'comment' => '更新日時',
            ],
        ],
        [
            'tableName' => 'media_upload',
            'update_time' => [
                'newColumnName' => 'updated_at',
                'comment' => '更新日時',
            ],
        ],
        [
            'tableName' => 'member_master',
            // 論理時間なため保留
//            'birth_date' => [
//                'newColumnName' => 'birth_date',
//                'comment' => '誕生日',
//            ],
            'regist_datetime' => [
                'newColumnName' => 'created_at',
                'comment' => '登録日時',
            ],
            'update_datetime' => [
                'newColumnName' => 'updated_at',
                'comment' => '更新日時',
            ],
        ],
        [
            'tableName' => 'member_resume',
            'primaryKey' => 'member_resume_id',
            'regist_date' => [
                'newColumnName' => 'created_at',
                'comment' => '登録日時',
            ],
        ],
        [
            'tableName' => 'oiwai_application',
            'primaryKey' => 'oiwai_application_id',
            'application_time' => [
                'newColumnName' => 'created_at',
                'comment' => '申請日時',
            ],
        ],
        [
            'tableName' => 'oiwai_master',
            'primaryKey' => 'oiwai_master_id',
            'regist_datetime' => [
                'newColumnName' => 'created_at',
                'comment' => '申請日時',
            ],
        ],
//        [
//            'tableName' => 'sample_pict',
//            'primaryKey' => 'sample_pict_id',
//            'update_time' => [
//                'newColumnName' => 'updated_at',
//                'comment' => '更新日時',
//            ],
//        ],
        [
            'tableName' => 'satellite_master',
            'primaryKey' => 'satellite_id',
            'regist_time' => [
                'newColumnName' => 'created_at',
                'comment' => '作成日時',
            ],
            'update_time' => [
                'newColumnName' => 'updated_at',
                'comment' => '更新日時',
            ],
        ],
        [
            'tableName' => 'send_mail_logs',
            'primaryKey'=>'send_mail_logs_id',
            'send_mail_start_datetime' => [
                'newColumnName' => 'start_sending_at',
                'comment' => '送信開始日時',
            ],
            'send_mail_end_datetime' => [
                'newColumnName' => 'finish_sending_at',
                'comment' => '送信完了日時',
            ],
        ],
        [
            'tableName' => 'send_mail_logs_application',
            'primaryKey'=>'send_mail_logs_application_id',
            'sendmail_datetime' => [
                'newColumnName' => 'send_at',
                'comment' => '送信日時',
            ],
        ],
        [
            'tableName' => 'send_mail_logs_from_client',
            'primaryKey'=>'send_mail_logs_from_client_id',
            'send_mail_date' => [
                'newColumnName' => 'send_at',
                'comment' => '送信日時',
            ],
        ],
        [
            'tableName' => 'send_mail_logs_from_user',
            'primaryKey'=>'send_mail_logs_from_user_id',
            'send_mail_date' => [
                'newColumnName' => 'send_at',
                'comment' => '送信日時',
            ],
        ],
        [
            'tableName' => 'send_mail_logs_scout',
            'primaryKey'=>'send_mail_logs_scout_id',
            'sendmail_datetime' => [
                'newColumnName' => 'send_at',
                'comment' => '送信日時',
            ],
        ],
        [
            'tableName' => 'send_mail_master',
            'primaryKey'=>'send_mail_master_id',
            'next_send_date' => [
                'newColumnName' => 'next_send_at',
                'comment' => '送信日時',
            ],
        ],
        [
            'tableName' => 'tenant',
            'primaryKey'=>'tenant_id',
            'regist_date' => [
                'newColumnName' => 'created_at',
                'comment' => '作成日時',
            ],
            'update_date' => [
                'newColumnName' => 'updated_at',
                'comment' => '更新日時',
            ],
        ],
        // 仕様未定のため保留
//        [
//            'tableName' => 'widget_data',
//            'disp_start_date' => [
//                'newColumnName' => '',
//                'comment' => '掲載開始日',
//            ],
//            'disp_end_date' => [
//                'newColumnName' => '',
//                'comment' => '掲載終了日',
//            ],
//        ],
    ];

    public function safeUp()
    {
        foreach (self::TABLES as $table) {
            $tableName = \yii\helpers\ArrayHelper::remove($table, 'tableName');
            $primaryKey = \yii\helpers\ArrayHelper::remove($table, 'primaryKey', 'id');
            foreach ($table as $oldColumnName => $newColumn) {
                $this->toUnixTime($tableName, $oldColumnName, $newColumn['newColumnName'], $newColumn['comment'], $primaryKey);
            }
        }
    }

    public function safeDown()
    {
        foreach (self::TABLES as $table) {
            $tableName = \yii\helpers\ArrayHelper::remove($table, 'tableName');
            $primaryKey = \yii\helpers\ArrayHelper::remove($table, 'primaryKey', 'id');
            foreach ($table as $oldColumnName => $newColumn) {
                $this->toDatetime($tableName, $oldColumnName, $newColumn['newColumnName'], $newColumn['comment'], $primaryKey);
            }
        }
    }

    private function toUnixTime($tableName, $oldColumnName, $newColumnName, $comment, $primaryKey)
    {
        if ($oldColumnName != $newColumnName) {
            $this->renameColumn($tableName, $oldColumnName, $newColumnName);
        }
        $dateTimes = \yii\helpers\ArrayHelper::map((new Query)->select([$primaryKey, $newColumnName])->from($tableName)->all(), $primaryKey, $newColumnName);
        $this->alterColumn($tableName, $newColumnName, $this->string());
        $this->update($tableName, [$newColumnName => 0]);
        $this->alterColumn($tableName, $newColumnName, $this->integer(11)->notNull() . ' COMMENT "' . $comment . '"');
        foreach ($dateTimes as $id => $dateTime) {
            $unixTime = strtotime($dateTime);
            if($unixTime < 0){
                $unixTime = 0;
            }
            $this->update($tableName, [$newColumnName => $unixTime], [$primaryKey => $id]);
        }
    }

    private function toDatetime($tableName, $oldColumnName, $newColumnName, $comment, $primaryKey)
    {
        if ($newColumnName != $oldColumnName) {
            $this->renameColumn($tableName, $newColumnName, $oldColumnName);
        }
        $dateTimes = \yii\helpers\ArrayHelper::map((new Query)->select([$primaryKey, $oldColumnName])->from($tableName)->all(), $primaryKey, $oldColumnName);
        $this->alterColumn($tableName, $oldColumnName, $this->string());
        $this->update($tableName, [$oldColumnName => '0000-00-00']);
        $this->alterColumn($tableName, $oldColumnName, $this->date()->notNull() . ' COMMENT "' . $comment . '"');
        foreach ($dateTimes as $id => $dateTime) {
            $this->update($tableName, [$oldColumnName => date('Y-m-d', $dateTime)], ['id' => $id]);
        }
    }
}
