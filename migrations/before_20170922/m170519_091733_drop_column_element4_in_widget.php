<?php

use yii\db\Migration;

class m170519_091733_drop_column_element4_in_widget extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('widget', 'element4');
    }

    public function safeDown()
    {
        $this->addColumn('widget', 'element4', $this->tinyInteger()->defaultValue(null)->comment('コンテンツ内で4番目に表示させる要素')->after('element3'));
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
