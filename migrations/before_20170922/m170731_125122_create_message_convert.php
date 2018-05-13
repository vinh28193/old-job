<?php

use yii\db\Migration;

/**
 * Class m170731_125122_create_tenant_message
 */
class m170731_125122_create_message_convert extends Migration
{
    const TABLE_NAME = 'message_convert';

    /**
     * up
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }

        // 文言変換パターン保存テーブル
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned()->notNull()->comment('主キー'),
            'tenant_id' => $this->integer(11)->notNull()->comment('テナントID'),
            'content' => $this->text()->comment('変換パターンJSON'),
            'is_active' => $this->boolean()->defaultValue(true)->comment('有効フラグ'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('作成日時'),
            'updated_at' => $this->integer()->unsigned()->comment('更新日時'),
        ], $tableOptions);
        $this->addForeignKey(
            self::TABLE_NAME . '-tenant_id-tenant-tenant_id',
            self::TABLE_NAME,
            'tenant_id',
            'tenant',
            'tenant_id'
        );
    }

    /**
     * down
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
