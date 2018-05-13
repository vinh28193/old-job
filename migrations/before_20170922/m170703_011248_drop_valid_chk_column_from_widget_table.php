<?php

use yii\db\Migration;

/**
 * Handles dropping valid_chk from table `widget`.
 */
class m170703_011248_drop_valid_chk_column_from_widget_table extends Migration
{
    public function safeup()
    {
        $this->dropColumn('widget', 'valid_chk');
    }

    public function safeDown()
    {
        $this->addColumn('widget', 'valid_chk', $this->tinyInteger(1)->defaultValue(1)->comment('状態')->after('element3'));
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
