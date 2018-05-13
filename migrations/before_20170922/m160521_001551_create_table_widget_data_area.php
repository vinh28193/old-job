<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_widget_data_area`.
 */
class m160521_001551_create_table_widget_data_area extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute('DROP TABLE IF EXISTS widget_data_area');
        $this->createTable('widget_data_area', [
            'id' => $this->primaryKey() . ' COMMENT"主キーID"',
            'tenant_id' => $this->integer(11) . ' COMMENT"テナントID"',
            'widget_data_id' => $this->integer(11) . ' COMMENT"widget_dataのID"',
            'area_id' => $this->integer(11) . ' COMMENT"areaのID"',
            'url' => $this->string(255) . ' COMMENT"URL"',
            'movie_tag' => $this->string(255) . ' COMMENT"動画タグ"',
        ]);
        $this->createIndex('idx_widget_data_area_1_tenant_id_2_widget_data_id_3_area_id', 'widget_data_area', ['tenant_id', 'widget_data_id', 'area_id']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('widget_data_area');
    }
}
