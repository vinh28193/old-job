<?php

use yii\db\Migration;

/**
 * Class m160803_050318_fix_columns_in_job_master
 * job_masterテーブルの原稿画像で使うmedia_uploadテーブルと連携するカラムを修正
 */
class m160803_050318_fix_columns_in_job_master extends Migration
{
    public function safeUp()
    {
        // media_uploadテーブルと連携するカラムを追加
        $this->alterColumn('job_master', 'media_upload_id_1', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->alterColumn('job_master', 'media_upload_id_2', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->alterColumn('job_master', 'media_upload_id_3', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->alterColumn('job_master', 'media_upload_id_4', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->alterColumn('job_master', 'media_upload_id_5', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid"');
    }

    public function safeDown()
    {
        $this->alterColumn('job_master', 'media_upload_id_1', $this->integer(11)->notNull() . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->alterColumn('job_master', 'media_upload_id_2', $this->integer(11)->notNull() . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->alterColumn('job_master', 'media_upload_id_3', $this->integer(11)->notNull() . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->alterColumn('job_master', 'media_upload_id_4', $this->integer(11)->notNull() . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->alterColumn('job_master', 'media_upload_id_5', $this->integer(11)->notNull() . ' COMMENT "テーブルmedia_uploadのカラムid"');
    }
}
