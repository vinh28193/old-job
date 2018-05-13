<?php

use yii\db\Migration;

/**
 * Handles the creation of table `site_html`.
 */
class m170303_060756_create_site_html_table extends Migration
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
        $this->createTable('site_html', [
            'id' => $this->primaryKey()->comment('主キーID'),
            'tenant_id' => $this->integer(11)->notNull()->comment('テナントID'),
            'header_html' => $this->text()->comment('ヘッダーHTML'),
            'footer_html' => $this->text()->comment('フッターHTML'),
            'updated_at' => $this->integer(11)->notNull()->comment('更新日時'),
        ], $tableOptions);
        $this->createIndex('idx_site_html_tenant_id', 'site_html', 'tenant_id');
        $this->execute('ALTER TABLE `site_html` ROW_FORMAT=DYNAMIC;');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('site_html');
    }
}
