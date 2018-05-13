<?php

use app\models\manage\MainVisualImage;
use yii\db\Migration;

/**
 * Class m171126_062143_alter_column_main_visual_image_content
 */
class m171126_062143_alter_column_main_visual_image_content extends Migration
{
    /**
     * Up
     */
    public function safeUp()
    {
        $this->alterColumn(
            MainVisualImage::tableName(),
            'content',
            $this->string(64)->comment('ALTテキスト')
        );
    }

    /**
     * Down
     */
    public function safeDown()
    {
        $this->alterColumn(
            MainVisualImage::tableName(),
            'content',
            $this->string(64)->notNull()->comment('ALTテキスト')
        );
    }
}
