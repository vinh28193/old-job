<?php

use yii\db\Migration;

/**
 * Class m160810_134147_alter_and_add_columns_in_job_master_backup
 * migrationファイル[m160721_054928_alter_and_add_columns_in_job_master]と
 * 同様のmigrationの内容を、[job_master_backup]テーブルに適用する
 */
class m160810_134147_alter_and_add_columns_in_job_master_backup extends Migration
{
    public function safeUp()
    {
        $this->addColumn('job_master_backup', 'media_upload_id_1', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->addColumn('job_master_backup', 'media_upload_id_2', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->addColumn('job_master_backup', 'media_upload_id_3', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->addColumn('job_master_backup', 'media_upload_id_4', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid"');
        $this->addColumn('job_master_backup', 'media_upload_id_5', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid"');

        $this->dropColumn('job_master_backup', 'job_pict_0');
        $this->dropColumn('job_master_backup', 'job_pict_1');
        $this->dropColumn('job_master_backup', 'job_pict_2');
        $this->dropColumn('job_master_backup', 'job_pict_3');
        $this->dropColumn('job_master_backup', 'job_pict_4');

        $this->renameColumn('job_master_backup', 'job_pict_text_4', 'job_pict_text_5');
        $this->renameColumn('job_master_backup', 'job_pict_text_3', 'job_pict_text_4');
        $this->renameColumn('job_master_backup', 'job_pict_text_2', 'job_pict_text_3');
    }

    public function safeDown()
    {
        $this->addColumn('job_master_backup', 'job_pict_0', $this->string(255) . ' COMMENT \'画像０（Aタイプ）\'');
        $this->addColumn('job_master_backup', 'job_pict_1', $this->string(255) . ' COMMENT \'画像１（Bタイプ）\'');
        $this->addColumn('job_master_backup', 'job_pict_2', $this->string(255) . ' COMMENT \'画像２（Cタイプ）\'');
        $this->addColumn('job_master_backup', 'job_pict_3', $this->string(255) . ' COMMENT \'画像３（Cタイプ）\'');
        $this->addColumn('job_master_backup', 'job_pict_4', $this->string(255) . ' COMMENT \'画像４（Cタイプ）\'');

        $this->dropColumn('job_master_backup', 'media_upload_id_1');
        $this->dropColumn('job_master_backup', 'media_upload_id_2');
        $this->dropColumn('job_master_backup', 'media_upload_id_3');
        $this->dropColumn('job_master_backup', 'media_upload_id_4');
        $this->dropColumn('job_master_backup', 'media_upload_id_5');

        $this->renameColumn('job_master_backup', 'job_pict_text_3', 'job_pict_text_2');
        $this->renameColumn('job_master_backup', 'job_pict_text_4', 'job_pict_text_3');
        $this->renameColumn('job_master_backup', 'job_pict_text_5', 'job_pict_text_4');
    }
}
