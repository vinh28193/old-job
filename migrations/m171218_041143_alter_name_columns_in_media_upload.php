<?php

use yii\db\Migration;

class m171218_041143_alter_name_columns_in_media_upload extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('media_upload', 'save_file_name', $this->string(200)->notNull()->comment('実ファイル名'));
        $this->alterColumn('media_upload', 'disp_file_name', $this->string(200)->notNull()->comment('表示用ファイル名'));
    }

    public function safeDown()
    {
        $this->alterColumn('media_upload', 'save_file_name', $this->string(30)->notNull()->comment('保存用ファイル名'));
        $this->alterColumn('media_upload', 'disp_file_name', $this->string(30)->notNull()->comment('表示用ファイル名'));
    }
}
