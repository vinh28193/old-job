<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `columns_in_widget_data`.
 */
class m160521_052731_drop_columns_in_widget_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn('widget_data', 'area_id');
        $this->dropColumn('widget_data', 'movie_tag');
        $this->dropColumn('widget_data', 'url');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn('widget_data', 'area_id', $this->integer(11) . ' COMMENT "areaのid" AFTER widget_id');
        $this->addColumn('widget_data', 'movie_tag', $this->string(255) . ' COMMENT "areaのid" AFTER description');
        $this->addColumn('widget_data', 'url', $this->string(255)  . ' COMMENT "areaのid" AFTER movie_tag');
    }
}
