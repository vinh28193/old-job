<?php

use yii\db\Migration;
use app\models\manage\JobMaster;
use yii\helpers\ArrayHelper;

/**
 * Class m161028_062046_change_column_of_job_pics
 * media_uploadテーブルの原稿画像ファイル名を保存するカラムを削除し、media_uploadテーブルと連携するカラムを追加
 * 過去、m160721_054928_change_column_of_job_pics というmigrationで同じ処理をしていたが、削除されてしまっているので追加した。
 * m160721_054928_change_column_of_job_pics のmigration未実行の場合のみ、更新処理されるようにしている
 */
class m161028_062046_change_column_of_job_pics extends Migration
{
    public function safeUp()
    {
        // media_uploadテーブルと連携するカラムを追加
        $this->existAddColumn('job_master', 'media_upload_id_1', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid（画像1）"');
        $this->existAddColumn('job_master', 'media_upload_id_2', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid（画像2）"');
        $this->existAddColumn('job_master', 'media_upload_id_3', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid（画像3）"');
        $this->existAddColumn('job_master', 'media_upload_id_4', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid（画像4）"');
        $this->existAddColumn('job_master', 'media_upload_id_5', $this->integer(11) . ' COMMENT "テーブルmedia_uploadのカラムid（画像5）"');

        // 既存のファイル名を保存するカラムを削除
        $this->existDropColumn('job_master', 'job_pict_0');
        $this->existDropColumn('job_master', 'job_pict_1');
        $this->existDropColumn('job_master', 'job_pict_2');
        $this->existDropColumn('job_master', 'job_pict_3');
        $this->existDropColumn('job_master', 'job_pict_4');
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
    }

    /**
     * @param $table string
     * @param $column string
     * @param $type string
     * @throws \yii\base\InvalidConfigException
     */
    private function existAddColumn($table, $column, $type)
    {
        if (!ArrayHelper::isIn($column, JobMaster::getTableSchema()->columnNames)) {
            $this->addColumn($table, $column, $type);
        }
    }

    /**
     * @param $table string
     * @param $column string
     * @throws \yii\base\InvalidConfigException
     */
    private function existDropColumn($table, $column)
    {
        if (ArrayHelper::isIn($column, JobMaster::getTableSchema()->columnNames)) {
            $this->dropColumn($table, $column);
        }
    }
}
