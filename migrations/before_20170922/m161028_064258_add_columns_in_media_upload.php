<?php

use yii\db\Migration;
use yii\helpers\ArrayHelper;
use app\models\manage\MediaUpload;

/**
 * Class m161028_064258_add_columns_in_media_upload
 * media_uploadテーブルに、掲載企業IDとファイルサイズのカラムを追加
 * 過去、m160721_053343_add_columns_in_media_upload というmigrationで同じ処理をしていたが、削除されてしまっているので追加した。
 * m160721_053343_add_columns_in_media_upload のmigration未実行の場合のみ、更新処理されるようにしている
 */
class m161028_064258_add_columns_in_media_upload extends Migration
{
    public function safeUp()
    {
        if (!ArrayHelper::isIn('client_master_id', MediaUpload::getTableSchema()->columnNames)) {
            $this->addColumn('media_upload', 'client_master_id', $this->integer(11) . ' COMMENT "テーブルclient_masterのカラムid"');
            $this->createIndex('idx_media_upload_client_master_id', 'media_upload', ['client_master_id']);
        }

        if (!ArrayHelper::isIn('file_size', MediaUpload::getTableSchema()->columnNames)) {
            $this->addColumn('media_upload', 'file_size', $this->integer(11) . ' COMMENT "ファイルサイズ(Byte)"');
        }
    }

    public function safeDown()
    {
        $this->dropColumn('media_upload', 'client_master_id');
        $this->dropColumn('media_upload', 'file_size');
    }
}
