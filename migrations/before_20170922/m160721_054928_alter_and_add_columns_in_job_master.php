<?php

use yii\db\Migration;

/**
 * Class m160721_054928_alter_and_add_columns_in_job_master
 * job_masterテーブルの原稿画像ファイル名を保存するカラムを削除し、media_uploadテーブルと連携するカラムを追加
 * job_masterの画像テキストのカラム名を微修正
 */
class m160721_054928_alter_and_add_columns_in_job_master extends Migration
{
    public function safeUp()
    {
        // media_uploadテーブルと連携するカラムを追加
        $this->addColumn('job_master', 'media_upload_id_1', $this->integer(11)->notNull() . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->addColumn('job_master', 'media_upload_id_2', $this->integer(11)->notNull() . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->addColumn('job_master', 'media_upload_id_3', $this->integer(11)->notNull() . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->addColumn('job_master', 'media_upload_id_4', $this->integer(11)->notNull() . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->addColumn('job_master', 'media_upload_id_5', $this->integer(11)->notNull() . ' COMMENT "テーブルmedia_uploadのカラムid"');

        // 既存のファイル名を保存するテーブルを削除
        $this->dropColumn('job_master', 'job_pict_0');
        $this->dropColumn('job_master', 'job_pict_1');
        $this->dropColumn('job_master', 'job_pict_2');
        $this->dropColumn('job_master', 'job_pict_3');
        $this->dropColumn('job_master', 'job_pict_4');

        // job_masterテーブルのjob_pict_text_○○カラムのカラム名を変更しておく
        $this->renameColumn('job_master', 'job_pict_text_4', 'job_pict_text_5');
        $this->renameColumn('job_master', 'job_pict_text_3', 'job_pict_text_4');
        $this->renameColumn('job_master', 'job_pict_text_2', 'job_pict_text_3');
    }

    public function safeDown()
    {
        $this->addColumn('job_master', 'job_pict_0', $this->string(255) . ' COMMENT \'画像０（Aタイプ）\'');
        $this->addColumn('job_master', 'job_pict_1', $this->string(255) . ' COMMENT \'画像１（Bタイプ）\'');
        $this->addColumn('job_master', 'job_pict_2', $this->string(255) . ' COMMENT \'画像２（Cタイプ）\'');
        $this->addColumn('job_master', 'job_pict_3', $this->string(255) . ' COMMENT \'画像３（Cタイプ）\'');
        $this->addColumn('job_master', 'job_pict_4', $this->string(255) . ' COMMENT \'画像４（Cタイプ）\'');

        $this->dropColumn('job_master', 'media_upload_id_1');
        $this->dropColumn('job_master', 'media_upload_id_2');
        $this->dropColumn('job_master', 'media_upload_id_3');
        $this->dropColumn('job_master', 'media_upload_id_4');
        $this->dropColumn('job_master', 'media_upload_id_5');

        $this->renameColumn('job_master', 'job_pict_text_3', 'job_pict_text_2');
        $this->renameColumn('job_master', 'job_pict_text_4', 'job_pict_text_3');
        $this->renameColumn('job_master', 'job_pict_text_5', 'job_pict_text_4');
    }
}