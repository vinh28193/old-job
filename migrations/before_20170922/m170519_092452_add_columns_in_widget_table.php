<?php

use yii\db\Migration;

class m170519_092452_add_columns_in_widget_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('widget', 'style_pc', $this->tinyInteger()->notNull()->defaultValue(1)->comment('PCでのwidget_data表示スタイル'));
        $this->addColumn('widget', 'style_sp', $this->tinyInteger()->notNull()->defaultValue(1)->comment('SPでのwidget_data表示スタイル'));
        $this->addColumn('widget', 'data_per_line_pc', $this->tinyInteger()->notNull()->defaultValue(1)->comment('PCでの一行あたりのwidget_data表示件数'));
        $this->addColumn('widget', 'data_per_line_sp', $this->tinyInteger()->notNull()->defaultValue(1)->comment('SPでの一行あたりのwidget_data表示件数'));
    }

    public function safeDown()
    {
        $this->dropColumn('widget', 'style_pc');
        $this->dropColumn('widget', 'style_sp');
        $this->dropColumn('widget', 'data_per_line_pc');
        $this->dropColumn('widget', 'data_per_line_sp');
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
