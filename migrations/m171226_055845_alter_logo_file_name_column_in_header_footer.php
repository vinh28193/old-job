<?php

use yii\db\Migration;

class m171226_055845_alter_logo_file_name_column_in_header_footer extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('header_footer', 'logo_file_name', $this->string(200)->notNull()->comment('ロゴ画像'));
    }

    public function safeDown()
    {
        $this->alterColumn('header_footer', 'logo_file_name', $this->string(30)->notNull()->comment('ロゴ画像'));
    }
}
