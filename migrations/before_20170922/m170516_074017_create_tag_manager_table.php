<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tag_manager`.
 */
class m170516_074017_create_tag_manager_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $table = 'tag_manager';
        $this->createTable($table, [
            'id' => $this->primaryKey()->comment('主キーID'),
            'tenant_id' => $this->integer(11)->notNull()->comment('テナントID'),
            'number' => $this->integer(11)->notNull()->comment('タグNo'),
            'name' => $this->string(50)->notNull()->comment('タグ種別'),
            'html' => $this->text()->comment('タグ'),
            'updated_at' => $this->integer(11)->notNull()->comment('更新日時'),
        ], $tableOptions);
        $this->execute('ALTER TABLE `' . $table . '` ROW_FORMAT=DYNAMIC;');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tag_manager');
    }
}
