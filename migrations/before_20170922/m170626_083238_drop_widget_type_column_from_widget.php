<?php

use yii\db\Migration;

class m170626_083238_drop_widget_type_column_from_widget extends Migration
{
    public function up()
    {
        $this->dropColumn('widget', 'widget_type');
    }

    public function down()
    {
        $this->addColumn('widget', 'widget_type', $this->tinyInteger(1)->defaultValue(1)->comment('ウィジェットタイプ(0=URL, 1=動画)')->after('widget_name'));
    }

    /**
     * Creates a tinyint column.
     * @param $length integer
     */
    public function tinyInteger($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('tinyint', $length);
    }
}
