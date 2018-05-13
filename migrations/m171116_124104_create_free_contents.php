<?php

use yii\db\Migration;

/**
 * Class m171116_124104_create_free_contents
 */
class m171116_124104_create_free_contents extends Migration
{
    const TABLE_NAME = 'free_content';

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
            'tenant_id' => $this->integer(11)->notNull()->comment('テナントID'),
            'title' => $this->string(255)->comment('ページタイトル'),
            'keyword' => $this->string(255)->comment('SEO対策用keyword'),
            'description' => $this->string(255)->comment('SEO対策用description'),
            'url_directory' => $this->string(30)->comment('コンテンツURL'),
            'valid_chk' => $this->boolean()->notNull()->comment('有効フラグ'),
            'created_at' => $this->integer(11)->unsigned()->notNull()->comment('作成日時'),
            'updated_at' => $this->integer(11)->unsigned()->notNull()->comment('更新日時'),
        ], $tableOptions);
        $this->createIndex('idx-tenant_id-valid_chk', self::TABLE_NAME, ['tenant_id', 'valid_chk']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
