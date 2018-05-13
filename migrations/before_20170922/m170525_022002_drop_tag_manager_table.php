<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `tag_manager`.
 */
class m170525_022002_drop_tag_manager_table extends Migration
{

    /** 変更対象のテーブル名 */
    const TABLE = 'tag_manager';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropTable(self::TABLE);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey()->comment('主キーID'),
            'tenant_id' => $this->integer(11)->notNull()->comment('テナントID'),
            'number' => $this->integer(11)->notNull()->comment('タグNo'),
            'name' => $this->string(50)->notNull()->comment('タグ種別'),
            'html' => $this->text()->comment('タグ'),
            'updated_at' => $this->integer(11)->notNull()->comment('更新日時'),
        ], $tableOptions);
        $this->execute('ALTER TABLE `' . self::TABLE . '` ROW_FORMAT=DYNAMIC;');
    }
}
