<?php

use yii\db\Migration;

/**
 * Class m171116_124231_create_free_contents_element
 */
class m171116_124231_create_free_contents_element extends Migration
{
    const TABLE_NAME = 'free_content_element';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }

        // 文言変換パターン保存テーブル
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned()->notNull()->comment('主キーID'),
            'tenant_id' => $this->integer(11)->unsigned()->notNull()->comment('テナントID'),
            'free_content_id' => $this->integer(11)->unsigned()->notNull()->comment('free_contentテーブルのID'),
            'type' => $this->tinyInteger(4)->unsigned()->comment('要素タイプ'),
            'image_file_name' => $this->string(255)->comment('画像ファイル名'),
            'text' => $this->text()->comment('テキスト'),
            'sort' => $this->tinyInteger(4)->unsigned()->notNull()->comment('並び順'),
            'created_at' => $this->integer(11)->unsigned()->notNull()->comment('作成日時'),
            'updated_at' => $this->integer(11)->unsigned()->notNull()->comment('更新日時'),
        ], $tableOptions);
        $this->createIndex('idx-tenant_id-free_content_id-sort', self::TABLE_NAME, ['tenant_id', 'free_content_id', 'sort']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }

    /**
     * @param null $length
     * @return \yii\db\ColumnSchemaBuilder
     */
    public function tinyInteger($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('tinyint', $length);
    }
}
