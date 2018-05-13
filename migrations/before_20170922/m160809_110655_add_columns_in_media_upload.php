<?php

use yii\db\Migration;
use app\models\manage\MediaUpload;
use yii\helpers\ArrayHelper;

/**
 * Class m160809_110655_add_columns_in_media_upload
 * media_uploadテーブルに、代理店ID・表示用ファイル名を追加し、元の"ファイル名"を保存用のファイル名（実ファイル名）
 * としてリネームする
 * 追記：本番サイトと、開発環境・ローカルで差分があったため、カラムがあっても通るような形に修正。
 */
class m160809_110655_add_columns_in_media_upload extends Migration
{
    public function safeUp()
    {
        if (ArrayHelper::isIn('created_at', MediaUpload::getTableSchema()->columnNames)) {
            $this->dropColumn('media_upload', 'created_at');
        }
        $this->addColumn('media_upload', 'created_at', $this->integer(11)->notNull() . ' COMMENT "登録日時"');

        if (ArrayHelper::isIn('corp_master_id', MediaUpload::getTableSchema()->columnNames)) {
            $this->dropColumn('media_upload', 'corp_master_id');
        }
        $this->addColumn('media_upload', 'corp_master_id', $this->integer(11) . ' COMMENT "corp_masterのカラムid"');

        if (ArrayHelper::isIn('disp_file_name', MediaUpload::getTableSchema()->columnNames)) {
            $this->dropColumn('media_upload', 'disp_file_name');
        }
        $this->addColumn('media_upload', 'disp_file_name', $this->string(30)->notNull() . ' COMMENT "表示用ファイル名"');

        if (ArrayHelper::isIn('file_name', MediaUpload::getTableSchema()->columnNames)) {
            $this->renameColumn('media_upload', 'file_name', 'save_file_name');
        }

        if (ArrayHelper::isIn('save_file_name', MediaUpload::getTableSchema()->columnNames)) {
            $this->dropColumn('media_upload', 'save_file_name');
        }
        $this->addColumn('media_upload', 'save_file_name', $this->string(30)->notNull() . ' COMMENT "保存用ファイル名"');
    }

    public function safeDown()
    {
        $this->alterColumn('media_upload', 'save_file_name', $this->string(255) . ' COMMENT "ファイル名"');
        $this->renameColumn('media_upload', 'save_file_name', 'file_name');
        $this->dropColumn('media_upload', 'disp_file_name');
        $this->dropColumn('media_upload', 'corp_master_id');
        $this->dropColumn('media_upload', 'created_at');
    }
}
