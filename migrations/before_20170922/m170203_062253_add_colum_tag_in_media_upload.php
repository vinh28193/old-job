<?php

use app\models\manage\MediaUpload;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

class m170203_062253_add_colum_tag_in_media_upload extends Migration
{
    public function safeUp()
    {
        if (ArrayHelper::isIn('tag', MediaUpload::getTableSchema()->columnNames)) {
            $this->safeDown();
        }
        $this->addColumn(MediaUpload::tableName(), 'tag', $this->string(50) . ' COMMENT "画像検索用タグ"');
        $this->createIndex('idx_media_upload_tag', MediaUpload::tableName(), ['tag']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_media_upload_tag', MediaUpload::tableName());
        $this->dropColumn(MediaUpload::tableName(), 'tag');
    }
}
