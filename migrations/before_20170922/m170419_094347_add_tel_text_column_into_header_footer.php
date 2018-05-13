<?php

use yii\db\Migration;

class m170419_094347_add_tel_text_column_into_header_footer extends Migration
{
    public function safeUp()
    {
        $this->addColumn('header_footer', 'tel_text', $this->string(50)->defaultValue('')->comment('電話番号テキスト'));
    }

    public function safeDown()
    {
        $this->dropColumn('header_footer', 'tel_text');
    }
}
