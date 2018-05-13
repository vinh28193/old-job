<?php

use yii\db\Migration;

/**
 * Class m171122_115201_add_column_main_visual_image_tenant_id
 */
class m171122_115201_add_column_main_visual_image_tenant_id extends Migration
{
    /**
     * Up
     */
    public function safeUp()
    {
        $this->addColumn(
            'main_visual_image',
            'tenant_id',
            $this->integer(11)->unsigned()->notNull()->comment('テナントID')->after('id')
        );
        $this->createIndex('idx-main_visual_image-tenant_id', 'main_visual_image', 'tenant_id');
    }

    /**
     * Down
     */
    public function safeDown()
    {
        $this->dropColumn(
            'main_visual_image',
            'tenant_id'
        );
    }
}
